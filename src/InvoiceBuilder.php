<?php

namespace Szamlazzphp;

use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\Language;
use Szamlazzphp\Enum\PaymentMethod;
use DateTime;

/**
 * Számla építő osztály
 * 
 * Fluent interfész a számla építéséhez
 */
class InvoiceBuilder
{
    private Buyer $buyer;
    private array $items = [];
    private ?DateTime $issueDate = null;
    private ?DateTime $fulfillmentDate = null;
    private ?DateTime $dueDate = null;
    private ?PaymentMethod $paymentMethod = null;
    private ?Currency $currency = null;
    private ?Language $language = null;
    private ?Seller $seller = null;
    private ?string $orderNumber = null;
    private ?bool $noNavReport = null;
    private ?bool $proforma = null;
    private ?string $invoiceIdPrefix = null;
    private ?bool $paid = null;
    private ?string $comment = null;
    private ?string $logoImage = null;
    private ?string $adjustmentInvoiceNumber = null;
    private bool $prepaymentInvoice = false;
    private ?float $exchangeRate = null;
    private ?string $exchangeBank = null;

    /**
     * InvoiceBuilder konstruktor
     * 
     * @param Buyer $buyer A vevő adatai
     */
    public function __construct(Buyer $buyer)
    {
        $this->buyer = $buyer;
    }

    /**
     * Tétel hozzáadása
     * 
     * @param Item $item A számla tétel
     * @return self
     */
    public function addItem(Item $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Tételek beállítása
     * 
     * @param array $items A számla tételei
     * @return self
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Számla kelte
     * 
     * @param DateTime $issueDate Számla kelte
     * @return self
     */
    public function setIssueDate(DateTime $issueDate): self
    {
        $this->issueDate = $issueDate;
        return $this;
    }

    /**
     * Teljesítés dátuma
     * 
     * @param DateTime $fulfillmentDate Teljesítés dátuma
     * @return self
     */
    public function setFulfillmentDate(DateTime $fulfillmentDate): self
    {
        $this->fulfillmentDate = $fulfillmentDate;
        return $this;
    }

    /**
     * Fizetési határidő
     * 
     * @param DateTime $dueDate Fizetési határidő
     * @return self
     */
    public function setDueDate(DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    /**
     * Fizetési mód
     * 
     * @param PaymentMethod $paymentMethod Fizetési mód
     * @return self
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * Pénznem
     * 
     * @param Currency $currency Pénznem
     * @return self
     */
    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Számla nyelve
     * 
     * @param Language $language Számla nyelve
     * @return self
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Eladó
     * 
     * @param Seller $seller Az eladó
     * @return self
     */
    public function setSeller(Seller $seller): self
    {
        $this->seller = $seller;
        return $this;
    }

    /**
     * Rendelésszám beállítása
     * 
     * @param string $orderNumber A rendelésszám
     * @return self
     */
    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * NAV jelentés nélküli számla beállítása
     * 
     * @param bool $noNavReport NAV jelentés nélküli-e
     * @return self
     */
    public function setNoNavReport(bool $noNavReport): self
    {
        $this->noNavReport = $noNavReport;
        return $this;
    }

    /**
     * Díjbekérő beállítása
     * 
     * @param bool $proforma Díjbekérő-e
     * @return self
     */
    public function setProforma(bool $proforma): self
    {
        $this->proforma = $proforma;
        return $this;
    }

    /**
     * Számlaszám előtag beállítása
     * 
     * @param string $invoiceIdPrefix A számlaszám előtag
     * @return self
     */
    public function setInvoiceIdPrefix(string $invoiceIdPrefix): self
    {
        $this->invoiceIdPrefix = $invoiceIdPrefix;
        return $this;
    }

    /**
     * Fizetve állapot beállítása
     * 
     * @param bool $paid Fizetve van-e
     * @return self
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;
        return $this;
    }

    /**
     * Megjegyzés beállítása
     * 
     * @param string $comment A megjegyzés
     * @return self
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Logó kép beállítása
     * 
     * @param string $logoImage A logó kép
     * @return self
     */
    public function setLogoImage(string $logoImage): self
    {
        $this->logoImage = $logoImage;
        return $this;
    }

    /**
     * Helyesbített számla számának beállítása
     * 
     * @param string $adjustmentInvoiceNumber A helyesbített számla száma
     * @return self
     */
    public function setAdjustmentInvoiceNumber(string $adjustmentInvoiceNumber): self
    {
        $this->adjustmentInvoiceNumber = $adjustmentInvoiceNumber;
        return $this;
    }

    /**
     * Előlegszámla beállítása
     * 
     * @param bool $prepaymentInvoice Előlegszámla-e
     * @return self
     */
    public function setPrepaymentInvoice(bool $prepaymentInvoice): self
    {
        $this->prepaymentInvoice = $prepaymentInvoice;
        return $this;
    }

    /**
     * Árfolyambank beállítása
     * 
     * @param string $exchangeBank Az árfolyambank neve
     * @return self
     */
    public function setExchangeBank(string $exchangeBank): self
    {
        $this->exchangeBank = $exchangeBank;
        return $this;
    }

    /**
     * Árfolyam beállítása
     * 
     * @param float $exchangeRate Az árfolyam
     * @return self
     */
    public function setExchangeRate(float $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * Számla létrehozása a beállított adatokkal
     * 
     * @return Invoice
     * @throws \Exception Ha a kötelező adatok hiányoznak
     */
    public function build(): Invoice
    {
        if (empty($this->items)) {
            throw new \Exception('Legalább egy számlatétel megadása kötelező');
        }

        $invoice = new Invoice(
            $this->buyer, 
            $this->items,
            $this->issueDate,
            $this->fulfillmentDate,
            $this->dueDate,
            $this->paymentMethod,
            $this->currency,
            $this->language
        );
        
        if ($this->seller !== null) {
            $invoice->setSeller($this->seller);
        }
        
        if ($this->orderNumber !== null) {
            $invoice->setOrderNumber($this->orderNumber);
        }
        
        if ($this->noNavReport !== null) {
            $invoice->setNoNavReport($this->noNavReport);
        }
        
        if ($this->proforma !== null) {
            $invoice->setProforma($this->proforma);
        }
        
        if ($this->invoiceIdPrefix !== null) {
            $invoice->setInvoiceIdPrefix($this->invoiceIdPrefix);
        }
        
        if ($this->paid !== null) {
            $invoice->setPaid($this->paid);
        }
        
        if ($this->comment !== null) {
            $invoice->setComment($this->comment);
        }
        
        if ($this->logoImage !== null) {
            $invoice->setLogoImage($this->logoImage);
        }
        
        if ($this->adjustmentInvoiceNumber !== null) {
            $invoice->setAdjustmentInvoiceNumber($this->adjustmentInvoiceNumber);
        }
        
        if ($this->prepaymentInvoice) {
            $invoice->setPrepaymentInvoice($this->prepaymentInvoice);
        }
        
        if ($this->exchangeBank !== null) {
            $invoice->setExchangeBank($this->exchangeBank);
        }
        
        if ($this->exchangeRate !== null) {
            $invoice->setExchangeRate($this->exchangeRate);
        }
        
        return $invoice;
    }
} 