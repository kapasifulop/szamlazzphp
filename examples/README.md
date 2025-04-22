# Számlázz.hu PHP példák

Ez a könyvtár különböző példákat tartalmaz a Számlázz.hu API használatához PHP nyelven.

## Példa fájlok

### Alapvető számla kiállítás

- [basic-invoice.php](basic-invoice.php) - Egyszerű számla kiállítása egy tétellel

```php
// Számla kiállítása
$response = $client->issueInvoice($invoice);

if ($response->isSuccess()) {
    // Számla adatok lekérdezése
    $invoiceId = $response->getInvoiceId();
    $netTotal = $response->getNetTotal();
    $grossTotal = $response->getGrossTotal();
    
    // PDF mentése
    $response->savePdf('szamla.pdf');
}
```

### Haladó számla kiállítás

- [advanced-invoice.php](advanced-invoice.php) - Összetettebb számla kiállítása több tétellel, különböző opciókkal

### Számla sztornózás

- [reverse-invoice.php](reverse-invoice.php) - Korábban kiállított számla sztornózása

```php
// Számla sztornózása
$response = $client->reverseInvoice('SZÁMLA-001', true, true);

if ($response->isSuccess()) {
    // Sztornó számla adatok lekérdezése
    $invoiceId = $response->getInvoiceId();
    $netTotal = $response->getNetTotal();
    $grossTotal = $response->getGrossTotal();
    
    // PDF mentése
    $response->savePdf('sztorno_szamla.pdf');
}
```

### Számlaadatok lekérdezése

- [get-invoice-data.php](get-invoice-data.php) - Számla adatok lekérdezése számlaszám vagy rendelésszám alapján

```php
// Számla adatok lekérdezése
$response = $client->getInvoiceData('SZÁMLA-001', null, true);

if ($response->isSuccess()) {
    // Számla adatok lekérdezése
    $invoiceData = $response->getInvoiceData();
    
    // Ha PDF-et is kértünk
    if ($response->getPdf()) {
        $response->savePdf('szamla.pdf');
    }
}
```

### Számla letöltés PDF formátumban

- [download-invoice.php](download-invoice.php) - Kiállított számla letöltése PDF formátumban

```php
// Számla letöltése
$response = $client->downloadInvoicePdf('SZÁMLA-001');

if ($response->isSuccess()) {
    // PDF mentése
    $response->savePdf('szamla.pdf');
}
```

### EU-s számla kiállítása

- [eu-invoice.php](eu-invoice.php) - EU-s partner részére történő számlázás

### Autentikációs módok

- [auth-based-client.php](auth-based-client.php) - Felhasználónév-jelszó alapú kliens használata

> **FONTOS:** Felhasználónév és jelszó páros csak akkor használható, ha nincs bekapcsolva a kétlépcsős azonosítás a Számlázz.hu fiókban! [több információ](https://docs.szamlazz.hu/hu/agent/basics/agent-user)

## Laravel integráció

- [laravel-example.php](laravel-example.php) - Példa a Laravel integrációra
- [laravel-example-2.php](laravel-example-2.php) - További Laravel példa a SzamlazzHU facade használatára

## Használat

A példák futtatásához:

1. Telepítsd a könyvtárat a `composer require szamlazzphp/szamlazzphp` paranccsal
2. Navigálj a `examples` mappába
3. Módosítsd a kiválasztott példafájlban az "az_ön_api_kulcsa" vagy "felhasznalonev"/"jelszo" értékeket a valós hozzáférési adataidra
4. Futtasd a kiválasztott példát: `php basic-invoice.php`

## Megjegyzések

* A példák szemléltető jellegűek, a valódi környezetben valószínűleg konfigurációs fájlból vagy környezeti változókból fogod betölteni az API kulcsot vagy a felhasználónevet/jelszót.
* A Builder pattern használata opcionális, de javasolt, mert átláthatóbb és könnyebben kezelhető kódot eredményez.
* A PDF-ek automatikusan mentésre kerülnek a példa futtatásának mappájába.
* Ügyelj arra, hogy az adatok (nevek, címek, adószámok stb.) valós környezetben megfeleljenek a törvényi előírásoknak. 