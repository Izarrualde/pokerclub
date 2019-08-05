<?php
namespace Solcre\pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class TableIsFullException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Mesa llena.");
    }
}
