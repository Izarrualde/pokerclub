<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class SessionInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Resource no válida.", 400);
    }
}
