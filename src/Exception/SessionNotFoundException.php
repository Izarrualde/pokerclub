<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class SessionNotFoundException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Resource not found.", 400);
    }
}
