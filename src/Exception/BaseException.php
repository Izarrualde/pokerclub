<?php

namespace Solcre\Pokerclub\Exception;

use Exception;

class BaseException extends Exception
{
    protected $additional = [];

    public function __construct($message = '', $code = 0, $additional = [])
    {
        $this->additional = $additional;
        parent::__construct($message, $code);
    }

    /**
     * @return array
     */
    public function getAdditional(): array
    {
        return $this->additional;
    }

    public static function classNonExistentException(): self
    {
        return new self('La clase no existe.', 400);
    }

    public static function incompleteDatatException(): self
    {
        return new self('Incomplete data.', 400);
    }
}
