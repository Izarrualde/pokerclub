<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class ServiceTipExceptions extends BaseException
{
    public static function serviceTipInvalidException(): self
    {
        return new self('El Service Tip debe ser numérico.', 400);
    }

    public static function serviceTipNotFoundException(): self
    {
        return new self('Resource not found.', 400);
    }
}
