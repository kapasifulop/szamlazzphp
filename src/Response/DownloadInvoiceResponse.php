<?php

namespace Szamlazzphp\Response;

/**
 * Számla letöltési válasz
 */
class DownloadInvoiceResponse
{
    /**
     * Sikeres letöltés
     */
    private bool $success;
    
    /**
     * Hiba kód
     */
    private ?string $errorCode = null;
    
    /**
     * Hibaüzenet
     */
    private ?string $errorMessage = null;
    
    /**
     * Számla azonosító
     */
    private ?string $invoiceId = null;
    
    /**
     * Számla nettó összege
     */
    private ?string $netTotal = null;
    
    /**
     * Számla bruttó összege
     */
    private ?string $grossTotal = null;
    
    /**
     * PDF adatok
     */
    private ?string $pdf = null;
    
    /**
     * DownloadInvoiceResponse konstruktor
     * 
     * @param bool $success Sikeres letöltés
     */
    public function __construct(bool $success = true)
    {
        $this->success = $success;
    }
    
    /**
     * Válasz létrehozása tömbből
     * 
     * @param array $data Válasz adatok
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $response = new self($data['success'] ?? false);
        
        if (isset($data['errorCode'])) {
            $response->setErrorCode($data['errorCode']);
        }
        
        if (isset($data['errorMessage'])) {
            $response->setErrorMessage($data['errorMessage']);
        }
        
        if (isset($data['invoiceId'])) {
            $response->setInvoiceId($data['invoiceId']);
        }
        
        if (isset($data['netTotal'])) {
            $response->setNetTotal($data['netTotal']);
        }
        
        if (isset($data['grossTotal'])) {
            $response->setGrossTotal($data['grossTotal']);
        }
        
        if (isset($data['pdf'])) {
            $response->setPdf($data['pdf']);
        }
        
        return $response;
    }
    
    /**
     * Sikeres-e a letöltés
     * 
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }
    
    /**
     * Hiba kód lekérdezése
     * 
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
    
    /**
     * Hiba kód beállítása
     * 
     * @param string|null $errorCode Hiba kód
     * @return self
     */
    public function setErrorCode(?string $errorCode): self
    {
        $this->errorCode = $errorCode;
        return $this;
    }
    
    /**
     * Hibaüzenet lekérdezése
     * 
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
    
    /**
     * Hibaüzenet beállítása
     * 
     * @param string|null $errorMessage Hibaüzenet
     * @return self
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
    
    /**
     * Számla azonosító lekérdezése
     * 
     * @return string|null
     */
    public function getInvoiceId(): ?string
    {
        return $this->invoiceId;
    }
    
    /**
     * Számla azonosító beállítása
     * 
     * @param string|null $invoiceId Számla azonosító
     * @return self
     */
    public function setInvoiceId(?string $invoiceId): self
    {
        $this->invoiceId = $invoiceId;
        return $this;
    }
    
    /**
     * Számla nettó összegének lekérdezése
     * 
     * @return string|null
     */
    public function getNetTotal(): ?string
    {
        return $this->netTotal;
    }
    
    /**
     * Számla nettó összegének beállítása
     * 
     * @param string|null $netTotal Számla nettó összege
     * @return self
     */
    public function setNetTotal(?string $netTotal): self
    {
        $this->netTotal = $netTotal;
        return $this;
    }
    
    /**
     * Számla bruttó összegének lekérdezése
     * 
     * @return string|null
     */
    public function getGrossTotal(): ?string
    {
        return $this->grossTotal;
    }
    
    /**
     * Számla bruttó összegének beállítása
     * 
     * @param string|null $grossTotal Számla bruttó összege
     * @return self
     */
    public function setGrossTotal(?string $grossTotal): self
    {
        $this->grossTotal = $grossTotal;
        return $this;
    }
    
    /**
     * PDF adatok lekérdezése
     * 
     * @return string|null
     */
    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    /**
     * PDF fájl mentése
     * 
     * @param string $filename Fájl neve
     * @return self
     */
    public function savePdf(string $filename): self
    {
        if ($this->pdf) {
            file_put_contents($filename, $this->pdf);
        } else {
            throw new \Exception('Nincs PDF adat a válaszban!');
        }
        return $this;
    }

    /**
     * PDF fájl mentése
     * 
     * @param string $filename Fájl neve
     * @return self
     */
    public function storePdf(string $filename): self
    {
        return $this->savePdf($filename);
    }
    
    /** 
     * PDF beállítása
     * 
     * @param string|null $pdf PDF adatok
     * @return self
     */
    public function setPdf(?string $pdf): self
    {
        $this->pdf = $pdf;
        return $this;
    }
} 