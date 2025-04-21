<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\Enum\ResponseVersion;

// API kulcs alapú kliens létrehozása
$client = new SzamlaAgentClient(
    'az_ön_api_kulcsa', // Ide a valódi API kulcs kerül
    false,  // eInvoice: nem releváns a letöltésnél
    false   // requestInvoiceDownload: nem releváns a letöltésnél, mivel direkt letöltést kérünk
);

// Vagy felhasználónév-jelszó alapú kliens létrehozása
// $client = new AuthBasedClient(
//     'your_username',
//     'your_password',
//     false, // eInvoice
//     true,  // requestInvoiceDownload
//     1,     // downloadedInvoiceCount
//     1,     // responseVersion
//     0      // timeout
// );


// A letöltendő számla száma
$invoiceId = 'WEB-2023-123';

try {
    // 1. módszer: alapértelmezett PDF válasz verzióval
    $response = $client->downloadInvoicePdf($invoiceId);
    
    // Ellenőrizzük, hogy sikeres volt-e a letöltés
    if ($response->isSuccess()) {
        // Számla adatok kiírása
        echo "Számla sikeresen letöltve!" . PHP_EOL;
        
        // PDF mentése fájlba
        $pdfPath = __DIR__ . '/szamla_' . $invoiceId . '.pdf';
        $response->savePdf($pdfPath);
        // vagy ugyanez más néven:
        // $response->storePdf($pdfPath);
        
        echo "A számla PDF elmentve: " . $pdfPath . PHP_EOL;
    } else {
        // Hiba esetén
        echo "Hiba történt a számla letöltése során!" . PHP_EOL;
        echo "Hibakód: " . $response->getErrorCode() . PHP_EOL;
        echo "Hibaüzenet: " . $response->getErrorMessage() . PHP_EOL;
    }
    
    // 2. módszer: XML válasz verzióval (több adat)
    $xmlResponse = $client->downloadInvoicePdf(
        $invoiceId,
        ResponseVersion::XML  // Enum érték használata
    );
    
    if ($xmlResponse->isSuccess()) {
        // Számla adatok kiírása
        echo "Számla sikeresen letöltve (XML verzió)!" . PHP_EOL;
        echo "Számla azonosító: " . $xmlResponse->getInvoiceId() . PHP_EOL;
        echo "Nettó összeg: " . $xmlResponse->getNetTotal() . PHP_EOL;
        echo "Bruttó összeg: " . $xmlResponse->getGrossTotal() . PHP_EOL;
        
        // PDF mentése fájlba
        $pdfXmlPath = __DIR__ . '/szamla_xml_' . $xmlResponse->getInvoiceId() . '.pdf';
        $xmlResponse->savePdf($pdfXmlPath);
        
        echo "A számla PDF elmentve: " . $pdfXmlPath . PHP_EOL;
    } else {
        // Hiba esetén
        echo "Hiba történt a számla letöltése során (XML verzió)!" . PHP_EOL;
        echo "Hibakód: " . $xmlResponse->getErrorCode() . PHP_EOL;
        echo "Hibaüzenet: " . $xmlResponse->getErrorMessage() . PHP_EOL;
    }
    
    // 3. módszer: Letöltés külső azonosító alapján
    $externalId = 'RENDELES-123';
    $externalResponse = $client->downloadInvoicePdf(
        '',  // Üres számla azonosító
        ResponseVersion::XML,
        $externalId
    );
    
    if ($externalResponse->isSuccess()) {
        echo "Számla sikeresen letöltve külső azonosító alapján!" . PHP_EOL;
        echo "Számla azonosító: " . $externalResponse->getInvoiceId() . PHP_EOL;
        
        // PDF mentése fájlba
        $pdfExternalPath = __DIR__ . '/szamla_ext_' . $externalResponse->getInvoiceId() . '.pdf';
        $externalResponse->savePdf($pdfExternalPath);
        
        echo "A számla PDF elmentve: " . $pdfExternalPath . PHP_EOL;
    } else {
        echo "Hiba történt a számla letöltése során külső azonosító alapján!" . PHP_EOL;
        echo "Hibaüzenet: " . $externalResponse->getErrorMessage() . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Kivétel történt a számla letöltése során: " . $e->getMessage() . PHP_EOL;
} 