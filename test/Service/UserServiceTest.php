<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Exception\UserHadActionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\UserExceptions;
use Solcre\SolcreFramework2\Common\BaseRepository;
use Solcre\Pokerclub\Repository\UserRepository;

class UserServiceTest extends TestCase
{
    public function testAdd(): void
    {
        $data = [
            'password'   => '123',
            'name'       => 'Jhon',
            'lastname'   => 'Doe',
            'email'      => 'jhon@lmsuy.com',
            'username'   => '12345',
            'multiplier' => 0,
            'active'     => 1,
            'hours'      => 0,
            'points'     => 0,
            'sessions'   => 0,
            'results'    => 0,
            'cashin'     => 0
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);

        $userService = new UserService($mockedEntityManager, []);

        $expectedUser = new UserEntity();
        $expectedUser->setPassword($data['password']);
        $expectedUser->setName($data['name']);
        $expectedUser->setLastname($data['lastname']);
        $expectedUser->setEmail($data['email']);
        $expectedUser->setUsername($data['username']);
        $expectedUser->setMultiplier($data['multiplier']);
        $expectedUser->setIsActive($data['active']);
        $expectedUser->setHours($data['hours']);
        $expectedUser->setPoints($data['points']);
        $expectedUser->setSessions($data['sessions']);
        $expectedUser->setResults($data['results']);
        $expectedUser->setCashin($data['cashin']);

        $mockedEntityManager->expects($this->once())
                            ->method('persist')
                            ->with(
                                $this->equalTo($expectedUser)
                            );

        $userService->add($data);
    }

    public function testUpdate(): void
    {
        $data = [
            'id'                      =>1,
            'password'                => '123',
            'password_confirm'        => '123',
            'name'                    => 'Jhon',
            'lastname'                => 'Doe',
            'email'                   => 'jhon@lmsuy.com',
            'username'                => '12345',
            'multiplier'              => 0,
            'active'                  => 1,
            'hours'                   => 0,
            'points'                  => 0,
            'sessions'                => 0,
            'results'                 => 0,
            'cashin'                  => 0,
            'logged_user_username'    => '12345',
            'avatar_file'             =>'file',
            'avatar_visible_filename' => ''
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedUserRepository = $this->createMock(UserRepository::class);
        $mockedUserRepository->method('find')->willReturn(
            new UserEntity(
                1,
                '123',
                'origina@email.com',
                'original lastname',
                'original name',
                'original username',
                0,
                1,
                0,
                0,
                0,
                0,
                0
            )
        );

        $mockedUserRepository->method('findOneBy')->willReturn(
            new UserEntity(
                1,
                '123',
                'origina@email.com',
                'original lastname',
                'original name',
                'original username',
                0,
                1,
                0,
                0,
                0,
                0,
                0
            )
        );

        $mockedUserRepository->method('userExists')->willReturn(false);
     
        $mockedEntityManager->method('getRepository')->willReturn($mockedUserRepository);
        $userService = new UserService($mockedEntityManager, []);

        $user = $userService->fetch($data['id']);

        $expectedUser    = new UserEntity();
        $expectedUser->setId($data['id']);
        $expectedUser->setPassword($data['password']);
        $expectedUser->setName($data['name']);
        $expectedUser->setLastname($data['lastname']);
        $expectedUser->setEmail($data['email']);
        $expectedUser->setUsername($data['username']);
        $expectedUser->setMultiplier($data['multiplier']);
        $expectedUser->setIsActive($data['active']);
        $expectedUser->setHours($data['hours']);
        $expectedUser->setPoints($data['points']);
        $expectedUser->setSessions($data['sessions']);
        $expectedUser->setResults($data['results']);
        $expectedUser->setCashin($data['cashin']);

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
           $this->equalTo($expectedUser)
        );

        $userService->update($data['id'], $data);
    }

    public function testUpdateWithIncompleteDataException(): void
    {
        // $data without id
        $data = [
            'password'                => '123',
            'password_confirm'        => '123',
            'name'                    => 'Jhon',
            'lastname'                => 'Doe',
            'email'                   => 'jhon@lmsuy.com',
            'username'                => '12345',
            'multiplier'              => 0,
            'active'                  => 1,
            'hours'                   => 0,
            'points'                  => 0,
            'sessions'                => 0,
            'results'                 => 0,
            'cashin'                  => 0,
            'logged_user_username'    => '12345',
            'avatar_file'             =>'file',
            'avatar_visible_filename' => ''
        ];

        $idNull = null;

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $userService = new UserService($mockedEntityManager, []);

        $this->expectException(BaseException::class);

        $userService->update($idNull, $data);
    }

    public function testUpdateWithUserNotFoundException(): void
    {
        // $data without id
        $data = [
            'id'                      =>'an unexisting id',
            'password'                => '123',
            'password_confirm'        => '123',
            'name'                    => 'Jhon',
            'lastname'                => 'Doe',
            'email'                   => 'jhon@lmsuy.com',
            'username'                => '12345',
            'multiplier'              => 0,
            'active'                  => 1,
            'hours'                   => 0,
            'points'                  => 0,
            'sessions'                => 0,
            'results'                 => 0,
            'cashin'                  => 0,
            'logged_user_username'    => '12345',
            'avatar_file'             =>'file',
            'avatar_visible_filename' => ''
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedUserRepository = $this->createMock(UserRepository::class);
        $mockedUserRepository->method('find')->will($this->throwException(
          UserExceptions::userNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedUserRepository);
        $userService = new UserService($mockedEntityManager, []);

        $this->expectException(UserExceptions::class);

        $userService->update($data['id'], $data);
    }

    public function testUpdateWithException(): void
    {
        // $data without id
        $data = [
            'id'                      =>'an unexisting id',
            'password'                => '123',
            'password_confirm'        => '123',
            'name'                    => 'Jhon',
            'lastname'                => 'Doe',
            'email'                   => 'jhon@lmsuy.com',
            'username'                => '12345',
            'multiplier'              => 0,
            'active'                  => 1,
            'hours'                   => 0,
            'points'                  => 0,
            'sessions'                => 0,
            'results'                 => 0,
            'cashin'                  => 0,
            'logged_user_username'    => '12345',
            'avatar_file'             =>'file',
            'avatar_visible_filename' => ''
        ];
        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedUserRepository = $this->createMock(UserRepository::class);
        $mockedUserRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\UserEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedUserRepository);
        $userService = new UserService($mockedEntityManager, []);

        $this->expectException(\Exception::class);

        $userService->update($data['id'], $data);
    }

    public function testDelete(): void
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new UserEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userService = new UserService($mockedEntityManager, []);

        $expectedUser = new UserEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
           $this->equalTo($expectedUser)
        );

        $userService->delete($data['id']);
    }

    public function testDeleteWithUserNotFoundException(): void
    {
        $data = [
          'id' => 'a non-existent id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          UserExceptions::userNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userService = new UserService($mockedEntityManager, []);

        $this->expectException(UserExceptions::class);

        $userService->delete($data['id']);
    }

    public function testDeleteWithException(): void
    {
        $data = [
          'id' => 'a non-existent id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\UserEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userService = new UserService($mockedEntityManager, []);

        $this->expectException(\Exception::class);

        $userService->delete($data['id']);
    }

    public function testCheckGenericInputDataWithIncompleteDataException(): void
    {
        // $data without cashin
        $data = [
            'id'         => 1,
            'password'   => '123',
            'name'       => 'Jhon',
            'lastname'   => 'Doe',
            'email'      => 'jhon@lmsuy.com',
            'username'   => '12345',
            'multiplier' => 0,
            'active'     => 1,
            'hours'      => 0,
            'points'     => 0,
            'sessions'   => 0,
            'results'    => 0
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $userService = new UserService($mockedEntityManager, []);

        $this->expectException(BaseException::class);

        $userService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithUserInvalid(): void
    {
        // $data with non numeric cashin
        $data = [
            'id'         => 1,
            'password'   => '123',
            'name'       => 'Jhon',
            'lastname'   => 'Doe',
            'email'      => 'jhon@lmsuy.com',
            'username'   => '12345',
            'multiplier' => 0,
            'active'     => 1,
            'hours'      => 0,
            'points'     => 0,
            'sessions'   => 0,
            'results'    => 0,
            'cashin'     => 'a non numeric value'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $userService = new UserService($mockedEntityManager, []);

        $this->expectException(UserExceptions::class);

        $userService->checkGenericInputData($data);
    }
}
