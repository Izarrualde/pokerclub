<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Service\UserSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\UserSessionAlreadyAddedException;
use Solcre\Pokerclub\Exception\UserSessionInvalidException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\SolcreFramework2\Common\BaseRepository;

class UserSessionServiceTest extends TestCase
{
    public function testAdd(): void
    {
        $data = [
          'idSession'  => 3,
          'idUser'     => 1,
          'isApproved' => 1,
          'points'     => 0
        ];

        $map = [
            ['Solcre\Pokerclub\Entity\UserEntity', $data['idUser'], new UserEntity(1)],
            ['Solcre\Pokerclub\Entity\SessionEntity', $data['idSession'], new SessionEntity(3)]
        ];  

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedEntityManager->method('getReference')->will($this->returnValueMap($map));

        $session = new SessionEntity(3);
        $user = new UserEntity(1);
        $userService = $this->createMock(UserService::class);

        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $expectedUserSession    = new UserSessionEntity();
        $expectedUserSession->setSession($session);
        $expectedUserSession->setUser($user);
        $expectedUserSession->setIdUser($data['idUser']);
        $expectedUserSession->setIsApproved($data['isApproved']);
        $expectedUserSession->setAccumulatedPoints($data['points']);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
            $this->equalTo($expectedUserSession)
        )/*->willReturn('anything')*/;

        $userSessionService->add($data);
        // y que se llame metodo flush con anythig
     }

/*
 public function testAddAlreadyAddedException()
 {
    $data = [
      'idSession'  => 3,
      'idUser'     => 1,
      'isApproved' => 1,
      'points'     => 0,
      'start'      => '2019-07-04T19:00'
    ];


   $mockedEntityManager = $this->createMock(EntityManager::class);
   $mockedEntityManager->method('persist')->willReturn(true);

   $sessionWithThis = new SessionEntity(3);
   $session->s
   $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3))

   
   $mockedSessionEntity = $this->createMock(SessionEntity::class);
   $mockedSessionEntity->method('getActivePlayers')->willReturn(['1']);


    $session = new SessionEntity(3);
    $user = new UserEntity(1);
    $userSessionService = new UserService($mockedEntityManager);

    $expectedUserSession    = new UserEntity();
    $expectedUserSession->setSession($session);
    $expectedUserSession->setUser($user);
    $expectedUserSession->setIdUser($data['idUser']);
    $expectedUserSession->setIsApproved($data['isApproved']);
    $expectedUserSession->setAccumulatedPoints(new \DateTime($data['points']));


  //al llamar a userSessionService con esta $data, corroboro que se lanza UserSessionAlreadyAddedException 
  // ver en siguientes lineas como verificar que userSessionService->add lanzo la expcepcion.  

   $mockedEntityManager->expects($this->once())
   ->method('persist')
   ->with(
       $this->contains([$exception->getMessage()])
   )/*->willReturn('anything')*//*;

   $userSessionService->add($data);



 // y que se llame metodo flush con anythig

 }
*/

    public function testUpdate(): void
    {
        $data = [
            'id'              => 1,
            'idSession'       => 3,
            'idUser'          => 1,
            'isApproved'      => 1,
            'points'          => 0,
            'cashout'         => 0,
            'start'           => '2019-07-04T19:00',
            'end'             =>  null,
            'minimum_minutes' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);
        $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
            new UserSessionEntity(
                1,
                new SessionEntity(3),
                1,
                1,
                0,
                0,
                new \DateTime('2019-07-04T18:00'),
                null,
                null,
                new UserEntity(1)
            )
        );

        $userService        = $this->createMock(UserService::class);

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        /*
        // Inject the mockedRepository by reflection
        $reflection = new ReflectionProperty($userSessionService, 'repository');
        $reflection->setAccessible(true);
        $reflection->setValue($userSessionService, $mockedRepository);
        */

        $expectedUserSession    = new UserSessionEntity();
        $expectedUserSession->setId($data['id']);
        $expectedUserSession->setSession(new SessionEntity($data['idSession']));
        $expectedUserSession->setUser(new UserEntity($data['idUser']));
        $expectedUserSession->setIdUser($data['idUser']);
        $expectedUserSession->setIsApproved($data['isApproved']);
        $expectedUserSession->setAccumulatedPoints($data['points']);
        $expectedUserSession->setCashout($data['cashout']);
        $expectedUserSession->setStart(new \DateTime($data['start']));
        $expectedUserSession->setMinimumMinutes($data['minimum_minutes']);

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
            $this->equalTo($expectedUserSession)
        )/*->willReturn('anything')*/;

        $userSessionService->update($data['id'], $data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException(): void
    {
        // $data without id
        $data = [
            'idSession'       => 3,
            'idUser'          => 1,
            'isApproved'      => 1,
            'points'          => 0,
            'cashout'         => 0,
            'start'           => '2019-07-04T19:00',
            'end'             =>  null,
            'minimum_minutes' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $userService = $this->createMock(UserService::class);

        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $this->expectException(IncompleteDataException::class);

        $fakeIdForTesting = 1111;
        $userSessionService->update($fakeIdForTesting, $data);
    }

    public function testUpdateWithUserSessionNotFoundException(): void
    {
        $data = [
            'id'              => 'an unexisting id',
            'idSession'       => 3,
            'idUser'          => 1,
            'isApproved'      => 1,
            'points'          => 0,
            'cashout'         => 0,
            'start'           => '2019-07-04T19:00',
            'end'             =>  null,
            'minimum_minutes' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new UserSessionNotFoundException())
        );

        $userService        = $this->createMock(UserService::class);

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $this->expectException(UserSessionNotFoundException::class);
        $userSessionService->update($data['id'], $data);
    }

    public function testUpdateWithException(): void
    {
        $data = [
            'id'              => 'an unexisting id',
            'idSession'       => 3,
            'idUser'          => 1,
            'isApproved'      => 1,
            'points'          => 0,
            'cashout'         => 0,
            'start'           => '2019-07-04T19:00',
            'end'             =>  null,
            'minimum_minutes' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\CommissionSessionEntity' . " Entity not found", 404))
        );

        $userService        = $this->createMock(UserService::class);
        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $this->expectException(\Exception::class);
        $userSessionService->update($data['id'], $data);
    }

    public function testDelete(): void
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new UserSessionEntity(1));

        $userService        = $this->createMock(UserService::class);
        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $expectedUserSession = new UserSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedUserSession)
        )/*->willReturn('anything')*/;

        $userSessionService->delete($data['id']);
    }

    public function testDeleteWithUserSessionNotFoundException(): void
    {
        $data = [
            'id'           => 'an unexisting id',
            'idSession'    => 3,
            'idUser'       => 1,
            'isApproved'   => 1,
            'points'       => 0,
            'cashout'      => 0,
            'start'        => '2019-07-04T19:00',
            'end'          =>  null,
            'minimumHours' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new UserSessionNotFoundException())
        );

        $userService        = $this->createMock(UserService::class);
        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);
        
        $this->expectException(UserSessionNotFoundException::class);
        $userSessionService->delete($data);
    }

    public function testDeleteWithException(): void
    {
        $data = [
            'id'           => 'an unexisting id',
            'idSession'    => 3,
            'idUser'       => 1,
            'isApproved'   => 1,
            'points'       => 0,
            'cashout'      => 0,
            'start'        => '2019-07-04T19:00',
            'end'          =>  null,
            'minimumHours' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\UserEntity' . " Entity not found", 404))
        );

        $userService        = $this->createMock(UserService::class);
        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $this->expectException(\Exception::class);
        $userSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException(): void
    {
        // $data without idSession
         $data = [
            'idUser'       => 1,
            'isApproved'   => 1,
            'points'       => 0,
            'cashout'      => 0,
            'start'        => '2019-07-04T19:00',
            'end'          =>  null,
            'minimumHours' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $userService = $this->createMock(UserService::class);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $this->expectException(IncompleteDataException::class);
        $userSessionService->checkGenericInputData($data);
    }

    public function testClose(): void
    {
        $data = [
            'id'              => 1,
            'idSession'       => 3,
            'idUser'          => 1,
            'isApproved'      => 1,
            'points'          => 0,
            'cashout'         => 500,
            'start'           => '2019-07-04T19:00',
            'end'             => '2019-07-04T23:00',
            'minimum_minutes' => 180
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
            new UserSessionEntity(
                1,
                new SessionEntity(3),
                1,
                1,
                0,
                0,
                new \DateTime('2019-07-04T19:00'),
                null,
                3,
                new UserEntity(1)
            )
        );

        $userService        = $this->createMock(UserService::class);
        $userService->method('fetch')->willReturn(new UserEntity(1));
        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $userSessionService = new UserSessionService($mockedEntityManager, $userService, []);

        $expectedUserSession    = new UserSessionEntity();
        $expectedUserSession->setId($data['id']);
        $expectedUserSession->setSession(new SessionEntity($data['idSession']));

        $expectedUser = new UserEntity($data['idUser']);
        $expectedUser->setHours(3);

        $expectedUserSession->setUser($expectedUser);

        $expectedUserSession->setIdUser($data['idUser']);
        $expectedUserSession->setIsApproved($data['isApproved']);
        $expectedUserSession->setAccumulatedPoints($data['points']);
        $expectedUserSession->setCashout($data['cashout']);
        $expectedUserSession->setStart(new \DateTime($data['start']));
        $expectedUserSession->setEnd(new \DateTime($data['end']));
        $expectedUserSession->setMinimumMinutes($data['minimum_minutes']);

        // no testea que los argumentos de cada vez sean los que pido, si pongo 'hola' como argumento igual funciona porque si llama dos veces da test ok solo se rompe test si llama una vez
        $mockedEntityManager->expects($this->exactly(2))
        ->method('persist')
        ->withConsecutive(
            $this->equalTo($expectedUserSession->getUser()),
            $this->equalTo($expectedUserSession)
        );

        // 

        $userSessionService->close($data);
    }
}
