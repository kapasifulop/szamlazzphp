<?php

namespace Szamlazzphp\Enum;

enum TaxSubject: int
{
    case NonEUCompany = 7;
    case EUCompany = 6;
    case HungarianTaxID = 1;
    case Unknown = 0;
    case NoTaxID = -1;

    public function getComment(): string 
    {
        return match($this) {
            self::NonEUCompany => 'Company outside EU',
            self::EUCompany => 'Company within EU',
            self::HungarianTaxID => 'Has Hungarian VAT ID',
            self::Unknown => 'Unknown VAT status',
            self::NoTaxID => 'Has no Hungarian VAT ID'
        };
    }
} 