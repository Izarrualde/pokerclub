<?php
namespace Solcre\Pokerclub\Exception;

/**
 * @codeCoverageIgnore
 */
class RakebackClassNotFoundException extends \Exception
{

    public function __construct()
    {
        parent::__construct("La clase no existe");
    }
}
