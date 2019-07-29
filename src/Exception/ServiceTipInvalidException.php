<?php
namespace Solcre\lmsuy\Exception;

/**
 * @codeCoverageIgnore
 */
class ServiceTipInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El Service Tip debe ser numérico.");
    }
}
