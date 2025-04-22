<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\Enum\ResponseVersion;

/**
 * Példa számla letöltésre
 * 
 * Ez a példa bemutatja, hogyan tölthetünk le egy számlát PDF formátumban
 * a Számlázz.hu rendszeréből többféle módon.
 * 
 * A példa a SzamlaAgentClient-et használja API kulcs alapú autentikációval.
 */

// Hiba megjelenítés engedélyezése fejlesztéshez
error_reporting(E_ALL);
ini_set('display_errors', '1');

// API kulcs és egyéb beállítások
$apiKey = 'az-on-szamlazz-hu-api-kulcsa';

// Kliens létrehozása
$client = new SzamlaAgentClient($apiKey);

// Számlaszám, amelyet le szeretnénk tölteni
$invoiceNumber = 'TESZT-2023-001';

try {
    //------------------------------------------------------------
    // 1. Számla letöltése egyszerű módon (bináris válasz)
    //------------------------------------------------------------
    echo "1. Számla letöltése egyszerű módon (PDF formátumban)...\n";
    
    $response = $client->downloadInvoicePdf($invoiceNumber);
    
    if ($response->isSuccess()) {
        // Számla mentése fájlba
        $pdfPath1 = __DIR__ . '/szamla1_' . str_replace('/', '_', $invoiceNumber) . '.pdf';
        $response->savePdf($pdfPath1);
        echo "A számla sikeresen letöltve és elmentve: {$pdfPath1}\n";
        
        // Alternatív mentési mód
        $pdfPath2 = __DIR__ . '/szamla1_alt_' . str_replace('/', '_', $invoiceNumber) . '.pdf';
        $response->storePdf($pdfPath2);
        echo "A számla sikeresen letöltve és elmentve (alternatív módon): {$pdfPath2}\n";
        
        // A PDF adatok közvetlen elérése, ha további feldolgozásra van szükség
        $pdfData = $response->getPdf();
        echo "A PDF mérete: " . strlen($pdfData) . " bájt\n";
    } else {
        echo "Hiba történt a számla letöltése közben: " . $response->getErrorMessage() . "\n";
        if ($response->getErrorCode()) {
            echo "Hibakód: " . $response->getErrorCode() . "\n";
        }
    }
    
    //------------------------------------------------------------
    // 2. Számla letöltése XML válasszal (több információt ad)
    //------------------------------------------------------------
    echo "\n2. Számla letöltése XML válasszal...\n";
    
    $response = $client->downloadInvoicePdf($invoiceNumber, ResponseVersion::XML);
    
    if ($response->isSuccess()) {
        // Az előző módszerhez hasonlóan elmenthetjük a PDF-et
        $pdfPath3 = __DIR__ . '/szamla2_' . str_replace('/', '_', $invoiceNumber) . '.pdf';
        $response->savePdf($pdfPath3);
        echo "A számla sikeresen letöltve és elmentve: {$pdfPath3}\n";
        
        // XML válasz esetén többet tudunk a számláról
        echo "Számla azonosító: " . $response->getInvoiceId() . "\n";
        echo "Nettó összeg: " . $response->getNetTotal() . "\n";
        echo "Bruttó összeg: " . $response->getGrossTotal() . "\n";
    } else {
        echo "Hiba történt a számla letöltése közben: " . $response->getErrorMessage() . "\n";
        if ($response->getErrorCode()) {
            echo "Hibakód: " . $response->getErrorCode() . "\n";
        }
    }
    
    //------------------------------------------------------------
    // 3. Számla letöltése külső azonosító alapján
    //------------------------------------------------------------
    echo "\n3. Számla letöltése külső azonosító alapján...\n";
    
    // Csak akkor használható, ha a számla létrehozásakor külső azonosítót is megadtunk
    $externalId = 'KULSO-AZONOSITO-123';
    
    $response = $client->downloadInvoicePdf('', ResponseVersion::XML, $externalId);
    
    if ($response->isSuccess()) {
        $pdfPath4 = __DIR__ . '/szamla3_kulso_' . str_replace('/', '_', $externalId) . '.pdf';
        $response->savePdf($pdfPath4);
        echo "A számla sikeresen letöltve és elmentve: {$pdfPath4}\n";
        
        echo "Számla azonosító: " . $response->getInvoiceId() . "\n";
        echo "Nettó összeg: " . $response->getNetTotal() . "\n";
        echo "Bruttó összeg: " . $response->getGrossTotal() . "\n";
    } else {
        echo "Hiba történt a számla letöltése közben külső azonosító alapján: " . $response->getErrorMessage() . "\n";
        if ($response->getErrorCode()) {
            echo "Hibakód: " . $response->getErrorCode() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Kivétel történt: " . $e->getMessage() . "\n";
} 