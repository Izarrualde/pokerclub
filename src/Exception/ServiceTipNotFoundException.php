<?php
namespace Solcre\pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class ServiceTipNotFoundException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Resource not found.");
    }
}