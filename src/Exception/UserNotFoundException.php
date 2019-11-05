<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class UserNotFoundException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Resource not found.", 400);
    }
}
