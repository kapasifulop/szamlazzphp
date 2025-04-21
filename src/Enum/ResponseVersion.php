<?php

namespace Szamlazzphp\Enum;

/**
 * Számlázz.hu API válasz verzió típusok
 */
enum ResponseVersion: int
{
    /**
     * Válasz PDF formátumban
     */
    case PDF = 1;
    
    /**
     * Válasz XML formátumban, benne base64 kódolt PDF
     */
    case XML = 2;
} 