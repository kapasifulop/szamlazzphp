<?php

namespace Szamlazzphp\Enum;

enum Currency: string
{
    case Ft = 'Ft';
    case HUF = 'HUF';
    case EUR = 'EUR';
    case CHF = 'CHF';
    case USD = 'USD';
    case AUD = 'AUD';
    case AED = 'AED';
    case BGN = 'BGN';
    case CAD = 'CAD';
    case CNY = 'CNY';
    case CZK = 'CZK';
    case DKK = 'DKK';
    case EEK = 'EEK';
    case GBP = 'GBP';
    case HRK = 'HRK';
    case ISK = 'ISK';
    case JPY = 'JPY';
    case LTL = 'LTL';
    case LVL = 'LVL';
    case NOK = 'NOK';
    case NZD = 'NZD';
    case PLN = 'PLN';
    case RON = 'RON';
    case RUB = 'RUB';
    case SEK = 'SEK';
    case SKK = 'SKK';
    case UAH = 'UAH';

    public function getRoundPriceExp(): int
    {
        return match($this) {
            self::Ft, self::HUF => 0,
            default => 2
        };
    }

    public function getComment(): string
    {
        return match($this) {
            self::Ft, self::HUF => 'Hungarian Forint',
            self::EUR => 'Euro',
            self::CHF => 'Swiss Franc',
            self::USD => 'US Dollar',
            self::AUD => 'Australian Dollar',
            self::AED => 'Emirati Dirham',
            self::BGN => 'Bulgarian Lev',
            self::CAD => 'Canadian Dollar',
            self::CNY => 'Chinese Yuan Renminbi',
            self::CZK => 'Czech Koruna',
            self::DKK => 'Danish Krone',
            self::EEK => 'Estonian Kroon',
            self::GBP => 'British Pound',
            self::HRK => 'Croatian Kuna',
            self::ISK => 'Icelandic Krona',
            self::JPY => 'Japanese Yen',
            self::LTL => 'Lithuanian Litas',
            self::LVL => 'Latvian Lats',
            self::NOK => 'Norwegian Krone',
            self::NZD => 'New Zealand Dollar',
            self::PLN => 'Polish Zloty',
            self::RON => 'Romanian New Leu',
            self::RUB => 'Russian Ruble',
            self::SEK => 'Swedish Krona',
            self::SKK => 'Slovak Koruna',
            self::UAH => 'Ukrainian Hryvnia'
        };
    }
} 