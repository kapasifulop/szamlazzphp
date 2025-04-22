<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\Enum\ResponseVersion;

/**
 * Példa számlaadat lekérdezése
 * 
 * Ez a példa bemutatja, hogyan lehet lekérdezni egy számla adatait 
 * a Számlázz.hu rendszeréből számlaszám vagy rendelésszám alapján.
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

try {
    // 1. Számla adatok lekérése számlaszám alapján
    $response = $client->getInvoiceData('TESZT-2023-001', null, true);
    
    if ($response->isSuccess()) {
        echo "A számla adatok sikeresen lekérdezve." . PHP_EOL;
        
        // Az összes számlaadatot tartalmazó tömb
        $invoiceData = $response->getInvoiceData();
        
        // Néhány mező kinyerése a válaszból (példa)
        if (isset($invoiceData['alap'])) {
            $alap = $invoiceData['alap'];
            echo "Számla szám: " . ($alap['szamlaszam'] ?? 'N/A') . PHP_EOL;
            echo "Kelt: " . ($alap['kelt'] ?? 'N/A') . PHP_EOL;
            echo "Fizetési határidő: " . ($alap['fizHat'] ?? 'N/A') . PHP_EOL;
            echo "Fizetési mód: " . ($alap['fizmod'] ?? 'N/A') . PHP_EOL;
        }
        
        // Vevő adatok
        if (isset($invoiceData['vevo'])) {
            $vevo = $invoiceData['vevo'];
            echo "Vevő neve: " . ($vevo['nev'] ?? 'N/A') . PHP_EOL;
        }
        
        // Tételek
        if (isset($invoiceData['tetelek']['tetel'])) {
            $tetelek = $invoiceData['tetelek']['tetel'];
            // Ha csak egy tétel van, akkor a tetelek.tetel egy asszociatív tömb, 
            // különben tömbök tömbje
            if (isset($tetelek['megnevezes'])) {
                echo "1 tétel található a számlán:" . PHP_EOL;
                echo "  - " . $tetelek['megnevezes'] . " (" . $tetelek['nettoar'] . " " . $invoiceData['alap']['penznem'] . ")" . PHP_EOL;
            } else {
                echo count($tetelek) . " tétel található a számlán:" . PHP_EOL;
                foreach ($tetelek as $tetel) {
                    echo "  - " . $tetel['megnevezes'] . " (" . $tetel['nettoar'] . " " . $invoiceData['alap']['penznem'] . ")" . PHP_EOL;
                }
            }
        }
        
        // PDF mentése ha kértük és van PDF a válaszban
        if ($response->getPdf()) {
            $pdfFile = 'szamla_' . str_replace('/', '_', $invoiceData['alap']['szamlaszam'] ?? 'ismeretlen') . '.pdf';
            $response->savePdf($pdfFile);
            echo "PDF elmentve: $pdfFile" . PHP_EOL;
        }
    } else {
        echo "Hiba történt a számla adatok lekérésekor: " . $response->getErrorMessage() . PHP_EOL;
    }
    
    // 2. Számla adatok lekérése rendelésszám alapján
    // Ezt csak akkor használjuk, ha ismerjük a rendelésszámot
    /*
    $response = $client->getInvoiceData(null, 'R-2023-001', true);
    
    if ($response->isSuccess()) {
        echo "A számla adatok sikeresen lekérdezve rendelésszám alapján." . PHP_EOL;
        $invoiceData = $response->getInvoiceData();
        // További feldolgozás...
    } else {
        echo "Hiba történt a számla adatok lekérésekor rendelésszám alapján: " . $response->getErrorMessage() . PHP_EOL;
    }
    */
    
} catch (Exception $e) {
    echo "Hiba történt: " . $e->getMessage() . PHP_EOL;
} 