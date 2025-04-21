<?php

namespace Szamlazzphp;

class Seller
{
    private array $options;

    /**
     * Seller konstruktor
     * 
     * @param array $options Az eladó adatai
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
        
        $output = $pad . "<elado>\n";
        
        if (!empty($this->options['bank'])) {
            $output .= $innerPad . "<bank>{$this->options['bank']}</bank>\n";
        }
        
        if (!empty($this->options['bankAccount'])) {
            $output .= $innerPad . "<bankszamlaszam>{$this->options['bankAccount']}</bankszamlaszam>\n";
        }
        
        if (!empty($this->options['emailReplyTo'])) {
            $output .= $innerPad . "<emailReplyto>{$this->options['emailReplyTo']}</emailReplyto>\n";
        }
        
        if (!empty($this->options['emailSubject'])) {
            $output .= $innerPad . "<emailTargy>{$this->options['emailSubject']}</emailTargy>\n";
        }
        
        if (!empty($this->options['emailText'])) {
            $output .= $innerPad . "<emailSzoveg>{$this->options['emailText']}</emailSzoveg>\n";
        }
        
        $output .= $pad . "</elado>\n";
        
        return $output;
    }
} 