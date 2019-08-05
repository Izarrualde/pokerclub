<?php
namespace Solcre\pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class ComissionNotFoundException extends \Exception
{

    public function __construct()
    {
        parent::__construct("La comision debe ser numérica.");
    }
}