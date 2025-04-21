<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;

// API kulcs alapú kliens létrehozása
$client = new SzamlaAgentClient(
    'az_ön_api_kulcsa', // Ide a valódi API kulcs kerül
    false, // eInvoice: elektronikus számla
    true,  // requestInvoiceDownload: kérje a számla PDF letöltését
    1,     // downloadedInvoiceCount: letöltendő példányszám
    1,     // responseVersion: válasz verzió
    0      // timeout: időtúllépés másodpercben (0 = nincs)
);

// Vevő létrehozása
$buyer = new Buyer([
    'name' => 'Teszt Vevő Kft.',
    'country' => 'Magyarország',
    'zip' => '1234',
    'city' => 'Budapest',
    'address' => 'Példa utca 1.',
    'email' => 'vevo@example.com',
    'taxNumber' => '12345678-1-42',
]);

// Tételek létrehozása
$item1 = new Item([
    'label' => 'Webfejlesztés',
    'quantity' => 1,
    'unit' => 'óra',
    'vat' => 27, // ÁFA százalék
    'netUnitPrice' => 10000, // nettó egységár
    'comment' => 'Frontend fejlesztés',
]);

$item2 = new Item([
    'label' => 'Szerver üzemeltetés',
    'quantity' => 5,
    'unit' => 'óra',
    'vat' => 27, // ÁFA százalék
    'netUnitPrice' => 8000, // nettó egységár
    'comment' => 'Linux szerverek karbantartása',
]);

// Számla létrehozása Builder pattern segítségével
$invoice = (new InvoiceBuilder($buyer))
    ->addItem($item1)
    ->addItem($item2)
    ->setPaymentMethod(PaymentMethod::BankTransfer)
    ->setCurrency(Currency::Ft)
    ->setLanguage(Language::Hungarian)
    ->setOrderNumber('ORD-2023-001')
    ->setComment('Köszönjük a megrendelést!')
    ->setPaid(true) // A számla már ki van egyenlítve
    ->build();

try {
    // Számla kiállítása
    $result = $client->issueInvoice($invoice);
    
    // A számla adatainak kiírása
    echo "Számla sikeresen kiállítva!" . PHP_EOL;
    echo "Számla azonosító: " . $result['invoiceId'] . PHP_EOL;
    echo "Nettó összeg: " . $result['netTotal'] . PHP_EOL;
    echo "Bruttó összeg: " . $result['grossTotal'] . PHP_EOL;
    
    // Ha kértük a számla letöltését, mentsük el a PDF-et
    if (isset($result['pdf'])) {
        $pdfPath = __DIR__ . '/' . $result['invoiceId'] . '.pdf';
        file_put_contents($pdfPath, $result['pdf']);
        echo "A számla PDF elmentve: " . $pdfPath . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Hiba történt a számla kiállítása során: " . $e->getMessage() . PHP_EOL;
} 