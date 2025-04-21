<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;
use Szamlazzphp\Enum\TaxSubject;

// API kulcs alapú kliens létrehozása
$client = new SzamlaAgentClient(
    'az_ön_api_kulcsa', // Ide a valódi API kulcs kerül
    true,  // eInvoice: elektronikus számla
    true   // requestInvoiceDownload: kérje a számla PDF letöltését
);

// EU-s vevő létrehozása
$buyer = new Buyer([
    'name' => 'Example GmbH',
    'country' => 'Deutschland', // Németország
    'zip' => '10115',
    'city' => 'Berlin',
    'address' => 'Beispielstraße 123',
    'email' => 'contact@example.de',
    'taxNumberEU' => 'DE123456789', // Német adószám
    'taxSubject' => TaxSubject::EUCompany, // EU-s adóalany
]);

// Tétel létrehozása 0%-os ÁFÁ-val (EU-n belüli értékesítés)
$item = new Item([
    'label' => 'Software Development Services',
    'quantity' => 20,
    'unit' => 'hour',
    'vat' => 'EU', // EU-s ÁFA mentesség (vagy 0% is megadható)
    'netUnitPrice' => 60, // nettó egységár EUR-ban
]);

// Számla létrehozása
$invoice = (new InvoiceBuilder($buyer))
    ->addItem($item)
    ->setPaymentMethod(PaymentMethod::BankTransfer)
    ->setCurrency(Currency::EUR) // EUR-ban állítjuk ki
    ->setLanguage(Language::English) // angol nyelvű számla
    ->setNoNavReport(true) // EU-s partner, nem kell NAV jelentés
    ->setOrderNumber('EU-2023-001')
    ->setComment('Thank you for your business!')
    // EUR alapú számla esetén szükséges MNB árfolyam
    ->setExchangeBank('MNB')
    ->setExchangeRate(380.0) // Az aktuális MNB árfolyam
    ->build();

try {
    // Számla kiállítása
    $result = $client->issueInvoice($invoice);
    
    // A számla adatainak kiírása
    echo "Invoice successfully issued!" . PHP_EOL;
    echo "Invoice ID: " . $result['invoiceId'] . PHP_EOL;
    echo "Net total: " . $result['netTotal'] . PHP_EOL;
    echo "Gross total: " . $result['grossTotal'] . PHP_EOL;
    
    // Ha kértük a számla letöltését, mentsük el a PDF-et
    if (isset($result['pdf'])) {
        $pdfPath = __DIR__ . '/' . $result['invoiceId'] . '.pdf';
        file_put_contents($pdfPath, $result['pdf']);
        echo "The invoice PDF saved to: " . $pdfPath . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error while issuing the invoice: " . $e->getMessage() . PHP_EOL;
} 