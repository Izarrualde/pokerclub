<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class CommissionExceptions extends BaseException
{
    public static function commissionInvalidException(): self
    {
        return new self('La comision debe ser numérica.', 400);
    }

    public static function commissionNotFoundException(): self
    {
        return new self('Resource not found.', 400);
    }
}
