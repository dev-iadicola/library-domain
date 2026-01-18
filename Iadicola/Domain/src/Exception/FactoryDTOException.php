<?php

namespace Iadicola\Domain\Src\Exception;

use InvalidArgumentException;
use Throwable;

class FactoryDTOException extends InvalidArgumentException {

    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        return parent::__construct($message, $code, $previous);
    }
}