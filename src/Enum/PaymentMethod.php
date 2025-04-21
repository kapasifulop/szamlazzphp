<?php

namespace Szamlazzphp\Enum;

enum PaymentMethod: string
{
    case Cash = 'Készpénz';
    case BankTransfer = 'Átutalás';
    case CreditCard = 'Bankkártya';
    case PayPal = 'PayPal';

    public function getComment(): string
    {
        return match($this) {
            self::Cash => 'cash',
            self::BankTransfer => 'bank transfer',
            self::CreditCard => 'credit card',
            self::PayPal => 'PayPal'
        };
    }

    public function __toString(): string
    {
        return $this->value . ' (' . $this->getComment() . ')';
    }
} 