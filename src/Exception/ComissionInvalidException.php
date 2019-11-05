<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class ComissionInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("La comision debe ser numérica.", 400);
    }
}
