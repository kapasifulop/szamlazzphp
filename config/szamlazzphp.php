<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Számlázz.hu Autentikáció
    |--------------------------------------------------------------------------
    |
    | Szükséges autentikációs adatok a Számlázz.hu API-hoz. Használhatsz
    | API kulcsot, vagy felhasználónév-jelszó párost is.
    |
    */
    'auth_type' => env('SZAMLAZZHU_AUTH_TYPE', 'api_key'), // 'api_key' vagy 'auth'
    'auth_token' => env('SZAMLAZZHU_TOKEN', ''),
    'username' => env('SZAMLAZZHU_USER', ''),
    'password' => env('SZAMLAZZHU_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | E-számla beállítások
    |--------------------------------------------------------------------------
    |
    | E-számla generálás alapértelmezett beállításai.
    |
    */
    'e_invoice' => env('SZAMLAZZHU_E_INVOICE', false),
    'request_invoice_download' => env('SZAMLAZZHU_DOWNLOAD', true),
    'downloaded_invoice_count' => env('SZAMLAZZHU_DOWNLOAD_COUNT', 1),
    'response_version' => env('SZAMLAZZHU_RESPONSE_VERSION', 1),

    /*
    |--------------------------------------------------------------------------
    | Eladó adatok
    |--------------------------------------------------------------------------
    |
    | Alapértelmezett eladó beállítások.
    |
    */
    'seller' => [
        'bank' => env('SZAMLAZZHU_BANK', ''),
        'bank_account' => env('SZAMLAZZHU_BANK_ACCOUNT', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Kérés beállítások
    |--------------------------------------------------------------------------
    |
    | API kérés időtúllépési ideje másodpercben.
    |
    */
    'timeout' => env('SZAMLAZZHU_TIMEOUT', 30),
]; 