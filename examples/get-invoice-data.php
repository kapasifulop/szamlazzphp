<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;

// API kulcs alapú kliens létrehozása
$client = new SzamlaAgentClient(
    'az_ön_api_kulcsa', // Ide a valódi API kulcs kerül
    false,
    false
);

// Számlaszám, amelyet le szeretnénk kérdezni
$invoiceId = 'WEB-2023-123';

try {
    // 1. Számla lekérdezése számlaszám alapján (PDF nélkül)
    $invoiceData = $client->getInvoiceData($invoiceId, null, false);
    
    // Az eredmény feldolgozása
    echo "Számla adatok lekérdezve: {$invoiceId}" . PHP_EOL;
    echo "Vevő: " . ($invoiceData['vevo']['nev'] ?? 'N/A') . PHP_EOL;
    echo "Kiállítás dátuma: " . ($invoiceData['fejlec']['keltDatum'] ?? 'N/A') . PHP_EOL;
    echo "Nettó összeg: " . ($invoiceData['osszesites']['nettoOsszesen'] ?? 'N/A') . PHP_EOL;
    echo "Bruttó összeg: " . ($invoiceData['osszesites']['bruttoOsszesen'] ?? 'N/A') . PHP_EOL;
    
    // Tételek listázása
    if (isset($invoiceData['tetelek']['tetel'])) {
        $tetelek = $invoiceData['tetelek']['tetel'];
        // Ha csak egy tétel van, akkor nem tömb formában kapjuk
        if (!isset($tetelek[0])) {
            $tetelek = [$tetelek];
        }
        
        echo "\nSzámla tételek:\n";
        foreach ($tetelek as $index => $tetel) {
            echo ($index + 1) . ". " . ($tetel['megnevezes'] ?? 'N/A') . " - ";
            echo ($tetel['mennyiseg'] ?? 'N/A') . " " . ($tetel['mennyisegiEgyseg'] ?? 'N/A') . " - ";
            echo "Nettó: " . ($tetel['nettoErtek'] ?? 'N/A') . " Ft, ";
            echo "Bruttó: " . ($tetel['bruttoErtek'] ?? 'N/A') . " Ft\n";
        }
    }
    
    // 2. Ugyanaz PDF-fel (másik példa)
    echo "\nPDF letöltése a számlához...\n";
    $invoiceDataWithPdf = $client->getInvoiceData($invoiceId, null, true);
    
    if (isset($invoiceDataWithPdf['pdf'])) {
        $pdfPath = __DIR__ . '/' . $invoiceId . '.pdf';
        file_put_contents($pdfPath, base64_decode($invoiceDataWithPdf['pdf']));
        echo "A számla PDF elmentve: " . $pdfPath . PHP_EOL;
    } else {
        echo "A PDF nem elérhető a számlához." . PHP_EOL;
    }
    
    // 3. Számla lekérdezése rendelésszám alapján
    $orderNumber = 'ORD-2023-042';
    echo "\nSzámla keresése rendelésszám alapján: {$orderNumber}\n";
    $invoiceByOrder = $client->getInvoiceData(null, $orderNumber, false);
    
    if (!empty($invoiceByOrder)) {
        echo "Talált számla: " . ($invoiceByOrder['alap']['szamlaszam'] ?? 'N/A') . PHP_EOL;
    } else {
        echo "Nem található számla a megadott rendelésszámmal." . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Hiba történt a számla adatok lekérdezése során: " . $e->getMessage() . PHP_EOL;
} 