<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\Buyer;
use Szamlazzphp\Item;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Enum\Language;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;

/**
 * Alapvető számla kiállítási példa
 * 
 * Ez a példa bemutatja, hogyan állíthatunk ki egy egyszerű számlát
 * a Számlázz.hu rendszerében.
 * 
 * A példa a SzamlaAgentClient-et használja API kulcs alapú autentikációval.
 */

// Hiba megjelenítés engedélyezése fejlesztéshez
error_reporting(E_ALL);
ini_set('display_errors', '1');

// API kulcs és egyéb beállítások
$apiKey = 'az-on-szamlazz-hu-api-kulcsa';
$eInvoice = false;                  // E-számla generálása
$requestInvoiceDownload = true;     // Számla letöltése PDF-ben
$downloadedInvoiceCount = 1;        // Letöltendő példányszám
$responseVersion = 1;               // Válasz verzió (1: régi, 2: XML)
$timeout = 30;                      // Időtúllépés másodpercben

// Kliens létrehozása
$client = new SzamlaAgentClient(
    $apiKey,
    $eInvoice,
    $requestInvoiceDownload,
    $downloadedInvoiceCount,
    $responseVersion,
    $timeout
);

// Vevő létrehozása
$buyer = new Buyer([
    'name' => 'Teszt Vevő',
    'zip' => '1234',
    'city' => 'Budapest',
    'address' => 'Teszt utca 1.',
    'email' => 'vevo@example.com',
    // Adószám opcionális, de céges vevőnél adjuk meg:
    'taxNumber' => '12345678-1-42',
]);

// Tétel létrehozása
$item = new Item([
    'label' => 'Teszt termék',
    'quantity' => 1,
    'unit' => 'db',
    'vat' => 27, // 27% ÁFA
    'netUnitPrice' => 10000, // nettó egységár
    'comment' => 'Teszt termék leírása',
]);

try {
    // Számla létrehozása InvoiceBuilder segítségével
    $invoice = (new InvoiceBuilder($buyer))
        ->addItem($item)
        ->setPaymentMethod(PaymentMethod::BankTransfer)
        ->setCurrency(Currency::Ft)
        ->setLanguage(Language::Hungarian)
        ->setComment("Teszt számla")
        ->setOrderNumber("REND-2023-001")
        ->build();

    // Számla kiállítása
    $response = $client->issueInvoice($invoice);
    
    if ($response->isSuccess()) {
        echo "A számla sikeresen kiállítva!" . PHP_EOL;
        echo "Számla száma: " . $response->getInvoiceId() . PHP_EOL;
        echo "Nettó összeg: " . $response->getNetTotal() . PHP_EOL;
        echo "Bruttó összeg: " . $response->getGrossTotal() . PHP_EOL;
        
        // Vevői fiók URL megjelenítése, ha elérhető
        if ($response->getCustomerAccountUrl()) {
            echo "Vevői fiók URL: " . $response->getCustomerAccountUrl() . PHP_EOL;
        }
        
        // PDF mentése, ha kértük a letöltést
        if ($requestInvoiceDownload && $response->getPdf()) {
            $pdfFile = 'szamla_' . str_replace('/', '_', $response->getInvoiceId()) . '.pdf';
            $response->savePdf($pdfFile);
            echo "Számla PDF elmentve: {$pdfFile}" . PHP_EOL;
        }
    } else {
        echo "Hiba történt a számla kiállítása közben: " . $response->getErrorMessage() . PHP_EOL;
        if ($response->getErrorCode()) {
            echo "Hibakód: " . $response->getErrorCode() . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "Kivétel történt: " . $e->getMessage() . PHP_EOL;
} 