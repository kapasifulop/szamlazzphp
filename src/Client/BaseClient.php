<?php

namespace Szamlazzphp\Client;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use SimpleXMLElement;
use Szamlazzphp\Invoice;
use Szamlazzphp\Enum\ResponseVersion;
use Szamlazzphp\Response\DownloadInvoiceResponse;

/**
 * Számlázz.hu API alap kliens
 */
abstract class BaseClient
{
    protected GuzzleClient $httpClient;
    protected CookieJar $cookieJar;
    protected bool $eInvoice = false;
    protected bool $requestInvoiceDownload = false;
    protected int $downloadedInvoiceCount = 1;
    protected int $responseVersion = 1;
    protected int $timeout = 0;

    /**
     * Autentikációs mezők lekérése
     * 
     * @return array
     */
    abstract protected function getAuthFields(): array;

    /**
     * BaseClient konstruktor
     * 
     * @param bool $eInvoice E-számla generálása
     * @param bool $requestInvoiceDownload Számla letöltése PDF-ben
     * @param int $downloadedInvoiceCount Letöltendő példányszám
     * @param int $responseVersion Válasz verzió
     * @param int $timeout Kérés időtúllépés másodpercben
     */
    public function __construct(
        bool $eInvoice = false,
        bool $requestInvoiceDownload = false,
        int $downloadedInvoiceCount = 1,
        int $responseVersion = 1,
        int $timeout = 0
    ) {
        $this->eInvoice = $eInvoice;
        $this->requestInvoiceDownload = $requestInvoiceDownload;
        $this->downloadedInvoiceCount = $downloadedInvoiceCount;
        $this->responseVersion = $responseVersion;
        $this->timeout = $timeout;

        $this->cookieJar = new CookieJar();
        $this->httpClient = new GuzzleClient([
            'cookies' => $this->cookieJar,
            'timeout' => $this->timeout
        ]);
    }

    /**
     * Számla adatok lekérdezése
     * 
     * @param string|null $invoiceId Számla azonosító (számlaszám)
     * @param string|null $orderNumber Rendelésszám
     * @param bool $pdf PDF formátumban kéri-e a választ
     * @return array A számla adatai
     * @throws Exception Ha hiányos az azonosítás vagy hiba történt a lekérdezéskor
     */
    public function getInvoiceData(?string $invoiceId = null, ?string $orderNumber = null, bool $pdf = false): array
    {
        if (empty($invoiceId) && empty($orderNumber)) {
            throw new Exception('A számla azonosító vagy a rendelésszám megadása kötelező');
        }

        $xml = $this->getXmlHeader('xmlszamlaxml', 'agentxml') .
            $this->wrapWithElement([
                ...$this->getAuthFields(),
                ['szamlaszam', $invoiceId ?? ''],
                ['rendelesSzam', $orderNumber ?? ''],
                ['pdf', $pdf]
            ]) .
            '</xmlszamlaxml>';

        $response = $this->sendRequest('action-szamla_agent_xml', $xml);
        
        if (!$response['success']) {
            throw new Exception($response['errorMessage'] ?? 'Hiba történt a számla adatok lekérdezésekor');
        }
        
        return $this->parseSimpleXmlToArray($response['data']);
    }

    /**
     * Számla sztornózása
     * 
     * @param string $invoiceId Számla azonosító
     * @param bool $eInvoice E-számla generálása
     * @param bool $requestInvoiceDownload Számla letöltése PDF-ben
     * @return array A sztornó számla adatai
     * @throws Exception Ha hiányos az azonosítás vagy hiba történt a sztornózáskor
     */
    public function reverseInvoice(string $invoiceId, bool $eInvoice, bool $requestInvoiceDownload): array
    {
        if (empty($invoiceId)) {
            throw new Exception('A számla azonosító megadása kötelező');
        }

        $xml = $this->getXmlHeader('xmlszamlast', 'agentst') .
            $this->wrapWithElement(
                'beallitasok', [
                    ...$this->getAuthFields(),
                    ['eszamla', (string)$eInvoice],
                    ['szamlaLetoltes', (string)$requestInvoiceDownload],
                ]) .
            $this->wrapWithElement(
                'fejlec', [
                    ['szamlaszam', $invoiceId],
                    ['keltDatum', date('Y-m-d')],
                ]) .
            '</xmlszamlast>';

        $response = $this->sendRequest('action-szamla_agent_st', $xml, true);
        
        if (!$response['success']) {
            throw new Exception($response['errorMessage'] ?? 'Hiba történt a számla sztornózásakor');
        }

        $data = [
            'invoiceId' => $response['headers']['szlahu_szamlaszam'] ?? null,
            'netTotal' => $response['headers']['szlahu_nettovegosszeg'] ?? null,
            'grossTotal' => $response['headers']['szlahu_bruttovegosszeg'] ?? null,
            'customerAccountUrl' => $response['headers']['szlahu_vevoifiokurl'] ?? null
        ];

        if ($requestInvoiceDownload && isset($response['data'])) {
            $data['pdf'] = $response['data'];
        }

        return $data;
    }

    /**
     * Számla kiállítása
     * 
     * @param Invoice $invoice A kiállítandó számla objektum
     * @return array A kiállított számla adatai
     * @throws Exception Ha hiba történt a számla kiállításakor
     */
    public function issueInvoice(Invoice $invoice): array
    {
        $xml = $this->getXmlHeader('xmlszamla', 'agent') .
            $this->wrapWithElement('beallitasok', [
                ...$this->getAuthFields(),
                ['eszamla', (string)$this->eInvoice],
                ['szamlaLetoltes', (string)$this->requestInvoiceDownload],
                ['szamlaLetoltesPld', (string)$this->downloadedInvoiceCount],
                ['valaszVerzio', (string)$this->responseVersion]
            ]) .
            $invoice->generateXML() .
            '</xmlszamla>';

        $response = $this->sendRequest('action-xmlagentxmlfile', $xml, $this->responseVersion === 1);
        
        if (!$response['success']) {
            throw new Exception($response['errorMessage'] ?? 'Hiba történt a számla kiállításakor');
        }

        $data = [
            'invoiceId' => $response['headers']['szlahu_szamlaszam'] ?? null,
            'netTotal' => $response['headers']['szlahu_nettovegosszeg'] ?? null,
            'grossTotal' => $response['headers']['szlahu_bruttovegosszeg'] ?? null,
            'customerAccountUrl' => $response['headers']['szlahu_vevoifiokurl'] ?? null,
        ];

        if ($this->requestInvoiceDownload && isset($response['data'])) {
            if ($this->responseVersion === 1) {
                $data['pdf'] = $response['data'];
            } else {
                $parsed = $this->parseSimpleXmlToArray($response['data']);
                $data['pdf'] = base64_decode($parsed['pdf'] ?? '');
            }
        }
        
        return $data;
    }
    
    /**
     * Számla letöltése PDF formátumban
     * 
     * @param string $invoiceId Számla azonosító
     * @param ResponseVersion $responseVersion Válasz verzió
     * @param string|null $externalId Külső azonosító
     * @return DownloadInvoiceResponse A letöltött számla adatai
     * @throws Exception Ha hiányos az azonosítás vagy hiba történt a letöltéskor
     */
    public function downloadInvoicePdf(string $invoiceId, ResponseVersion $responseVersion = ResponseVersion::PDF, ?string $externalId = null): DownloadInvoiceResponse
    {
        if (empty($invoiceId) && empty($externalId)) {
            throw new Exception('A számla azonosító vagy a külső azonosító megadása kötelező');
        }
        
        $xml = $this->getXmlHeader('xmlszamlapdf', 'agentpdf') .
            $this->wrapWithElement([
                ...$this->getAuthFields(),
                ['szamlaszam', $invoiceId],
                ['valaszVerzio', (string)$responseVersion->value],
                ['szamlaKulsoAzon', $externalId ?? ''],
            ]) .
            '</xmlszamlapdf>';
            
        $response = $this->sendRequest('action-szamla_agent_pdf', $xml, $responseVersion === ResponseVersion::PDF);
        
        if (!$response['success']) {
            return (new DownloadInvoiceResponse(false))
                ->setErrorMessage($response['errorMessage'] ?? 'Hiba történt a számla PDF letöltésekor')
                ->setErrorCode($response['errorCode'] ?? null);
        }
        
        $downloadResponse = new DownloadInvoiceResponse(true);
        
        if ($responseVersion === ResponseVersion::PDF) {
            $downloadResponse->setPdf($response['data']);
        } else {
            $parsed = $this->parseSimpleXmlToArray($response['data']);
            if (isset($parsed['sikeres']) && $parsed['sikeres'] === 'false') {
                return (new DownloadInvoiceResponse(false))
                    ->setErrorCode($parsed['hibakod'] ?? null)
                    ->setErrorMessage($parsed['hibauzenet'] ?? 'Ismeretlen hiba történt a számla letöltésekor');
            }
            
            $downloadResponse
                ->setInvoiceId($parsed['szamlaszam'] ?? null)
                ->setNetTotal($parsed['szamlanetto'] ?? null)
                ->setGrossTotal($parsed['szamlabrutto'] ?? null)
                ->setPdf(base64_decode($parsed['pdf'] ?? ''));
        }
        
        return $downloadResponse;
    }

    /**
     * Beállítja, hogy kérje-e a számla PDF letöltését
     * 
     * @param bool $value Kérjen-e PDF letöltést
     * @return void
     */
    public function setRequestInvoiceDownload(bool $value): void
    {
        $this->requestInvoiceDownload = $value;
    }

    /**
     * XML fejléc előállítása
     * 
     * @param string $tag Az XML gyökérelem neve
     * @param string $dir Az XML séma könyvtára
     * @return string Az XML fejléc
     */
    protected function getXmlHeader(string $tag, string $dir): string
    {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <{$tag} xmlns=\"http://www.szamlazz.hu/{$tag}\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
        xsi:schemaLocation=\"http://www.szamlazz.hu/{$tag} https://www.szamlazz.hu/szamla/docs/xsds/{$dir}/{$tag}.xsd\">\n";
    }

    /**
     * XML elemek becsomagolása
     * 
     * @param string|array $elements XML elemek tömb formában vagy a gyökérelem neve
     * @param array|null $subElements Alelemek tömb formában, ha a $elements a gyökérelem neve
     * @return string A becsomagolt XML elemek
     */
    protected function wrapWithElement($elements, ?array $subElements = null): string
    {
        if (is_string($elements) && $subElements !== null) {
            $output = "<{$elements}>\n";
            foreach ($subElements as $element) {
                if (!empty($element[1]) || $element[1] === '0' || $element[1] === false) {
                    $value = is_bool($element[1]) ? ($element[1] ? 'true' : 'false') : $element[1];
                    if ($value instanceof \DateTime) {
                        $value = $value->format('Y-m-d');
                    }
                    $output .= "  <{$element[0]}>{$value}</{$element[0]}>\n";
                }
            }
            $output .= "</{$elements}>\n";
            return $output;
        }

        $output = "";
        foreach ($elements as $element) {
            if (!empty($element[1]) || $element[1] === '0' || $element[1] === false) {
                $value = is_bool($element[1]) ? ($element[1] ? 'true' : 'false') : $element[1];
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d');
                }
                $output .= "<{$element[0]}>{$value}</{$element[0]}>\n";
            }
        }
        return $output;
    }

    /**
     * HTTP kérés küldése
     * 
     * @param string $fileFieldName A fájl mező neve
     * @param string $data Az XML adat
     * @param bool $isBinaryDownload Bináris letöltés-e
     * @return array A válasz adatok
     */
    protected function sendRequest(string $fileFieldName, string $data, bool $isBinaryDownload = false): array
    {
        $multipart = [
            [
                'name' => $fileFieldName,
                'contents' => $data,
                'filename' => 'request.xml'
            ]
        ];

        try {
            $response = $this->httpClient->post('https://www.szamlazz.hu/szamla/', [
                'multipart' => $multipart,
                'headers' => [
                    'Accept' => 'application/xml'
                ],
                'http_errors' => false
            ]);

            if ($response->getStatusCode() !== 200) {
                return [
                    'success' => false,
                    'errorMessage' => 'HTTP hiba: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase()
                ];
            }

            $headers = $response->getHeaders();
            $flatHeaders = [];

            foreach ($headers as $name => $values) {
                $flatHeaders[strtolower($name)] = $values[0];
            }

            if (isset($flatHeaders['szlahu_error_code'])) {
                return [
                    'success' => false,
                    'errorMessage' => urldecode(str_replace('+', ' ', $flatHeaders['szlahu_error'])),
                    'errorCode' => $flatHeaders['szlahu_error_code']
                ];
            }

            $responseBody = (string)$response->getBody();

            if (!$isBinaryDownload && !empty($responseBody)) {
                $xml = simplexml_load_string($responseBody);
                
                if ($xml && isset($xml->hibakod)) {
                    return [
                        'success' => false,
                        'errorMessage' => (string)$xml->hibauzenet,
                        'errorCode' => (string)$xml->hibakod
                    ];
                }
            }

            return [
                'success' => true,
                'data' => $responseBody,
                'headers' => $flatHeaders
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'errorMessage' => 'Hiba a kérés küldése közben: ' . $e->getMessage()
            ];
        }
    }

    /**
     * SimpleXML objektum átalakítása tömbbé
     * 
     * @param string|SimpleXMLElement $xml Az XML string vagy objektum
     * @return array A tömbbé alakított XML
     */
    protected function parseSimpleXmlToArray($xml): array
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        }
        
        if (!$xml instanceof SimpleXMLElement) {
            return [];
        }

        $json = json_encode($xml);
        return json_decode($json, true);
    }
} 