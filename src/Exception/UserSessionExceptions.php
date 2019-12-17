<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class UserSessionExceptions extends BaseException
{
    public static function userSessionAlreadyAddedException($usersAlreadyAdded): self
    {
        $list = implode(',', $usersAlreadyAdded);
        return new self('Operacion denegada, usuario/s: ' . $list .'ya fue/fueron agregado/s a la sesión.', 400);
    }

    public static function userHadActionException(): self
    {
        return new self('El usuario ha participado en alguna sesión.', 400);
    }

    public static function insufficientUserSessionTimeException(): self
    {
        return new self('Usuario no alcanzó el tiempo de juego requerido.', 400);
    }

    public static function userSessionNotFoundException(): self
    {
        return new self('Resource not found.', 400);
    }

    public static function invalidDuration(): self
    {
        return new self('Duration no pudo ser calculada', 400);
    }

    public static function userWithoutActionException(): self
    {
        return new self('User without action in this session.', 400);
    }
}
