<?php

namespace Szamlazzphp;

use Exception;
use Szamlazzphp\Enum\Currency;
use Szamlazzphp\Enum\Language;
use Szamlazzphp\Enum\PaymentMethod;
use DateTime;

class Invoice
{
    private DateTime $issueDate;
    private DateTime $fulfillmentDate;
    private DateTime $dueDate;
    private PaymentMethod $paymentMethod;
    private Currency $currency;
    private Language $language;
    private float $exchangeRate;
    private string $exchangeBank;
    private ?Seller $seller;
    private Buyer $buyer;
    private array $items;
    private ?string $orderNumber;
    private ?bool $noNavReport;
    private ?bool $proforma;
    private ?string $invoiceIdPrefix;
    private ?bool $paid;
    private ?string $comment;
    private ?string $logoImage;
    private ?string $adjustmentInvoiceNumber;
    private bool $prepaymentInvoice;
    private ?bool $adjustmentInvoice;

    /**
     * Invoice konstruktor
     * 
     * @param Buyer $buyer A vevő adatai
     * @param array $items A számla tételei
     * @param DateTime|null $issueDate Számla kelte
     * @param DateTime|null $fulfillmentDate Teljesítés dátuma
     * @param DateTime|null $dueDate Fizetési határidő
     * @param PaymentMethod|null $paymentMethod Fizetési mód
     * @param Currency|null $currency Pénznem
     * @param Language|null $language Számla nyelve
     */
    public function __construct(
        Buyer $buyer,
        array $items,
        ?DateTime $issueDate = null,
        ?DateTime $fulfillmentDate = null,
        ?DateTime $dueDate = null,
        ?PaymentMethod $paymentMethod = null,
        ?Currency $currency = null,
        ?Language $language = null
    ) {
        $this->buyer = $buyer;
        $this->items = $items;
        $this->issueDate = $issueDate ?? new DateTime();
        $this->fulfillmentDate = $fulfillmentDate ?? new DateTime();
        $this->dueDate = $dueDate ?? new DateTime();
        $this->paymentMethod = $paymentMethod ?? PaymentMethod::BankTransfer;
        $this->currency = $currency ?? Currency::Ft;
        $this->language = $language ?? Language::Hungarian;
        $this->exchangeRate = 0;
        $this->exchangeBank = '';
        $this->seller = null;
        $this->orderNumber = null;
        $this->noNavReport = null;
        $this->proforma = null;
        $this->invoiceIdPrefix = null;
        $this->paid = null;
        $this->comment = null;
        $this->logoImage = null;
        $this->adjustmentInvoiceNumber = null;
        $this->prepaymentInvoice = false;
        $this->adjustmentInvoice = null;
    }

    /**
     * Eladó beállítása
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
        $this->adjustmentInvoice = true;
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
     * XML generálása
     * 
     * @param int $indentLevel Behúzási szint
     * @return string A generált XML
     */
    public function generateXML(int $indentLevel = 0): string
    {
        if (!$this->issueDate instanceof DateTime) {
            throw new Exception('Érvényes kiállítási dátum hiányzik');
        }

        if (!$this->fulfillmentDate instanceof DateTime) {
            throw new Exception('Érvényes teljesítési dátum hiányzik');
        }

        if (!$this->dueDate instanceof DateTime) {
            throw new Exception('Érvényes fizetési határidő hiányzik');
        }

        if (!$this->paymentMethod instanceof PaymentMethod) {
            throw new Exception('Érvényes fizetési mód hiányzik');
        }

        if (!$this->currency instanceof Currency) {
            throw new Exception('Érvényes pénznem hiányzik');
        }

        if (!$this->language instanceof Language) {
            throw new Exception('Érvényes nyelv hiányzik');
        }

        if ($this->seller !== null && !$this->seller instanceof Seller) {
            throw new Exception('Az opcionális eladó érvénytelen');
        }

        if (!$this->buyer instanceof Buyer) {
            throw new Exception('Érvényes vevő hiányzik');
        }

        if (!is_array($this->items) || empty($this->items)) {
            throw new Exception('Érvényes tételek hiányoznak');
        }

        if ($this->adjustmentInvoiceNumber !== null) {
            if (!is_string($this->adjustmentInvoiceNumber)) {
                throw new Exception('A helyesbített számla száma szöveg típusú kell legyen');
            }
            if (strlen($this->adjustmentInvoiceNumber) === 0) {
                throw new Exception('A helyesbített számla száma legalább 1 karakter hosszú kell legyen');
            }
        }

        $pad = str_repeat('  ', $indentLevel);
        
        $output = $this->wrapWithElement('fejlec', [
            ['keltDatum', $this->issueDate],
            ['teljesitesDatum', $this->fulfillmentDate],
            ['fizetesiHataridoDatum', $this->dueDate],
            ['fizmod', $this->paymentMethod->value],
            ['penznem', $this->currency->value],
            ['szamlaNyelve', $this->language->value],
            ['megjegyzes', $this->comment],
            ['arfolyamBank', $this->exchangeBank],
            ['arfolyam', $this->exchangeRate],
            ['rendelesSzam', $this->orderNumber],
            ['elolegszamla', $this->prepaymentInvoice],
            ['helyesbitoszamla', $this->adjustmentInvoice],
            ['helyesbitettSzamlaszam', $this->adjustmentInvoiceNumber],
            ['dijbekero', $this->proforma],
            ['logoExtra', $this->logoImage],
            ['szamlaszamElotag', $this->invoiceIdPrefix],
            ['fizetve', $this->paid],
            ['eusAfa', $this->noNavReport],
        ], $indentLevel);

        if ($this->seller) {
            $output .= $this->seller->generateXML($indentLevel);
        }

        $output .= $this->buyer->generateXML($indentLevel);

        $output .= $pad . "<tetelek>\n";
        foreach ($this->items as $item) {
            if (!$item instanceof Item) {
                throw new Exception('A tételek egyike nem Item példány');
            }
            $output .= $item->generateXML($indentLevel + 1, $this->currency);
        }
        $output .= $pad . "</tetelek>\n";

        return $output;
    }

    /**
     * XML elemek becsomagolása
     * 
     * @param string $element A gyökérelem neve
     * @param array $subElements Az alelemek tömbbel
     * @param int $indentLevel Behúzási szint
     * @return string A becsomagolt XML
     */
    private function wrapWithElement(string $element, array $subElements, int $indentLevel = 0): string
    {
        $pad = str_repeat('  ', $indentLevel);
        $output = $pad . "<{$element}>\n";
        
        $innerPad = str_repeat('  ', $indentLevel + 1);
        
        foreach ($subElements as $subElement) {
            if (!empty($subElement[1]) || $subElement[1] === '0' || $subElement[1] === false) {
                $value = $subElement[1];
                
                if ($value instanceof DateTime) {
                    $value = $value->format('Y-m-d');
                } elseif (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }
                
                $output .= $innerPad . "<{$subElement[0]}>{$value}</{$subElement[0]}>\n";
            }
        }
        
        $output .= $pad . "</{$element}>\n";
        
        return $output;
    }
} 