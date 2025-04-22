<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;

/**
 * Példa számla sztornózására
 * 
 * Ez a példa bemutatja, hogyan lehet egy kiállított számlát sztornózni
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
$requestInvoiceDownload = true;     // Számla letöltése PDF-ben (sztornó számla)

// Kliens létrehozása
$client = new SzamlaAgentClient(
    $apiKey,
    $eInvoice,
    $requestInvoiceDownload
);

// Sztornózandó számla száma
$invoiceNumber = 'TESZT-2023-001';

try {
    // Számla sztornózása
    // Paraméterek:
    // - Számlaszám
    // - E-számla generálása a sztornó számlához
    // - PDF letöltése a sztornó számláról
    $response = $client->reverseInvoice($invoiceNumber, $eInvoice, $requestInvoiceDownload);
    
    if ($response->isSuccess()) {
        echo "A számla sikeresen sztornózva lett!" . PHP_EOL;
        echo "Sztornó számla száma: " . $response->getInvoiceId() . PHP_EOL;
        echo "Nettó összeg: " . $response->getNetTotal() . PHP_EOL;
        echo "Bruttó összeg: " . $response->getGrossTotal() . PHP_EOL;
        
        // PDF mentése, ha a letöltést kértük
        if ($requestInvoiceDownload && $response->getPdf()) {
            $pdfFile = 'sztorno_' . str_replace('/', '_', $response->getInvoiceId()) . '.pdf';
            $response->savePdf($pdfFile);
            echo "Sztornó számla PDF elmentve: {$pdfFile}" . PHP_EOL;
        }
    } else {
        echo "Hiba történt a számla sztornózása közben: " . $response->getErrorMessage() . PHP_EOL;
        if ($response->getErrorCode()) {
            echo "Hibakód: " . $response->getErrorCode() . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo "Kivétel történt: " . $e->getMessage() . PHP_EOL;
} 