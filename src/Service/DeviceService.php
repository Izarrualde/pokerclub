<?php

namespace Solcre\Pokerclub\Service;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Validator\ObjectExists;
use Solcre\Pokerclub\Entity\DeviceEntity;
use Solcre\SolcreFramework2\Service\BaseService;
use Solcre\SolcreFramework2\Utility\Date;
use Solcre\Pokerclub\Entity\UserEntity;

class DeviceService extends BaseService
{
    protected $userService;

    /**
     * DeviceService constructor.
     * @param $entityManager
     * @param $userService
     */
    public function __construct(EntityManager $entityManager, UserService $userService)
    {
        parent::__construct($entityManager);
        $this->userService = $userService;
    }

    public function delete($token, $userLogged)
    {
        $user = $this->userService->fetchBy(['cellphone' => $userLogged]);
        if ($user instanceof UserEntity) {

            $validator = new ObjectExists(
                [
                    'object_repository' => $this->repository,
                    'fields'            => ['user', 'deviceToken'],
                ]);
            $params = [
                'user'        => $user->getId(),
                'deviceToken' => $token
            ];

            if ($validator->isValid($params)) {
                $device = $this->fetchBy($params);
                $this->entityManager->remove($device);
                $this->entityManager->flush();

                return true;
            }
        } else {
            throw new \Exception('Usuario no encontrado', 400);
        }
    }

    public function getDevice($data, $userLogged)
    {
        $user = $this->userService->fetchBy(['cellphone' => $userLogged]);
        $validator = new ObjectExists(
            [
                'object_repository' => $this->repository,
                'fields'            => ['deviceToken'],
            ]);

        $device = null;

        if ($validator->isValid($data['token'])) {
            $device = $this->fetchBy(['deviceToken' => $data['token']]);
        }

        if ($device instanceof DeviceEntity) {
            if (! ($device->getUser() === $user->getId())) {
                $device->setUser($user);
                $device->setAddedDate(Date::current());
                $this->entityManager->flush();
            }

            return $device;
        } else {
            return $this->add($data);
        }
    }

    private function getDevicePlatformId($platform)
    {
        $platformId = 0;

        switch ($platform) {
            case 'Android':
                $platformId = 1;
                break;
            case 'iOS':
                $platformId = 2;
                break;
            case 'WinCE':
                $platformId = 3;
                break;
        }

        return $platformId;
    }
}
