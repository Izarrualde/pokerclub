<?php
namespace Solcre\lmsuy\Exception;

/**
 * @codeCoverageIgnore
 */
class ExpensesInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El monto del gasto debe ser numérico.");
    }
}
