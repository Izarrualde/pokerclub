<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class UserSessionExceptions extends BaseException
{
    public static function userSessionAlreadyAddedException(): self
    {
        return new self('El usuario ya fue agregado a la sesión.', 400);
    }

    public static function userHadActionException(): self
    {
        return new self('Usuario no alcanzó el tiempo de juego requerido.', 400);
    }

    public static function insufficientUserSessionTimeException(): self
    {
        return new self('El usuario ha participado en alguna sesión.', 400);
    }

    public static function userSessionNotFoundException(): self
    {
        return new self('Resource not found.', 400);
    }
}
