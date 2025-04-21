# Számlázz.hu PHP példák

Ebben a mappában különböző példákat találsz a Számlázz.hu PHP könyvtár használatához. A példák segítenek megérteni, hogyan lehet különböző típusú számlákat kiállítani, számla adatokat lekérdezni és számlákat sztornózni.

## Példák listája

1. **basic-invoice.php** - Egyszerű számla kiállítása
   * Alap API kulcs használata
   * Számla kiállítása Builder pattern segítségével
   * Több tétel hozzáadása
   * PDF eredmény mentése

2. **advanced-invoice.php** - Haladó számla kiállítása
   * Részletes vevő és eladó adatok
   * Tételek részletes adatokkal
   * Builder pattern használata lépésről lépésre
   * Egyedi számlaszám előtag és dátumok beállítása

3. **get-invoice-data.php** - Számla adatok lekérdezése
   * Számla lekérdezése számlaszám alapján
   * Számla lekérdezése rendelésszám alapján
   * PDF letöltése a számlához

4. **reverse-invoice.php** - Számla sztornózása
   * Számla sztornózása számlaszám alapján
   * Sztornó számla PDF letöltése

5. **auth-based-client.php** - Felhasználónév-jelszó alapú kliens használata
   * Számla kiállítása felhasználónév-jelszó autentikációval
   * Készpénzes fizetési mód példa

6. **eu-invoice.php** - EU-s számla kiállítása
   * EU-s partner számára történő számla kiállítása
   * EUR pénznem használata
   * ÁFA mentesség kezelése
   * Árfolyam beállítása

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