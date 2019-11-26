<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class SessionExceptions extends BaseException
{
    public static function insufficientAvailableSeatsException(): self
    {
        return new self('No hay suficientes asientos disponibles', 400);
    }

    public static function invalidRakebackClassException(): self
    {
        return new self('RakebackClass no válida', 400);
    }

    public static function invalidSessionException(): self
    {
        return new self('Sesión no válida', 400);
    }

    public static function sessionNotFoundException(): self
    {
        return new self('Resource not found.', 400);
    }

    public static function tableIsFullException(): self
    {
        return new self('Mesa llena.', 400);
    }
}
