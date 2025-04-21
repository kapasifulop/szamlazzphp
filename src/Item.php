<?php

namespace Szamlazzphp;

use Exception;
use Szamlazzphp\Enum\Currency;

class Item
{
    private array $options;

    /**
     * Item konstruktor
     * 
     * @param array $options A tétel adatai
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * XML generálása
     * 
     * @param int $indentLevel Behúzási szint
     * @param Currency $currency A számla pénzneme
     * @return string A generált XML
     */
    public function generateXML(int $indentLevel = 0, Currency|null $currency = null): string
    {
        $pad = str_repeat('  ', $indentLevel);
        $innerPad = str_repeat('  ', $indentLevel + 1);
        
        if (empty($this->options['label'])) {
            throw new Exception('A tétel nevének megadása kötelező');
        }
        
        if (empty($this->options['quantity']) && $this->options['quantity'] !== 0) {
            throw new Exception('A tétel mennyiségének megadása kötelező');
        }
        
        if (empty($this->options['unit'])) {
            throw new Exception('A mennyiségi egység megadása kötelező');
        }
        
        if (!isset($this->options['vat'])) {
            throw new Exception('Az ÁFA százalék megadása kötelező');
        }
        
        if (empty($this->options['netUnitPrice']) && $this->options['netUnitPrice'] !== 0) {
            throw new Exception('A nettó egységár megadása kötelező');
        }
        
        $vatValue = is_numeric($this->options['vat']) ? $this->options['vat'] : (is_string($this->options['vat']) ? $this->options['vat'] : '');
        $roundPriceExp = $currency ? $currency->getRoundPriceExp() : 0;
        
        $output = $pad . "<tetel>\n";
        
        $output .= $innerPad . "<megnevezes>{$this->options['label']}</megnevezes>\n";
        $output .= $innerPad . "<mennyiseg>{$this->options['quantity']}</mennyiseg>\n";
        $output .= $innerPad . "<mennyisegiEgyseg>{$this->options['unit']}</mennyisegiEgyseg>\n";
        $output .= $innerPad . "<nettoEgysegar>" . $this->formatPrice($this->options['netUnitPrice'], $roundPriceExp) . "</nettoEgysegar>\n";
        $output .= $innerPad . "<afakulcs>{$vatValue}</afakulcs>\n";
        
        if (!empty($this->options['netAmount'])) {
            $output .= $innerPad . "<nettoErtek>" . $this->formatPrice($this->options['netAmount'], $roundPriceExp) . "</nettoErtek>\n";
        }
        
        if (!empty($this->options['vatAmount'])) {
            $output .= $innerPad . "<afaErtek>" . $this->formatPrice($this->options['vatAmount'], $roundPriceExp) . "</afaErtek>\n";
        }
        
        if (!empty($this->options['grossAmount'])) {
            $output .= $innerPad . "<bruttoErtek>" . $this->formatPrice($this->options['grossAmount'], $roundPriceExp) . "</bruttoErtek>\n";
        }
        
        if (!empty($this->options['comment'])) {
            $output .= $innerPad . "<megjegyzes>{$this->options['comment']}</megjegyzes>\n";
        }

        $output .= $pad . "</tetel>\n";
        
        return $output;
    }

    /**
     * Ár formázása a megadott kerekítési beállítással
     *
     * @param float $price Az ár
     * @param int $exp A kerekítés helyiértéke (10^-exp)
     * @return string A formázott ár
     */
    private function formatPrice(float $price, int $exp): string
    {
        if ($exp === 0) {
            return (string)round($price);
        }
        
        return number_format(round($price, $exp), $exp, '.', '');
    }
} 