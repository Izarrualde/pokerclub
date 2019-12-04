<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class UserExceptions extends BaseException
{
    public static function userInvalidException(): self
    {
        return new self('Usuario no válido.', 400);
    }

    public static function userNotFoundException(): self
    {
        return new self('Resource not found.', 400);
    }

    public static function userAlreadyExistException(): self
    {
        return new self('Usuario ya existente.', 400);
    }

    public static function invalidEmailException(): self
    {
        return new self('El email ingresado no es válido', 400);
    }
}
