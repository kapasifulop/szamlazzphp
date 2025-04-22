<?php

namespace Szamlazzphp\Facades;

use Illuminate\Support\Facades\Facade;
use Szamlazzphp\Client\ClientInterface;
use Szamlazzphp\Enum\ResponseVersion;
use Szamlazzphp\Response\DownloadInvoiceResponse;
use Szamlazzphp\Response\GetInvoiceDataResponse;
use Szamlazzphp\Response\IssueInvoiceResponse;
use Szamlazzphp\Response\ReverseInvoiceResponse;

/**
 * SzamlazzHU Facade
 * 
 * @method static GetInvoiceDataResponse getInvoiceData(?string $invoiceId = null, ?string $orderNumber = null, bool $pdf = false) : GetInvoiceDataResponse
 * @method static ReverseInvoiceResponse reverseInvoice(string $invoiceId, bool $eInvoice, bool $requestInvoiceDownload) : ReverseInvoiceResponse
 * @method static IssueInvoiceResponse issueInvoice(\Szamlazzphp\Invoice $invoice) : IssueInvoiceResponse
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