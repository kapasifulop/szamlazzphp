<?php

namespace Szamlazzphp;

use Exception;
use Szamlazzphp\Enum\TaxSubject;

class Buyer
{
    private array $options;

    /**
     * Buyer konstruktor
     * 
     * @param array $options A vevő adatai
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * XML generálása
     * 
     * @param int $indentLevel Behúzási szint
     * @return string A generált XML
     */
    public function generateXML(int $indentLevel = 0): string
    {
        $pad = str_repeat('  ', $indentLevel);
        $innerPad = str_repeat('  ', $indentLevel + 1);
        
        if (empty($this->options['name'])) {
            throw new Exception('A vevő nevének megadása kötelező');
        }

        $output = $pad . "<vevo>\n";
        
        // Alap adatok
        $output .= $innerPad . "<nev>{$this->options['name']}</nev>\n";
        
        if (!empty($this->options['country'])) {
            $output .= $innerPad . "<orszag>{$this->options['country']}</orszag>\n";
        }
        
        if (!empty($this->options['zip'])) {
            $output .= $innerPad . "<irsz>{$this->options['zip']}</irsz>\n";
        }
        
        if (!empty($this->options['city'])) {
            $output .= $innerPad . "<telepules>{$this->options['city']}</telepules>\n";
        }
        
        if (!empty($this->options['address'])) {
            $output .= $innerPad . "<cim>{$this->options['address']}</cim>\n";
        }
        
        // Email
        if (!empty($this->options['email'])) {
            $output .= $innerPad . "<email>{$this->options['email']}</email>\n";
        }

        // Email küldése
        if(!empty($this->options['sendEmail'])) {
            if($this->options['sendEmail'] == true) {
                $output .= $innerPad . "<sendEmail>true</sendEmail>\n";
            } else {
                $output .= $innerPad . "<sendEmail>false</sendEmail>\n";
            }
        }
        
        // Telefonszám
        if (!empty($this->options['phone'])) {
            $output .= $innerPad . "<telefon>{$this->options['phone']}</telefon>\n";
        }
        
        // Adószám
        if (!empty($this->options['taxNumber'])) {
            $output .= $innerPad . "<adoszam>{$this->options['taxNumber']}</adoszam>\n";
        }
        
        // EU adószám
        if (!empty($this->options['taxNumberEU'])) {
            $output .= $innerPad . "<adoszamEU>{$this->options['taxNumberEU']}</adoszamEU>\n";
        }
        
        // Csoportos adószám
        if (!empty($this->options['groupTaxNumber'])) {
            $output .= $innerPad . "<csoportadoszam>{$this->options['groupTaxNumber']}</csoportadoszam>\n";
        }
        
        // Adóalanyiság
        if (!empty($this->options['taxSubject'])) {
            if ($this->options['taxSubject'] instanceof TaxSubject) {
                $output .= $innerPad . "<adoalany>{$this->options['taxSubject']->value}</adoalany>\n";
            } else {
                $output .= $innerPad . "<adoalany>{$this->options['taxSubject']}</adoalany>\n";
            }
        }
        
        // Szállítási név
        if (!empty($this->options['shippingName'])) {
            $output .= $innerPad . "<szallnev>{$this->options['shippingName']}</szallnev>\n";
        }
        
        // Szállítási ország
        if (!empty($this->options['shippingCountry'])) {
            $output .= $innerPad . "<szallorszag>{$this->options['shippingCountry']}</szallorszag>\n";
        }
        
        // Szállítási irányítószám
        if (!empty($this->options['shippingZip'])) {
            $output .= $innerPad . "<szallirsz>{$this->options['shippingZip']}</szallirsz>\n";
        }
        
        // Szállítási település
        if (!empty($this->options['shippingCity'])) {
            $output .= $innerPad . "<szalltelepules>{$this->options['shippingCity']}</szalltelepules>\n";
        }
        
        // Szállítási cím
        if (!empty($this->options['shippingAddress'])) {
            $output .= $innerPad . "<szallcim>{$this->options['shippingAddress']}</szallcim>\n";
        }
        
        // Vevő megjegyzés
        if (!empty($this->options['comment'])) {
            $output .= $innerPad . "<vevomegj>{$this->options['comment']}</vevomegj>\n";
        }
        
        $output .= $pad . "</vevo>\n";
        
        return $output;
    }
} 