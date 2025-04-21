<?php

namespace Szamlazzphp\Client;

use Exception;

/**
 * Számlázz.hu API kulcsos kliens
 */
class SzamlaAgentClient extends BaseClient implements ClientInterface
{
    private string $authToken;

    /**
     * SzamlaAgentClient konstruktor
     * 
     * @param string $authToken Számlázz.hu API kulcs
     * @param bool $eInvoice E-számla generálása
     * @param bool $requestInvoiceDownload Számla letöltése PDF-ben
     * @param int $downloadedInvoiceCount Letöltendő példányszám
     * @param int $responseVersion Válasz verzió
     * @param int $timeout Kérés időtúllépés másodpercben
     * @throws Exception Ha hiányzik az API kulcs
     */
    public function __construct(
        string $authToken,
        bool $eInvoice = false,
        bool $requestInvoiceDownload = false,
        int $downloadedInvoiceCount = 1,
        int $responseVersion = 1,
        int $timeout = 0
    ) {
        if (empty($authToken)) {
            throw new Exception('Az API kulcs megadása kötelező');
        }
        
        $this->authToken = $authToken;
        
        parent::__construct(
            $eInvoice,
            $requestInvoiceDownload,
            $downloadedInvoiceCount,
            $responseVersion,
            $timeout
        );
    }

    /**
     * Autentikációs mezők lekérése
     * 
     * @return array Az autentikációs mezők
     */
    protected function getAuthFields(): array
    {
        return [
            ['szamlaagentkulcs', $this->authToken],
        ];
    }
} 