<?php
namespace Solcre\lmsuy\Exception;

/**
 * @codeCoverageIgnore
 */
class BuyinInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El buyin debe ser numérico.");
    }
}
