<?php

namespace Szamlazzphp\Response;

/**
 * Számla adat lekérdezési válasz
 */
class GetInvoiceDataResponse
{
    /**
     * Sikeres-e a lekérdezés
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
     * Számla adatok
     */
    private array $invoiceData = [];
    
    /**
     * PDF adatok
     */
    private ?string $pdf = null;
    
    /**
     * GetInvoiceDataResponse konstruktor
     * 
     * @param bool $success Sikeres lekérdezés
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
        
        if (isset($data['invoiceData'])) {
            $response->setInvoiceData($data['invoiceData']);
        }
        
        if (isset($data['pdf'])) {
            $response->setPdf($data['pdf']);
        }
        
        return $response;
    }
    
    /**
     * Sikeres-e a lekérdezés
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
     * Számla adatok lekérdezése
     * 
     * @return array
     */
    public function getInvoiceData(): array
    {
        return $this->invoiceData;
    }
    
    /**
     * Számla adatok beállítása
     * 
     * @param array $invoiceData Számla adatok
     * @return self
     */
    public function setInvoiceData(array $invoiceData): self
    {
        $this->invoiceData = $invoiceData;
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
     * PDF adatok beállítása
     * 
     * @param string|null $pdf PDF adatok
     * @return self
     */
    public function setPdf(?string $pdf): self
    {
        $this->pdf = $pdf;
        return $this;
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
} 