<?php

namespace Szamlazzphp\Exceptions;

use Exception;

/**
 * Hiányzó azonosítási adatok kivétel
 */
class MissingCredentialsException extends Exception
{
    /**
     * Kivétel létrehozása
     * 
     * @param string $message A kivétel üzenete
     * @param int $code A kivétel kódja
     * @param Exception|null $previous Az előző kivétel
     */
    public function __construct(string $message = "Hiányzó azonosítási adatok", int $code = 0, Exception|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 