<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class InsufficientAvailableSeatsException extends \Exception
{

    public function __construct()
    {
        parent::__construct("No hay suficientes asientos disponibles", 400);
    }
}