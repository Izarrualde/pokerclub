<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Service\SessionService;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Solcre\Pokerclub\Exception\SessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Exception\UserInvalidException;
use Solcre\Pokerclub\Rakeback\rakebackAlgorithm;
use Solcre\SolcreFramework2\Common\BaseRepository;

class SessionServiceTest extends TestCase
{
  public const SESSION_POINTS_10 = 10;

    public function testAdd(): void
    {
        $data = [
          'date'                         => '2019-07-04',
          'start_at'                     => '2019-07-04T19:00',
          'title'                        => 'mesa mixta',
          'description'                  => 'lunes',
          'seats'                        => 9,
          'rakeback_class'               => 'Solcre\Pokerclub\Rakeback\SimpleRakeback',
          'minimum_user_session_minutes' => 240
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);

        $sessionService  = new SessionService($mockedEntityManager, []);
        $expectedSession = new SessionEntity();
        $expectedSession->setDate(new \DateTime($data['date']));
        $expectedSession->setStartTime(new \DateTime($data['start_at']));
        $expectedSession->setTitle($data['title']);
        $expectedSession->setDescription($data['description']);
        $expectedSession->setSeats($data['seats']);
        $expectedSession->setRakebackClass($data['rakeback_class']);
        $expectedSession->setMinimumUserSessionMinutes($data['minimum_user_session_minutes']);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
           $this->equalTo($expectedSession)
        )/*->willReturn('anything')*/;

        $sessionService->add($data);
    }

    public function testUpdate(): void
    {
        $data = [
            'id'                           => 1, 
            'date'                         => '2019-07-04',
            'start_at'                     => '2019-07-04T19:00',
            'real_start_at'                => '2019-07-04T19:15',
            'end_at'                       => '2019-07-04T20:00',
            'title'                        => 'title actualizado',
            'description'                  => 'description actualizada',
            'seats'                        => 9,
            'rakeback_class'               => 'SimpleRakeback',
            'minimum_user_session_minutes' => 240
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
            new SessionEntity(
                1,
                new \DateTime('2019-07-04T15:00'),
                'title original',
                'description original',
                'photo original',
                9,
                new \DateTime('2019-07-04T18:00'),
                new \DateTime('2019-07-04T18:30'),
                'SimpleRakeback',
                240
            )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $sessionService = new SessionService($mockedEntityManager, []);

        $expectedSession    = new SessionEntity();
        $expectedSession->setId($data['id']);
        $expectedSession->setDate(new \DateTime($data['date']));
        $expectedSession->setTitle($data['title']);
        $expectedSession->setDescription($data['description']);
        $expectedSession->setSeats($data['seats']);
        $expectedSession->setPhoto('photo original');
        $expectedSession->setStartTime(new \DateTime($data['start_at']));
        $expectedSession->setStartTimeReal(new \DateTime($data['real_start_at']));
        $expectedSession->setEndTime(new \DateTime($data['end_at']));
        $expectedSession->setRakebackClass($data['rakeback_class']);
        $expectedSession->setMinimumUserSessionMinutes($data['minimum_user_session_minutes']);

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
            $this->equalTo($expectedSession)
        )/*->willReturn('anything')*/;

        $sessionService->update($data['id'], $data);
    }

    public function testUpdateWithIncompleteDataException(): void
    {
        // $data without id
        $data = [
            'date'                         => '2019-07-04',
            'start_at'                     => '2019-07-04T19:00',
            'real_start_at'                => '2019-07-04T19:15',
            'end_at'                       => '2019-07-04T20:00',
            'title'                        => 'title actualizado',
            'description'                  => 'desscription actualizada',
            'seats'                        => 9,
            'rakeback_class'               => 'SimpleRakeback',
            'minimum_user_session_minutes' => 240
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $sessionService = new SessionService($mockedEntityManager, []);

        $this->expectException(IncompleteDataException::class);

        $fakeIdForTesting = 1111;
        $sessionService->update($fakeIdForTesting, $data);
    }

    public function testUpdateWithSessionNotFoundException(): void
    {
        $data = [
            'id'                           => 'an unexisting id',
            'date'                         => '2019-07-04',
            'start_at'                     => '2019-07-04T19:00',
            'real_start_at'                => '2019-07-04T19:15',
            'end_at'                       => '2019-07-04T20:00',
            'title'                        => 'title actualizado',
            'description'                  => 'description actualizada',
            'seats'                        => 9,
            'rakeback_class'               => 'SimpleRakeback',
            'minimum_user_session_minutes' => 240
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new SessionNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $sessionService = new SessionService($mockedEntityManager, []);

        $this->expectException(SessionNotFoundException::class);
        $sessionService->update($data['id'], $data);
    }

    public function testUpdateWithException(): void
    {
        $data = [
            'id'                           => 'an unexisting id',
            'date'                         => '2019-07-04',
            'start_at'                     => '2019-07-04T19:00',
            'real_start_at'                => '2019-07-04T19:15',
            'end_at'                       => '2019-07-04T20:00',
            'title'                        => 'title actualizado',
            'description'                  => 'desscription actualizada',
            'seats'                        => 9,
            'rakeback_class'               => 'SimpleRakeback',
            'minimum_user_session_minutes' => 240
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $sessionService = new SessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $sessionService->update($data['id'], $data);
    }

    public function testDelete(): void
    {
        $data = [
          'id'        => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new SessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $sessionService = new SessionService($mockedEntityManager, []);

        $expectedSession = new SessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedSession)
        )/*->willReturn('anything')*/;
     
        $sessionService->delete($data['id']);
    }

    public function testDeleteWithSessionNotFoundException(): void
    {
        $data = [
          'id' => 'an unexisting id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new SessionNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $sessionService = new SessionService($mockedEntityManager, []);

        $this->expectException(SessionNotFoundException::class);     
        $sessionService->delete($data['id']);
    }

    public function testDeleteWithException(): void
    {
        $data = [
          'id' => 'an unexisting id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $sessionService = new SessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);    
        $sessionService->delete($data['id']);
    }

    public function testCheckGenericInputDataWithIncompleteDataException(): void
    {
        // $data without rakebackClass
        $data = [
            'id'                           => 1,
            'date'                         => '2019-07-04',
            'start_at'                     => '2019-07-04T19:00',
            'real_start_at'                => '2019-07-04T19:15',
            'end_at'                       => '2019-07-04T20:00',
            'title'                        => 'title actualizado',
            'description'                  => 'updated description',
            'seats'                        => 9,
            'minimum_user_session_minutes' => 240
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $sessionService = new SessionService($mockedEntityManager, []);

        $this->expectException(IncompleteDataException::class);
        $sessionService->checkGenericInputData($data);
    }


/*
    public function testCreateRakebackAlgorithm()
    {
        $className = 'Solcre\Pokerclub\Rakeback\SimpleRakeback';

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $sessionService      = new SessionService($mockedEntityManager);
        
        $rakebackAlgoritmClass = get_class($sessionService->createRakebackAlgorithm($className));

        $this->assertEquals('Solcre\lmsuy\Rakeback\SimpleRakeback', $rakebackAlgoritmClass);
    }
*/

    public function testCalculateRakeback(): void
    {

        $data = [
          'id' => 1,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $session = new SessionEntity(
            1,
            new \DateTime('2019-07-04T15:00'),
            'title original',
            'description original',
            'photo original',
            9,
            new \DateTime('2019-07-04T18:00'),
            new \DateTime('2019-07-04T18:30'),
            'SimpleRakeback',
            4 
        );

        $user1 = New UserEntity();
        $user1->setPoints(100);
        $user2 = New UserEntity();
        $user2->setPoints(200);

        $userSession1 = new UserSessionEntity(
            1,
            $session,
            1,
            1,
            0,
            0,
            null,
            null,
            null,
            $user1
        );

        $userSession2 = new UserSessionEntity(
            2,
            $session,
            1,
            1,
            0,
            0,
            null,
            null,
            null,
            $user2
        );

        $sessionUsers = new ArrayCollection();
        $sessionUsers[] = $userSession1;
        $sessionUsers[] = $userSession2;

        $session->setSessionUsers($sessionUsers);

        $mockedSessionEntity = $this->createMock(SessionEntity::class);
        $mockedSessionEntity->method('getSessionUsers')->willReturn($sessionUsers);
 
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn($session);

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $rakebackAlgorithm = $this->createMock(rakebackAlgorithm::class);
        $rakebackAlgorithm->method('calculate')->willReturn(self::SESSION_POINTS_10);

        $mockedSessionService = $this->getMockBuilder(SessionService::class)
                                     ->disableOriginalConstructor()
                                     ->setMethods(['createRakebackAlgorithm', 'getEntityName'])
                                     ->getMock();


        $mockedSessionService->method('getEntityName')
                            ->willReturn('Solcre\Pokerclub\Entity\SessionEntity');

        $mockedSessionService->__construct($mockedEntityManager, []);                             
        $mockedSessionService->method('createRakebackAlgorithm')
                            ->willReturn($rakebackAlgorithm);

        // var_dump($mockedSessionService->createRakebackAlgorithm('class')->calculate($userSession1)); die();

        // var_dump($mockedSessionService->createRakebackAlgorithm('class')->calculate($userSession1)); die();

        $mockedSessionService->calculateRakeback($session->getId());

        $this->assertEquals($userSession1->getAccumulatedPoints(), 10);
/*
        foreach ($sessionUsers as $userSession) {
            $userSession->expects($this->once())
            ->method('setAccumulatedPoints')
            ->with(self::SESSION_POINTS_10);
            
            $user = $userSession->getUser();
            $user->expects($this->once())
            ->method('setPoints')
            ->with($user->getPoints()+self::SESSION_POINTS_10);
        }*/
    }
}
