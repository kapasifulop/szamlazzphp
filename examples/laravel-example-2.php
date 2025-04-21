<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Szamlazzphp\Facades\SzamlazzHU;
use Szamlazzphp\Invoice;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;

/**
 * Példa a SzamlazzHU Facade használatára Laravelben
 * 
 * Ez a példa bemutatja, hogyan lehet a SzamlazzHU Facade-t használni
 * számlázási műveletekhez egy Laravel alkalmazásban.
 */
class InvoicingController extends Controller
{
    /**
     * Számla kiállítása a SzamlazzHU Facade segítségével
     */
    public function issueInvoice(Request $request)
    {
        // Vevő adatok létrehozása a kérésből
        $buyer = new Buyer([
            'name' => $request->input('buyer_name'),
            'country' => $request->input('buyer_country', 'Magyarország'),
            'zip' => $request->input('buyer_zip'),
            'city' => $request->input('buyer_city'),
            'address' => $request->input('buyer_address'),
            'email' => $request->input('buyer_email'),
            'taxNumber' => $request->input('buyer_tax_number'),
        ]);

        // Tétel létrehozása
        $item = new Item([
            'label' => $request->input('item_name'),
            'quantity' => $request->input('item_quantity', 1),
            'unit' => $request->input('item_unit', 'db'),
            'vat' => $request->input('item_vat', 27),
            'netUnitPrice' => $request->input('item_price'),
            'comment' => $request->input('item_comment'),
        ]);

        // Számla létrehozása InvoiceBuilder segítségével
        $invoice = (new InvoiceBuilder($buyer))
            ->addItem($item)
            ->setPaymentMethod(PaymentMethod::BankTransfer)
            ->setCurrency(Currency::Ft)
            ->setLanguage(Language::Hungarian)
            ->setOrderNumber($request->input('order_number'))
            ->setComment($request->input('invoice_comment'))
            ->build();

        try {
            // Számla kiállítása a SzamlazzHU Facade használatával
            $result = SzamlazzHU::issueInvoice($invoice);

            // Sikeres számla kiállítás
            return response()->json([
                'success' => true,
                'message' => 'Számla sikeresen kiállítva',
                'invoice_id' => $result['invoiceId'] ?? null,
                'net_total' => $result['netTotal'] ?? null,
                'vat_total' => $result['vatTotal'] ?? null,
                'gross_total' => $result['grossTotal'] ?? null,
            ]);

        } catch (\Exception $e) {
            // Hiba esetén
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a számla kiállítása során',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Számla adatok lekérdezése a SzamlazzHU Facade segítségével
     */
    public function getInvoice(Request $request)
    {
        try {
            // Számla adatok lekérdezése azonosító alapján
            $invoiceData = SzamlazzHU::getInvoiceData(
                $request->input('invoice_id'),
                null,
                $request->boolean('include_pdf', false)
            );

            return response()->json([
                'success' => true,
                'invoice_data' => $invoiceData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a számla adatok lekérdezése során',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Számla sztornózása a SzamlazzHU Facade segítségével
     */
    public function reverseInvoice(Request $request)
    {
        try {
            // Számla sztornózása
            $result = SzamlazzHU::reverseInvoice(
                $request->input('invoice_id'),
                $request->boolean('e_invoice', true),
                $request->boolean('request_download', true)
            );

            return response()->json([
                'success' => true,
                'message' => 'Számla sikeresen sztornózva',
                'reverse_invoice_id' => $result['invoiceId'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a számla sztornózása során',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PDF letöltés beállítása a SzamlazzHU Facade segítségével
     */
    public function configurePdfDownload(Request $request)
    {
        try {
            // PDF letöltési beállítás módosítása
            SzamlazzHU::setRequestInvoiceDownload($request->boolean('download_pdf', true));

            return response()->json([
                'success' => true,
                'message' => 'PDF letöltési beállítás módosítva'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a beállítás módosítása során',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

/**
 * Példa a routes/web.php vagy routes/api.php fájlban a kontrollerhez tartozó útvonalakra:
 * 
 * // Számla kiállítása
 * Route::post('/invoices', [InvoicingController::class, 'issueInvoice'])->name('invoices.issue');
 * 
 * // Számla adatok lekérdezése
 * Route::get('/invoices/{invoice_id}', [InvoicingController::class, 'getInvoice'])->name('invoices.get');
 * 
 * // Számla sztornózása
 * Route::post('/invoices/{invoice_id}/reverse', [InvoicingController::class, 'reverseInvoice'])->name('invoices.reverse');
 * 
 * // PDF letöltés beállítása
 * Route::put('/invoices/settings/pdf-download', [InvoicingController::class, 'configurePdfDownload'])->name('invoices.settings.pdf');
 */ 