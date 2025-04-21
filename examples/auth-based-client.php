<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\AuthBasedClient;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;

// Felhasználónév-jelszó alapú kliens létrehozása
$client = new AuthBasedClient(
    'felhasznalonev', // Ide a valódi felhasználónév kerül
    'jelszo',         // Ide a valódi jelszó kerül
    false,            // eInvoice: elektronikus számla
    true,             // requestInvoiceDownload: kérje a számla PDF letöltését
    1,                // downloadedInvoiceCount: letöltendő példányszám
    1,                // responseVersion: válasz verzió
    30                // timeout: időtúllépés másodpercben
);

// Vevő létrehozása
$buyer = new Buyer([
    'name' => 'Teszt Vevő',
    'country' => 'Magyarország',
    'zip' => '1234',
    'city' => 'Budapest',
    'address' => 'Példa utca 1.',
    'email' => 'vevo@example.com',
]);

// Tétel létrehozása
$item = new Item([
    'label' => 'Tanácsadás',
    'quantity' => 2,
    'unit' => 'óra',
    'vat' => 27, // ÁFA százalék
    'netUnitPrice' => 10000, // nettó egységár
]);

// Számla létrehozása a Builder pattern segítségével
$invoice = (new InvoiceBuilder($buyer))
    ->addItem($item)
    ->setPaymentMethod(PaymentMethod::Cash) // Készpénzes fizetés
    ->setCurrency(Currency::Ft)
    ->setLanguage(Language::Hungarian)
    ->setPaid(true) // Készpénzes fizetésnél általában rögtön fizetett
    ->setComment('Felhasználónév-jelszó alapú authentikáció tesztje')
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