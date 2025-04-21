<?php

namespace Szamlazzphp\Enum;

enum Language: string
{
    case Hungarian = 'hu';
    case English = 'en';
    case German = 'de';
    case Italian = 'it';
    case Romanian = 'ro';
    case Slovak = 'sk';

    public function getName(): string
    {
        return match($this) {
            self::Hungarian => 'Hungarian',
            self::English => 'English',
            self::German => 'German',
            self::Italian => 'Italian',
            self::Romanian => 'Romanian',
            self::Slovak => 'Slovak'
        };
    }

    public function __toString(): string
    {
        return $this->getName() . ' (' . $this->value . ')';
    }
} 