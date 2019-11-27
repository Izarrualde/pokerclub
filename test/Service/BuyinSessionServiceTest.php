<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Service\BuyinSessionService;
use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\UserService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\BuyinExceptions;
use Solcre\Pokerclub\Exception\UserSessionExceptions;
use Solcre\SolcreFramework2\Common\BaseRepository;

class BuyinSessionServiceTest extends TestCase
{
    public function testAdd(): void
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

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $expectedBuyin = new BuyinSessionEntity();
        $expectedBuyin->setHour(new \DateTime($data['hour']));
        $expectedBuyin->setAmountCash($data['amountCash']);
        $expectedBuyin->setAmountCredit($data['amountCredit']);
        $expectedBuyin->setCurrency($data['currency']);
        $expectedBuyin->setIsApproved($data['approved']);
        $userSession = new UserSessionEntity(1);
        $userSession->setStart(new \DateTime($data['hour']));
        $expectedBuyin->setUserSession($userSession);
      
        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
           $this->equalTo($expectedBuyin)
        );

        $buyinSessionService->add($data);
    }

    public function testAddWithUserSessionNotFoundException(): void
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
          UserSessionExceptions::userSessionNotFoundException())
        );

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(UserSessionExceptions::class);

        $buyinSessionService->add($data);
    }

    public function testAddWithException(): void
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

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(\Exception::class);
        $buyinSessionService->add($data);
    }

    public function testUpdate(): void
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

        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

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
        );

       $buyinSessionService->update($data['id'], $data);
    }

    public function testUpdateWithIncompleteDataException(): void
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

        $idNull = null;

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);
        
        $this->expectException(BaseException::class);

        $buyinSessionService->update($idNull, $data);
    }

    public function testUpdateWithBuyinNotFoundException(): void
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

        $idNUll = null;

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          BuyinExceptions::buyinNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(BuyinExceptions::class);

        $buyinSessionService->update($idNUll, $data);
    }

    public function testUpdateWithException(): void
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

        $idNull = null;

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404))
        );;

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);
        
        $this->expectException(\Exception::class);

        $buyinSessionService->update($idNull, $data);
    }

    public function testDelete(): void
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
        $userSessionService  = $this->createMock(UserSessionService::class);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $userSessionService, []);

        $expectedBuyin = new BuyinSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
           $this->equalTo($expectedBuyin)
        );

        $buyinSessionService->delete($data['id']);
    }

    public function testDeleteWithBuyinNotFoundException(): void
    {
        $data = [
          'id' => 'an existing id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          BuyinExceptions::buyinNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $mockedUserService        = new UserService($mockedEntityManager, []);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService, []);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(BuyinExceptions::class);

        $buyinSessionService->delete($data);
    }

    public function testDeleteWithException(): void
    {
        $data = [
          'id' => 'an existing id'
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\BuyinSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $mockedUserService        = new UserService($mockedEntityManager, []);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService, []);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(\Exception::class);

        $buyinSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException(): void
    {
        // $data without idUserSession
        $data = [
          'hour'          => '2019-07-04T19:00', 
          'amountCash'    => 100,
          'amountCredit'  => 100,
          'currency'      => 1,
          'approved'      => 2
        ];

        $mockedEntityManager      = $this->createMock(EntityManager::class);
        $mockedUserService        = new UserService($mockedEntityManager, []);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService, []);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(BaseException::class);
        $buyinSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithNonNumericAmountCash(): void
    {
        // $data without idUserSession
        $data = [
            'hour'          => '2019-07-04T19:00',
            'amountCash'    => 'a non numeric value',
            'amountCredit'  => 100,
            'currency'      => 1,
            'approved'      => 2,
            'idUserSession' => 1
        ];

        $mockedEntityManager      = $this->createMock(EntityManager::class);
        $mockedUserService        = new UserService($mockedEntityManager, []);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService, []);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(BuyinExceptions::class);
        $buyinSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithNonNumericAmountCredit(): void
    {
        // $data without idUserSession
        $data = [
          'hour'          => '2019-07-04T19:00', 
          'amountCash'    => 100,
          'amountCredit'  => 'a non numeric value',
          'currency'      => 1,
          'approved'      => 2,
          'idUserSession' => 1
        ];

        $mockedEntityManager      = $this->createMock(EntityManager::class);
        $mockedUserService        = new UserService($mockedEntityManager, []);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService, []);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(BuyinExceptions::class);

        $buyinSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithNegativeAmountCash(): void
    {
        // $data without idUserSession
        $data = [
            'hour'          => '2019-07-04T19:00',
            'amountCash'    => -100,
            'amountCredit'  => 100,
            'currency'      => 1,
            'approved'      => 2,
            'idUserSession' => 1
        ];

        $mockedEntityManager      = $this->createMock(EntityManager::class);
        $mockedUserService        = new UserService($mockedEntityManager, []);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService, []);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(BuyinExceptions::class);

        $buyinSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithNegativeAmountCredit(): void
    {
        // $data without idUserSession
        $data = [
            'hour'          => '2019-07-04T19:00',
            'amountCash'    => 100,
            'amountCredit'  => -100,
            'currency'      => 1,
            'approved'      => 2,
            'idUserSession' => 1
        ];

        $mockedEntityManager      = $this->createMock(EntityManager::class);
        $mockedUserService        = new UserService($mockedEntityManager, []);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService, []);
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService, []);

        $this->expectException(BuyinExceptions::class);

        $buyinSessionService->checkGenericInputData($data);
    }

/*
    public function testFetchAllBuyins()
    {
        // como testear que se llama a fetchAll con el parametro idSession?
        $idSession = 1;

        $mockedEntityManager      = $this->createMock(EntityManager::class);
        $mockedUserService        = new UserService($mockedEntityManager);
        $mockedUserSessionService = new UserSessionService($mockedEntityManager, $mockedUserService);
        
        $buyinSessionService      = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);

        $repository->expects($this->once())
        ->method('fetchAll')
        ->with(
            $this->equalTo($idSession)
        );

        $buyinSessionService->fetchAllBuyins($idSession);
    }
*/
/*
    public function testFetchAllBuyins()
    {
        // testear des esta  manera tambien
        $userSession = new UserSessionEntity();
        $buyins[] = new BuyinSessionEntity(1,100,0,$userSession);
        $buyins[] = new BuyinSessionEntity(2,200,0,$userSession);

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedUserSessionService = $this->createMock(UserSessionService::class);
        //$mockedRepository = $this->createMock(BaseRepository::class);
        //$mockedRepository->method('fetchAllBuyins')-willReturn($buyins);

        //$mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $buyinSessionService = new BuyinSessionService($mockedEntityManager, $mockedUserSessionService);
        var_dump($buyinSessionService->getRepository());

        // $this->assertEquals($buyinSessionService->repository->fetchAllBuyins(1), $buyins);
        // ver como hacer que fetchAll de mockedRepository devuelva $buyins solo si lo llamo con '1'
    }
    */
}
