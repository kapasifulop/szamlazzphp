<?php

namespace Szamlazzphp\Client;

use Exception;

/**
 * Számlázz.hu felhasználónév-jelszó alapú kliens
 */
class AuthBasedClient extends BaseClient implements ClientInterface
{
    private string $username;
    private string $password;

    /**
     * AuthBasedClient konstruktor
     * 
     * @param string $username Számlázz.hu felhasználónév
     * @param string $password Számlázz.hu jelszó
     * @param bool $eInvoice E-számla generálása
     * @param bool $requestInvoiceDownload Számla letöltése PDF-ben
     * @param int $downloadedInvoiceCount Letöltendő példányszám
     * @param int $responseVersion Válasz verzió
     * @param int $timeout Kérés időtúllépés másodpercben
     * @throws Exception Ha hiányzik a felhasználónév vagy a jelszó
     */
    public function __construct(
        string $username,
        string $password,
        bool $eInvoice = false,
        bool $requestInvoiceDownload = false,
        int $downloadedInvoiceCount = 1,
        int $responseVersion = 1,
        int $timeout = 0
    ) {
        if (empty($username) || empty($password)) {
            throw new Exception('A felhasználónév és jelszó megadása kötelező');
        }
        
        $this->username = $username;
        $this->password = $password;
        
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
            ['felhasznalo', $this->username],
            ['jelszo', $this->password],
        ];
    }
} 