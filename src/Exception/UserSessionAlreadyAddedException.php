<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class UserSessionAlreadyAddedException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El usuario ya fue agregado a la sesión.");
    }
}
