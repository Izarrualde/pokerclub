<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class DealerTipExceptions extends BaseException
{
    public static function dealerTipInvalidException(): self
    {
        return new self('El Dealer Tip debe ser numérico.', 400);
    }

    public static function dealerTipNotFoundException(): self
    {
        return new self('Resource not found.', 400);
    }
}
