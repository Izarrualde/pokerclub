<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class UserSessionNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Resource not found.");
    }
}
