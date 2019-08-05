<?php
namespace Solcre\Pokerclub\Rakeback;

use Solcre\Pokerclub\Entity\UserSessionEntity;

interface RakeBackAlgorithm
{
    public function calculate(UserSessionEntity $userSession);
}
