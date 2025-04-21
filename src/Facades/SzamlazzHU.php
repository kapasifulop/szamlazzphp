<?php

namespace Szamlazzphp\Facades;

use Illuminate\Support\Facades\Facade;
use Szamlazzphp\Client\ClientInterface;
use Szamlazzphp\Enum\ResponseVersion;
use Szamlazzphp\Response\DownloadInvoiceResponse;

/**
 * SzamlazzHU Facade
 * 
 * @method static array getInvoiceData(?string $invoiceId = null, ?string $orderNumber = null, bool $pdf = false)
 * @method static array reverseInvoice(string $invoiceId, bool $eInvoice, bool $requestInvoiceDownload)
 * @method static array issueInvoice(\Szamlazzphp\Invoice $invoice)
 * @method static void setRequestInvoiceDownload(bool $value)
 * @method static DownloadInvoiceResponse downloadInvoicePdf(string $invoiceId, ResponseVersion $responseVersion = ResponseVersion::PDF, ?string $externalId = null): DownloadInvoiceResponse
 * 
 * @see \Szamlazzphp\Client\ClientInterface
 */
class SzamlazzHU extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ClientInterface::class;
    }
} 