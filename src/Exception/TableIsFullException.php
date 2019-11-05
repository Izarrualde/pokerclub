<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class TableIsFullException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Mesa llena.", 400);
    }
}
