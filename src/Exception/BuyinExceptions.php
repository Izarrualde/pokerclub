<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class BuyinExceptions extends BaseException
{
    public static function buyinInvalidException(): self
    {
        return new self('El buyin debe ser numérico.', 400);
    }

    public static function buyinNotFoundException(): self
    {
        return new self('Resource not found.', 404);
    }
}
