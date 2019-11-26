<?php
namespace Solcre\Pokerclub\Rakeback;

use Solcre\Pokerclub\Entity\UserSessionEntity;

interface RakebackAlgorithm
{
    public function calculate(UserSessionEntity $userSession);
}
