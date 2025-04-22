# Számlázz.hu PHP

A Számlázz.hu API-hoz készített PHP integráció. **FEJLESZTÉS ALATT ÁLL! Az alap számlakiállítási modul válaszai jelenleg átdolgozás alatt állnak (a válasz nem array hanem objektum lesz)!**

## Telepítés

```bash
composer require kapasifulop/szamlazzphp
```

## Laravel integrációval

A csomag Laravel keretrendszerrel is használható. A config publikálásához futtasd:

```bash
php artisan vendor:publish --provider="Szamlazzphp\SzamlazzphpServiceProvider"
```

Ezután konfiguráld a `.env` fájlban:

```
# Autentikáció típusa: api_key (API kulcs) vagy auth (felhasználónév/jelszó)
SZAMLAZZHU_AUTH_TYPE=api_key

# API kulcs alapú autentikáció
SZAMLAZZHU_TOKEN=your_token

# VAGY felhasználónév-jelszó alapú autentikáció csak akkor használható, ha nincs bekapcsolva a kétlépcsős azonosítás a Számlázz.hu fiókban! https://docs.szamlazz.hu/hu/agent/basics/agent-user
# SZAMLAZZHU_USER=your_username
# SZAMLAZZHU_PASSWORD=your_password

# Egyéb beállítások
SZAMLAZZHU_E_INVOICE=false                # E-számla generálása
SZAMLAZZHU_DOWNLOAD=true                  # Számla PDF letöltése
SZAMLAZZHU_DOWNLOAD_COUNT=1               # Letöltendő példányszám
SZAMLAZZHU_RESPONSE_VERSION=1             # Válasz verzió
SZAMLAZZHU_TIMEOUT=30                     # Időtúllépés másodpercben
```

> **FONTOS:** Felhasználónév és jelszó páros csak akkor használható, ha nincs bekapcsolva a kétlépcsős azonosítás a Számlázz.hu fiókban! [több](https://docs.szamlazz.hu/hu/agent/basics/agent-user)

### A SzamlazzHU Facade használata

A csomag tartalmaz egy beépített `SzamlazzHU` Facade-t, amelyet könnyen használhatsz Laravel alkalmazásodban. Ez a Facade automatikusan regisztrálva van, így nincs szükség további konfigurációra.

```php
use Szamlazzphp\Facades\SzamlazzHU;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\Language;

// Számla lekérése
$invoice = SzamlazzHU::getInvoiceData('SZLA-12345');

// Számla kiállítása
$invoice = (new InvoiceBuilder($buyer))
    ->addItem($item)
    ->setPaymentMethod(PaymentMethod::BankTransfer)
    ->setCurrency(Currency::Ft)
    ->setLanguage(Language::Hungarian)
    ->build();

$result = SzamlazzHU::issueInvoice($invoice);

// Számla sztornózása
$result = SzamlazzHU::reverseInvoice('SZLA-12345', true, true);
```

### Alternatív használati módok

A SzamlazzHU Facade mellett továbbra is használhatod a ClientInterface-t is közvetlenül:

#### 1. Constructor Injection

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Szamlazzphp\Client\ClientInterface;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;

class InvoiceController extends Controller
{
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function issueInvoice(Request $request)
    {
        // Számla összeállítása
        $invoice = (new InvoiceBuilder($buyer))
            ->addItem($item)
            ->setPaymentMethod(PaymentMethod::BankTransfer)
            ->setCurrency(Currency::Ft)
            ->setLanguage(Language::Hungarian)
            ->build();
        
        // Számla kiállítása
        $result = $this->client->issueInvoice($invoice);
        
        return response()->json([
            'success' => true,
            'invoice_id' => $result['invoiceId']
        ]);
    }
}
```

#### 2. Service Container közvetlen használata

```php
public function downloadInvoice($invoiceId)
{
    // Kliens lekérése a Service Containerből
    $client = app(ClientInterface::class);
    
    // Számla adatok lekérdezése
    $invoiceData = $client->getInvoiceData($invoiceId, null, true);
    
    // További feldolgozás...
}
```

## Használat

```php
use Szamlazzphp\Client\SzamlaAgentClient;
use Szamlazzphp\Client\AuthBasedClient;
use Szamlazzphp\Invoice;
use Szamlazzphp\InvoiceBuilder;
use Szamlazzphp\Buyer;
use Szamlazzphp\Seller;
use Szamlazzphp\Item;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\PaymentMethod;
use Szamlazzphp\Enum\Language;

// API kulcs alapú kliens létrehozása
$client = new SzamlaAgentClient(
    'your_token',
    false, // eInvoice
    true,  // requestInvoiceDownload
    1,     // downloadedInvoiceCount
    1,     // responseVersion
    0      // timeout
);

// Vagy felhasználónév-jelszó alapú kliens létrehozása
// Felhasználónév és jelszó páros csak akkor használható, ha nincs bekapcsolva a kétlépcsős azonosítás a Számlázz.hu fiókban!
// $client = new AuthBasedClient(
//     'your_username',
//     'your_password',
//     false, // eInvoice
//     true,  // requestInvoiceDownload
//     1,     // downloadedInvoiceCount
//     1,     // responseVersion
//     0      // timeout
// );

// Vevő létrehozása
$buyer = new Buyer([
    'name' => 'Vevő Neve',
    'country' => 'Magyarország',
    'zip' => '1234',
    'city' => 'Budapest',
    'address' => 'Példa utca 1.',
    'email' => 'buyer@example.com',
    'taxNumber' => '12345678-1-42',
]);

// Eladó létrehozása (opcionális, ha az alapértelmezett számlázz.hu profilod megfelelő)
$seller = new Seller([
    'bank' => 'OTP Bank',
    'bankAccount' => '11111111-22222222-33333333',
]);

// Tétel létrehozása
$item = new Item([
    'label' => 'Termék vagy szolgáltatás',
    'quantity' => 1,
    'unit' => 'db',
    'vat' => 27, // ÁFA százalék
    'netUnitPrice' => 10000, // nettó egységár
    'comment' => 'Megjegyzés a tételhez',
]);

// Számla létrehozása Builder pattern segítségével (ajánlott)
$invoice = (new InvoiceBuilder($buyer))
    ->addItem($item)
    ->setPaymentMethod(PaymentMethod::BankTransfer)
    ->setCurrency(Currency::Ft)
    ->setLanguage(Language::Hungarian)
    ->setSeller($seller)
    ->setOrderNumber('ABC-123')
    ->setComment('Megjegyzés a számlához')
    ->build();

// Számla kiállítása
$result = $client->issueInvoice($invoice);

// Eredmény
echo "Számla azonosító: " . $result['invoiceId'] . PHP_EOL;

// Közvetlenül is létrehozható a számla (egyszerűbb esetekre)
$directInvoice = new Invoice(
    $buyer,
    [$item],
    new DateTime(), // issueDate - kiállítás dátuma
    new DateTime(), // fulfillmentDate - teljesítés dátuma
    new DateTime(), // dueDate - fizetési határidő
    PaymentMethod::BankTransfer,
    Currency::Ft,
    Language::Hungarian
);
$directInvoice->setSeller($seller);
$directInvoice->setOrderNumber('ABC-123');
```

## InvoiceBuilder használata

Az InvoiceBuilder egy fluent interfészt biztosít a számlák egyszerű és olvasható létrehozásához:

```php
// Több tétel hozzáadása
$builder = new InvoiceBuilder($buyer);
$builder->addItem($item1);
$builder->addItem($item2);
$builder->addItem($item3);

// Vagy tömb formában
$builder->setItems([$item1, $item2, $item3]);

// Dátumok beállítása
$builder->setIssueDate(new DateTime('2023-12-01'))
    ->setFulfillmentDate(new DateTime('2023-12-01'))
    ->setDueDate(new DateTime('2023-12-15'));

// Egyéb opcionális beállítások
$builder->setOrderNumber('ABC-123')
    ->setPaid(true)
    ->setProforma(false)
    ->setNoNavReport(false)
    ->setInvoiceIdPrefix('INVOICE')
    ->setComment('Megjegyzés a számlához')
    ->setLogoImage('path/to/logo.png');

// Pénznem és árfolyam beállítása
$builder->setCurrency(Currency::EUR)
    ->setExchangeRate(380.0)
    ->setExchangeBank('MNB');
    
// Véglegesítés és számla létrehozása
$invoice = $builder->build();
```

## Metódusok

### Számla kiállítása
```php
$client->issueInvoice($invoice);
// Vagy Laravelben
// $response = SzamlazzHU::issueInvoice($invoice);
```

### Számla adatok lekérése
```php
$client->getInvoiceData('SZLA-123');
// vagy
$client->getInvoiceData(null, 'ABC-123');
// Vagy Laravelben
// $response = SzamlazzHU::getInvoiceData('SZLA-123');
```

### Számla sztornózása
```php
$client->reverseInvoice('SZLA-123', true, true);
// Vagy Laravelben
// $response = SzamlazzHU::reverseInvoice('SZLA-123', true, true);
```

### Számla letöltése PDF formátumban
```php

// Számla letöltése PDF-ben
$response = $client->downloadInvoicePdf('SZLA-123');
// Vagy Laravelben
// $response = SzamlazzHU::downloadInvoicePdf('SZLA-123')

// Ellenőrzés, hogy sikeres volt-e a letöltés
if ($response->isSuccess()) {
    // PDF mentése fájlba
    $response->savePdf('szamla.pdf');
    // vagy
    $response->storePdf('szamla.pdf');
    
    // Számla adatok lekérdezése
    $invoiceId = $response->getInvoiceId();
    $netTotal = $response->getNetTotal();
    $grossTotal = $response->getGrossTotal();
} else {
    // Hiba esetén
    $errorCode = $response->getErrorCode();
    $errorMessage = $response->getErrorMessage();
    echo "Hiba: {$errorMessage} (kód: {$errorCode})";
}
```

A `DownloadInvoiceResponse` osztály a letöltött számla adatait tartalmazza:

- `isSuccess()` - Sikeres volt-e a letöltés
- `getErrorCode()` - Hiba kód lekérdezése
- `getErrorMessage()` - Hibaüzenet lekérdezése
- `getInvoiceId()` - Számla azonosító lekérdezése
- `getNetTotal()` - Számla nettó összegének lekérdezése
- `getGrossTotal()` - Számla bruttó összegének lekérdezése
- `getPdf()` - PDF binary adatai
- `savePdf($filename)` / `storePdf($filename)` - PDF mentése fájlba

## DownloadInvoiceResponse osztály részletesen

A `DownloadInvoiceResponse` osztály kezeli a számla PDF letöltésekor kapott válaszokat.

Az osztály segítségével egyszerűen kezelhető a számlák PDF-ben való letöltése és a válaszok feldolgozása. Az osztály támogatja a fluent interfészt, így a metódusok láncolhatók.

## Licenc

MIT