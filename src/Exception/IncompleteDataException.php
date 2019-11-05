<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class IncompleteDataException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Incomplete data.", 400);
    }
}
