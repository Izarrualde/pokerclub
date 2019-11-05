<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class InsufficientUserSessionTimeException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Usuario no alcanzó el tiempo de juego requerido.", 400);
    }
}
