<?php

namespace Szamlazzphp\Client;

use Szamlazzphp\Invoice;
use Szamlazzphp\Enum\ResponseVersion;
use Szamlazzphp\Response\DownloadInvoiceResponse;

/**
 * Számlázz.hu API kliens interfész
 */
interface ClientInterface
{
    /**
     * Számla adatok lekérdezése
     * 
     * @param string|null $invoiceId Számla azonosító (számlaszám)
     * @param string|null $orderNumber Rendelésszám
     * @param bool $pdf PDF formátumban kéri-e a választ
     * @return array A számla adatai
     */
    public function getInvoiceData(?string $invoiceId = null, ?string $orderNumber = null, bool $pdf = false): array;

    /**
     * Számla sztornózása
     * 
     * @param string $invoiceId Számla azonosító
     * @param bool $eInvoice E-számla generálása
     * @param bool $requestInvoiceDownload Számla letöltése PDF-ben
     * @return array A sztornó számla adatai
     */
    public function reverseInvoice(string $invoiceId, bool $eInvoice, bool $requestInvoiceDownload): array;

    /**
     * Számla kiállítása
     * 
     * @param Invoice $invoice A kiállítandó számla objektum
     * @return array A kiállított számla adatai
     */
    public function issueInvoice(Invoice $invoice): array;

    /**
     * Számla letöltése PDF formátumban
     * 
     * @param string $invoiceId Számla azonosító
     * @param ResponseVersion $responseVersion Válasz verzió
     * @param string|null $externalId Külső azonosító
     * @return DownloadInvoiceResponse A letöltött számla adatai
     */
    public function downloadInvoicePdf(string $invoiceId, ResponseVersion $responseVersion = ResponseVersion::PDF, ?string $externalId = null): DownloadInvoiceResponse;

    /**
     * Beállítja, hogy kérje-e a számla PDF letöltését
     * 
     * @param bool $value Kérjen-e PDF letöltést
     * @return void
     */
    public function setRequestInvoiceDownload(bool $value): void;
} 