<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class ExpensesExceptions extends BaseException
{
    public static function expensesInvalidException(): self
    {
        return new self('El monto del gasto debe ser numérico.', 400);
    }

    public static function expenditureNotFoundException(): self
    {
        return new self('Resource not found.', 404);
    }
}
