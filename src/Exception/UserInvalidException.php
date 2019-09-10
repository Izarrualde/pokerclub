<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class UserInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Usuario no válido.");
    }
}