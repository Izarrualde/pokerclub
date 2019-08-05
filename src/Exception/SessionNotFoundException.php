<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class SessionNotFoundExceptionException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Resource not found.");
    }
}