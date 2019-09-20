<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Service\SessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Repository\BaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Solcre\Pokerclub\Exception\SessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Rakeback\rakebackAlgorithm;

class SimpleRakeback 
{
    const RAKEBACK_PERCENTAGE = 0.01;

    public function calculate(UserSessionEntity $userSession)
    {
      var_dump('created simpleRakebackClass');
    }
}

class SessionServiceTest extends TestCase
{
  const SESSION_POINTS_10 = 10;

    public function testAdd()
    {
        $data = [
          'id'            => 1, 
          'hour'          => '2019-07-04T19:00', 
          'comission'     => 100,
          'idSession'     => 3,
          'date'          => '2019-07-04',
          'start_at'      => '2019-07-04T19:00',
          'real_start_at' => '2019-07-04T19:15',
          'end_at'        => '2019-07-04T20:00',
          'title'         => 'mesa mixta',
          'description'   => 'lunes',
          'seats'         => 9,
          'rakebackClass' => 'Solcre\Pokerclub\Rakeback\SimpleRakeback'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);

        $sessionService = new SessionService($mockedEntityManager);

        $expectedSession    = new SessionEntity();
        $expectedSession->setDate(new \DateTime($data['date']));
        $expectedSession->setStartTime(new \DateTime($data['start_at']));
        $expectedSession->setStartTimeReal(new \DateTime($data['real_start_at']));
        $expectedSession->setEndTime(new \DateTime($data['end_at']));

        $expectedSession->setTitle($data['title']);
        $expectedSession->setDescription($data['description']);
        $expectedSession->setSeats($data['seats']);
        $expectedSession->setRakebackClass($data['rakebackClass']);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
           $this->equalTo($expectedSession)
        )/*->willReturn('anything')*/;

        $sessionService->add($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdate()
    {
        $data = [
            'id'            => 1, 
            'hour'          => '2019-07-04T19:00', 
            'comission'     => 100,
            'idSession'     => 3,
            'date'          => '2019-07-04',
            'start_at'      => '2019-07-04T19:00',
            'real_start_at' => '2019-07-04T19:15',
            'end_at'        => '2019-07-04T20:00',
            'title'         => 'title actualizado',
            'description'   => 'desscription actualizada',
            'seats'         => 9,
            'rakebackClass' => 'SimpleRakeback'
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
                null
            )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $sessionService = new SessionService($mockedEntityManager);

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

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
            $this->equalTo($expectedSession)
        )/*->willReturn('anything')*/;

        $sessionService->update($data);
        // y que se llame metodo flush con anythig
    }

    public function testDelete()
    {
        $data = [
          'id'        => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new SessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $sessionService = new SessionService($mockedEntityManager);

        $expectedSession = new SessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedSession)
        )/*->willReturn('anything')*/;
     
        $sessionService->delete($data['id']);
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

    public function testCalculateRakeback()
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
            'SimpleRakeback'
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

        $mockedSessionService->__construct($mockedEntityManager);                             
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
