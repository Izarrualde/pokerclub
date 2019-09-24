<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Service\BuyinSessionService;
use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\UserService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BuyinInvalidException;
use Solcre\Pokerclub\Exception\BuyinNotFoundException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Repository\BaseRepository;

class BuyinSessionServiceTest extends TestCase
{
    public function testAdd()
    {
        $data = [
            'id'            =>  1,
            'amountCash'    => 50,
            'amountCredit'  => 60,
            'idUserSession' => 1,
            'hour'          => '2019-07-04T19:00',
            'currency'      => 1,
            'approved'      => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $mockedUserSessionService->method('fetch')->willReturn(new UserSessionEntity(1));

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $expectedBuyin    = new BuyinSessionEntity();
        $expectedBuyin->setHour(new \DateTime($data['hour']));
        $expectedBuyin->setAmountCash($data['amountCash']);
        $expectedBuyin->setAmountCredit($data['amountCredit']);
        $expectedBuyin->setCurrency($data['currency']);
        $expectedBuyin->setIsApproved($data['approved']);
        $userSession = new UserSessionEntity(1);
        $userSession->setStart(new \DateTime($data['hour']));
        $expectedBuyin->setUserSession($userSession);
      

        $mockedEntityManager->expects($this->exactly(2))
        ->method('persist')
        ->withConsecutive(
            $this->equalTo($userSession),
            $this->equalTo($expectedBuyin)
        );

        $buyinSessionService->add($data);
        // y que se llame metodo flush con anythig
    }

    public function testAddWithUserSessionNotFoundException()
    {
        $data = [
            'id'            =>  'a non existing userSession',
            'amountCash'    => 50,
            'amountCredit'  => 60,
            'idUserSession' => 1,
            'hour'          => '2019-07-04T19:00',
            'currency'      => 1,
            'approved'      => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $mockedUserSessionService->method('fetch')->will($this->throwException(
          new UserSessionNotFoundException())
        );

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $this->expectException(UserSessionNotFoundException::class);
        $buyinSessionService->add($data);
        // y que se llame metodo flush con anythig
    }

    public function testAddWithException()
    {
        $data = [
            'id'            =>  'a non existing userSession',
            'amountCash'    => 50,
            'amountCredit'  => 60,
            'idUserSession' => 1,
            'hour'          => '2019-07-04T19:00',
            'currency'      => 1,
            'approved'      => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $mockedUserSessionService->method('fetch')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404))
        );

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $this->expectException(\Exception::class);
        $buyinSessionService->add($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdate()
    {
        $data = [
            'id'            =>  1,
            'amountCash'    => 50,
            'amountCredit'  => 60,
            'idUserSession' => 1,
            'hour'          => '2019-07-04T19:00',
            'currency'      => 1,
            'approved'      => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
            new BuyinSessionEntity(
                1,
                10,
                20,
                new UserSessionEntity(1),
                new \DateTime('2019-07-04T12:00'),
                1,
                1
            )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $mockedUserSessionService = $this->createMock(UserSessionService::class);

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $expectedBuyin    = new BuyinSessionEntity();
        $expectedBuyin->setId(1);
        $expectedBuyin->setUserSession(new UserSessionEntity(1));
        $expectedBuyin->setHour(new \DateTime($data['hour']));
        $expectedBuyin->setAmountCash($data['amountCash']);
        $expectedBuyin->setAmountCredit($data['amountCredit']);
        $expectedBuyin->setIsApproved($data['approved']);
        $expectedBuyin->setCurrency($data['currency']);
      

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
           $this->equalTo($expectedBuyin)
        )/*->willReturn('anything')*/;

       $buyinSessionService->update($data);
       // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException()
    {
        // $data without id
        $data = [
            'amountCash'    => 50,
            'amountCredit'  => 60,
            'idUserSession' => 1,
            'hour'          => '2019-07-04T19:00',
            'currency'      => 1,
            'approved'      => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);
        
        $this->expectException(IncompleteDataException::class);

        $buyinSessionService->update($data);
    }

    public function testUpdateWithBuyinNotFoundException()
    {
        // $data without id
        $data = [
            'id'            => 1,
            'amountCash'    => 50,
            'amountCredit'  => 60,
            'idUserSession' => 1,
            'hour'          => '2019-07-04T19:00',
            'currency'      => 1,
            'approved'      => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new BuyinNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $this->expectException(BuyinNotFoundException::class);
        $buyinSessionService->update($data);
    }

    public function testUpdateWithException()
    {
        // $data without id
        $data = [
            'id'            => 1,
            'amountCash'    => 50,
            'amountCredit'  => 60,
            'idUserSession' => 1,
            'hour'          => '2019-07-04T19:00',
            'currency'      => 1,
            'approved'      => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404))
        );;

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);
        
        $this->expectException(\Exception::class);
        $buyinSessionService->update($data);
    }

    public function testDelete()
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        //$mockedEntityManager->method('getReference')->willReturn(new BuyinSessionEntity(1));

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new BuyinSessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $mockedUserService        = new UserService($mockedEntityManager);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $expectedBuyin = new BuyinSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
           $this->equalTo($expectedBuyin)
        )/*->willReturn('anything')*/;

        $buyinSessionService->delete($data['id']);
    }

    public function testDeleteWithBuyinNotFoundException()
    {
        $data = [
          'id' => 'an existing id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new BuyinNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $mockedUserService        = new UserService($mockedEntityManager);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $this->expectException(BuyinNotFoundException::class);
        $buyinSessionService->delete($data);
    }

}
