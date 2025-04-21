<?php

/**
 * Ez a példa bemutatja a Számlázz.hu PHP csomag használatát Laravel környezetben.
 * 
 * Ez a fájl nem futtatható közvetlenül, csak szemléltető jellegű.
 * A valós használathoz helyezd el a megfelelő kódot a Laravel alkalmazásodban.
 * 
 * Megjegyzés: Ez a fájl kizárólag demonstrációs célokat szolgál, a Linter hibákat figyelmen kívül hagyjuk,
 * mivel ez nem egy valós Laravel alkalmazáson belül fut.
 */

namespace App\Http\Controllers;

// Laravel alaposztályok és Facade-ek
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

// Számlázz.hu könyvtár osztályok
use Szamlazzphp\Client\ClientInterface;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;
use Exception;

/**
 * A ServiceProvider használatának bemutatása
 * 
 * A package telepítése után a SzamlazzphpServiceProvider automatikusan regisztrál
 * egy ClientInterface példányt, amelyet közvetlenül injektálhatsz a kontrollerbe.
 * 
 * A .env fájlban állítsd be:
 * 
 * SZAMLAZZHU_AUTH_TYPE=api_key      # vagy "auth" felhasználónév/jelszó esetén
 * SZAMLAZZHU_TOKEN=az_api_kulcsod   # API kulcs esetén
 * 
 * # VAGY
 * 
 * SZAMLAZZHU_AUTH_TYPE=auth
 * SZAMLAZZHU_USER=felhasznaloneved
 * SZAMLAZZHU_PASSWORD=jelszavad
 * 
 * # Továbbá
 * SZAMLAZZHU_E_INVOICE=false
 * SZAMLAZZHU_DOWNLOAD=true
 */
class InvoiceController extends Controller
{
    /**
     * A Számlázz.hu kliens
     * 
     * @var ClientInterface
     */
    protected $client;

    /**
     * Konstruktor
     * 
     * Az automatikus függőség-befecskendezéssel a Laravel
     * megkeresi a ClientInterface-t a Service Containerben,
     * amelyet a SzamlazzphpServiceProvider regisztrált.
     * 
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Számla kiállítása
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function issueInvoice(Request $request)
    {
        // Validáció
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'address' => 'required|string|max:100',
            'city' => 'required|string|max:50',
            'zip' => 'required|string|max:10',
            'amount' => 'required|numeric|min:1',
            'productName' => 'required|string|max:100',
        ]);
        
        try {
            // Vevő létrehozása a validált adatokból
            $buyer = new Buyer([
                'name' => $validated['name'],
                'country' => 'Magyarország',
                'zip' => $validated['zip'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'email' => $validated['email'],
            ]);
            
            // Számlatétel létrehozása
            $item = new Item([
                'label' => $validated['productName'],
                'quantity' => 1,
                'unit' => 'db',
                'vat' => 27, // 27% ÁFA
                'netUnitPrice' => round($validated['amount'] / 1.27), // Nettó egységár kiszámítása
            ]);
            
            // Számla létrehozása Builder pattern segítségével
            $invoice = (new InvoiceBuilder($buyer))
                ->addItem($item)
                ->setPaymentMethod(PaymentMethod::BankTransfer)
                ->setCurrency(Currency::Ft)
                ->setLanguage(Language::Hungarian)
                ->setOrderNumber($request->session()->get('order_id'))
                ->setPaid($request->boolean('paid'))
                ->build();
            
            // Számla kiállítása a konstruktorban injektált klienssel
            $result = $this->client->issueInvoice($invoice);
            
            // Ha van PDF a válaszban, mentsük el
            if (isset($result['pdf'])) {
                $invoiceId = $result['invoiceId'];
                Storage::put('invoices/' . $invoiceId . '.pdf', $result['pdf']);
                
                // Példa: e-mail küldése a PDF-fel
                // Mail::to($validated['email'])->send(new InvoiceCreated($invoiceId));
            }
            
            // Válasz küldése
            return Response::json([
                'success' => true,
                'invoice_id' => $result['invoiceId'] ?? null,
                'message' => 'A számla sikeresen elkészült',
            ]);
            
        } catch (Exception $e) {
            // Hiba esetén logolás és hibaüzenet küldése
            Log::error('Számla kiállítási hiba:', [
                'error' => $e->getMessage(),
                'user_data' => $validated,
            ]);
            
            return Response::json([
                'success' => false,
                'message' => 'Hiba történt a számla kiállítása közben: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Számla letöltése
     * 
     * Ez a példa a Service Container közvetlen használatát mutatja be.
     * 
     * @param string $invoiceId
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice($invoiceId)
    {
        $pdfPath = 'invoices/' . $invoiceId . '.pdf';
        
        if (Storage::exists($pdfPath)) {
            return Response::file(Storage::path($pdfPath), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $invoiceId . '.pdf"'
            ]);
        }
        
        // Ha nem találja a fájlt, próbáljuk meg újra lekérni a Számlázz.hu rendszeréből
        try {
            // Kliens lekérése a Service Containerből
            $client = $this->client;
            
            $invoiceData = $client->getInvoiceData($invoiceId, null, true);
            
            if (isset($invoiceData['pdf'])) {
                // Mentsük el a PDF-et
                $pdfContent = is_string($invoiceData['pdf']) ? base64_decode($invoiceData['pdf']) : $invoiceData['pdf'];
                Storage::put($pdfPath, $pdfContent);
                
                return Response::file(Storage::path($pdfPath), [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $invoiceId . '.pdf"'
                ]);
            }
            
            return Response::json(['error' => 'A számla nem található'], 404);
        } catch (Exception $e) {
            Log::error('Számla letöltési hiba:', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);
            
            return Response::json(['error' => 'Hiba történt a számla letöltése közben'], 500);
        }
    }
} 