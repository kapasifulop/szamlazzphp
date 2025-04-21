<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;

// API kulcs alapú kliens létrehozása
$client = new SzamlaAgentClient(
    'az_ön_api_kulcsa', // Ide a valódi API kulcs kerül
    true,  // eInvoice: a sztornó számla is e-számla legyen
    true   // requestInvoiceDownload: kérjük a sztornó számla letöltését
);

// A sztornózandó számla száma
$invoiceId = 'WEB-2023-123';

try {
    // Számla sztornózása
    $result = $client->reverseInvoice(
        $invoiceId,
        true,  // eInvoice: a sztornó számla e-számla legyen
        true   // requestInvoiceDownload: kérjük a sztornó számla letöltését
    );
    
    // A sztornó számla adatainak kiírása
    echo "Számla sikeresen sztornózva!" . PHP_EOL;
    echo "Sztornó számla azonosító: " . $result['invoiceId'] . PHP_EOL;
    echo "Nettó összeg: " . $result['netTotal'] . PHP_EOL;
    echo "Bruttó összeg: " . $result['grossTotal'] . PHP_EOL;
    
    if (isset($result['customerAccountUrl'])) {
        echo "Vevői fiók URL: " . $result['customerAccountUrl'] . PHP_EOL;
    }
    
    // Ha kértük a számla letöltését, mentsük el a PDF-et
    if (isset($result['pdf'])) {
        $pdfPath = __DIR__ . '/storno_' . $result['invoiceId'] . '.pdf';
        file_put_contents($pdfPath, $result['pdf']);
        echo "A sztornó számla PDF elmentve: " . $pdfPath . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Hiba történt a számla sztornózása során: " . $e->getMessage() . PHP_EOL;
} 