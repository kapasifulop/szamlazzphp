<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Seller;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;
use Szamlazzphp\Enum\TaxSubject;

// API kulcs alapú kliens létrehozása bővebb beállításokkal
$client = new SzamlaAgentClient(
    'az_ön_api_kulcsa',  // Ide a valódi API kulcs kerül
    true,                // eInvoice: elektronikus számla
    true,                // requestInvoiceDownload: kérje a számla PDF letöltését
    2,                   // downloadedInvoiceCount: 2 példány letöltése
    2,                   // responseVersion: válasz verzió 2
    30                   // timeout: 30 másodperc időtúllépés
);

// Vevő létrehozása bővebb adatokkal és adóalanyiság beállítással
$buyer = new Buyer([
    'name' => 'Teszt Vevő Kft.',
    'country' => 'Magyarország',
    'zip' => '1234',
    'city' => 'Budapest',
    'address' => 'Példa utca 1.',
    'email' => 'vevo@example.com',
    'phone' => '+36 30 123 4567',
    'taxNumber' => '12345678-1-42',
    'taxNumberEU' => 'HU12345678',
    'taxSubject' => TaxSubject::HungarianTaxID,  // Adóalanyiság: magyar adószámmal rendelkezik
    'comment' => 'Kiemelt ügyfél',
    // Szállítási adatok, ha eltér a számlázási címtől
    'shippingName' => 'Teszt Vevő Kft. Telephely',
    'shippingCountry' => 'Magyarország',
    'shippingZip' => '4321',
    'shippingCity' => 'Debrecen',
    'shippingAddress' => 'Szállítási utca 9.',
]);

// Eladó adatainak felülbírálása (opcionális, csak ha az alapértelmezett profil nem megfelelő)
$seller = new Seller([
    'bank' => 'OTP Bank Nyrt.',
    'bankAccount' => '11111111-22222222-33333333',
    'emailReplyTo' => 'info@pelda.hu',
    'emailSubject' => 'Új számla: {invoiceNumber}',
    'emailText' => 'Tisztelt {buyer}!\n\nCsatolva küldjük a számlát.\n\nÜdvözlettel,\nPélda Cég'
]);

// Tételek létrehozása részletes adatokkal
$items = [];

// 1. tétel: webfejlesztés
$items[] = new Item([
    'label' => 'Webfejlesztés',
    'quantity' => 10,
    'unit' => 'óra',
    'vat' => 27,
    'netUnitPrice' => 15000,
    'comment' => 'Weboldal fejlesztés React keretrendszerrel',
    // Opcionálisan megadhatjuk közvetlenül az értékeket is a számítás helyett
    'netAmount' => 150000,      // 10 óra × 15000 Ft = 150000 Ft
    'vatAmount' => 40500,       // 150000 Ft × 27% = 40500 Ft
    'grossAmount' => 190500     // 150000 Ft + 40500 Ft = 190500 Ft
]);

// 2. tétel: tárhelyszolgáltatás
$items[] = new Item([
    'label' => 'Tárhelyszolgáltatás',
    'quantity' => 12,
    'unit' => 'hónap',
    'vat' => 27,
    'netUnitPrice' => 3000,
    'comment' => 'Éves tárhelyszolgáltatás (12 hónap)',
]);

// 3. tétel: domain regisztráció
$items[] = new Item([
    'label' => 'Domain regisztráció',
    'quantity' => 1,
    'unit' => 'db',
    'vat' => 27,
    'netUnitPrice' => 4000,
    'comment' => 'pelda.hu domain név regisztráció 1 évre',
]);

// Számla létrehozása haladó beállításokkal
$invoiceBuilder = new InvoiceBuilder($buyer);

// Tételek hozzáadása
foreach ($items as $item) {
    $invoiceBuilder->addItem($item);
}

// Alapvető beállítások
$invoiceBuilder
    ->setPaymentMethod(PaymentMethod::BankTransfer)
    ->setCurrency(Currency::Ft)
    ->setLanguage(Language::Hungarian)
    ->setSeller($seller);

// Számla kelte és határidők
$invoiceBuilder
    ->setIssueDate(new DateTime())                        // Számla kelte: ma
    ->setFulfillmentDate(new DateTime('-5 days'))         // Teljesítés: 5 nappal ezelőtt
    ->setDueDate(new DateTime('+8 days'));                // Fizetési határidő: 8 nap múlva

// Egyéb beállítások
$invoiceBuilder
    ->setOrderNumber('ORD-2023-042')                      // Rendelésszám
    ->setInvoiceIdPrefix('WEB')                           // Számlaszám előtag
    ->setComment('Köszönjük a megrendelést!')             // Megjegyzés
    ->setPaid(false);                                     // Számla állapota: nincs fizetve

// Véglegesítés és számla építés
$invoice = $invoiceBuilder->build();

try {
    // Számla kiállítása
    $result = $client->issueInvoice($invoice);
    
    // A számla adatainak kiírása
    echo "Számla sikeresen kiállítva!" . PHP_EOL;
    echo "Számla azonosító: " . $result['invoiceId'] . PHP_EOL;
    echo "Nettó összeg: " . $result['netTotal'] . PHP_EOL;
    echo "Bruttó összeg: " . $result['grossTotal'] . PHP_EOL;
    
    if (isset($result['customerAccountUrl'])) {
        echo "Vevői fiók URL: " . $result['customerAccountUrl'] . PHP_EOL;
    }
    
    // Ha kértük a számla letöltését, mentsük el a PDF-et
    if (isset($result['pdf'])) {
        $pdfPath = __DIR__ . '/' . $result['invoiceId'] . '.pdf';
        file_put_contents($pdfPath, $result['pdf']);
        echo "A számla PDF elmentve: " . $pdfPath . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Hiba történt a számla kiállítása során: " . $e->getMessage() . PHP_EOL;
} 