<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Exception\UserHadActionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Solcre\Pokerclub\Exception\UserNotFoundException;
use Solcre\Pokerclub\Exception\UserInvalidException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Repository\BaseRepository;

class UserServiceTest extends TestCase
{
    public function testAdd()
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

        $userService = new UserService($mockedEntityManager);

        $expectedUser    = new UserEntity();
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
        )/*->willReturn('anything')*/;

        $userService->add($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdate()
    {
        $data = [
            'id'         =>1,
            'password'   => '123',
            'name'  => 'Jhon',
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
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
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

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $userService = new UserService($mockedEntityManager);

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
        )/*->willReturn('anything')*/;

        $userService->update($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException()
    {
        // $data without id
        $data = [
            'password'   => '123',
            'name'  => 'Jhon',
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

        $userService = new UserService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
        $userService->update($data);
    }

    public function testUpdateWithUserNotFoundException()
    {
        // $data without id
        $data = [
            'id'         => 'an unexisting id',
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
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new UserNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $userService = new UserService($mockedEntityManager);

        $this->expectException(UserNotFoundException::class);
        $userService->update($data);
    }

    public function testUpdateWitException()
    {
        // $data without id
        $data = [
            'id'         => 'an unexisting id',
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
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\UserEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $userService = new UserService($mockedEntityManager);

        $this->expectException(\Exception::class);
        $userService->update($data);
    }

    public function testDelete()
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new UserEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $userService = new UserService($mockedEntityManager);

        $expectedUser = new UserEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
           $this->equalTo($expectedUser)
        )/*->willReturn('anything')*/;

        $userService->delete($data['id']);
    }

    public function testDeleteWithUserNotFoundException()
    {
        $data = [
          'id' => 'an unexisting id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new UserNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $userService = new UserService($mockedEntityManager);

        $this->expectException(UserNotFoundException::class);     
        $userService->delete($data['id']);
    }

    public function testDeleteWithException()
    {
        $data = [
          'id' => 'an unexisting id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\UserEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $userService = new UserService($mockedEntityManager);

        $this->expectException(\Exception::class);      
        $userService->delete($data['id']);
    }

    public function testCheckGenericInputDataWithIncompleteDataException()
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
        $userService = new UserService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
        $userService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithUserInvalidException()
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
        $userService = new UserService($mockedEntityManager);

        $this->expectException(UserInvalidException::class);
        $userService->checkGenericInputData($data);
    }
}
