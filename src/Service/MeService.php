<?php namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Entity\AwardEntity;
use Solcre\Pokerclub\Service\AwardService;
use Solcre\Pokerclub\Entity\UserEntity;
use Solcre\SolcreFramework2\Service\BaseService;
use Exception;
use Solcre\Pokerclub\Entity\MeEntity;

class MeService extends BaseService
{
    private $userService;
    // private $permissionService;
    private $awardService;
    private $userSessionService;

    const COUNT_OF_HISTORICAL_SESSIONS = 5;

    public function __construct(EntityManager $entityManager, UserService $userService, UserSessionService $userSessionService, AwardService $awardService)
    {
        parent::__construct($entityManager);
        $this->userService = $userService;
        // $this->permissionService = $permissionService;
        $this->awardService = $awardService;
        $this->userSessionService = $userSessionService;
    }

    public function fetchMe(string $username): MeEntity
    {
        $user = $this->userService->fetchBy(['username' => $username]);

        if ($user instanceof UserEntity) {
            $meEntity = new MeEntity();
            $meEntity->setId($user->getId());

            $profile = $this->getProfile($user);
            $meEntity->setProfile($profile);

            $sessions = $this->getSessions($user);
            $meEntity->setSessions($sessions);

            $awards = $this->getAwards();
            $meEntity->setAwards($awards);

            $meEntity->setDomain($_SERVER['HTTP_HOST']);

            //$permissions = $this->permissionService->getAllPermissions($user->getUsername());
            //$meEntity->setPermissions($permissions);
        } else {
            throw new Exception('User not found', 404);
        }

        return $meEntity;
    }

    private function getProfile(UserEntity $user): array
    {
        return [
            'cellphone'             => $user->getUsername(),
            'name'                  => $user->getName(),
            'lastName'              => $user->getLastName(),
            'avatarHashedFilename'  => $user->getAvatarHashedFilename(),
            'avatarVisibleFilename' => $user->getAvatarVisibleFilename()
        ];
    }

    private function getSessions(UserEntity $user): array
    {
        return [
            'count'   => $user->getSessions(),
            'hours'   => $user->getHours(),
            'points'  => $user->getPoints(),
            'awards'  => $this->getAwardsByUser($user),
            'history' => $this->getHistoricalSessions($user)
        ];
    }

    private function getAwards(): array
    {
        $awards         = [];
        $awardsEntities = $this->awardService->fetchAll();

        foreach ($awardsEntities as $awardEntity)
        {
            if ($awardEntity instanceof AwardEntity) {
                $awards[] = [
                    'id'          => $awardEntity->getId(),
                    'name'        => $awardEntity->getName(),
                    'class'       => $awardEntity->getClass(),
                    'description' => $awardEntity->getDescription()
                ];
            }
        }

        return $awards;
    }

    private function getAwardsByUser(UserEntity $user): array
    {
        $awards         = [];
        $awardsEntities = $user->getAwards();

        foreach ($awardsEntities as $awardEntity) {
            if ($awardEntity instanceof AwardEntity) {
                $awards[] = $awardEntity->getId();
            }
        }

        return $awards;
    }

    private function getHistoricalSessions(UserEntity $user): array
    {
        $historicalSessions           = $this->userSessionService->getHistoricalSessions($user, self::COUNT_OF_HISTORICAL_SESSIONS);
        $normalizedHistoricalSessions = $this->normalizeHistoricalSessions($historicalSessions);

        return $normalizedHistoricalSessions;
    }

    private function normalizeHistoricalSessions(array $historicalSessions): array
    {
        $normalizedHistoricalSessions = [];

        foreach ($historicalSessions as $historicalSession)
        {
            $normalizedHistoricalSessions[] = [
                'name'     => $historicalSession['title'],
                'playedAt' => $historicalSession['startAt']->format('Y/m/d'),
                'points'   => $historicalSession['points']
            ];
        }

        return $normalizedHistoricalSessions;
    }
}
