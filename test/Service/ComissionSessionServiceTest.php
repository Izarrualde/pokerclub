<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\CommissionSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\CommissionSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\CommissionInvalidException;
use Solcre\Pokerclub\Exception\CommissionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Service\BaseService;
use Solcre\SolcreFramework2\Common\BaseRepository;

class CommissionSessionServiceTest extends TestCase
{
    public function testAdd()
    {
        $data = [
            'id'         => 1,
            'hour'       => '2019-07-04T19:00',
            'commission' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $expectedCommission    = new CommissionSessionEntity();
        $expectedCommission->setHour(new \DateTime($data['hour']));
        $expectedCommission->setCommission($data['commission']);
        $session = new SessionEntity(3);
        $expectedCommission->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
           $this->equalTo($expectedCommission)
        )/*->willReturn('anything')*/;

        $commissionSessionService->add($data);
        // y que se llame metodo flush con anythig

    }

    public function testUpdate()
    {
        $data = [
          'id'        => 1, 
          'hour'      => '2019-07-04T19:00', 
          'commission' => 100,
          'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
            new CommissionSessionEntity(
              1,
              new \DateTime('2019-07-04T12:00'),
              80,
              new SessionEntity(3)
            )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $expectedCommmission    = new CommissionSessionEntity();
        $expectedCommission->setId(1);
        $expectedCommission->setHour(new \DateTime($data['hour']));
        $expectedCommission->setCommission($data['commission']);
        $session = new SessionEntity(3);
        $expectedCommission->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
           $this->equalTo($expectedCommission)
        );

        $commissionSessionService->update($data['id'], $data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException(): void
    {
        // $data without id
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'commission' => 100,
          'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(IncompleteDataException::class);

        $fakeIdForTesting = 1111;
        $commissionSessionService->update($fakeIdForTesting, $data);
    }

    public function testUpdateWithCommissionNotFoundException()
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'commission' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new CommissionNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(CommissionNotFoundException::class);
        $commissionSessionService->update($data['id'], $data);
    }

    public function testUpdateWithException()
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'commission' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\CommissionSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $commissionSessionService->update($data['id'], $data);
    }

    public function testDelete()
    {
        $data = [
          'id'        => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new CommissionSessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $expectedCommission = new CommissionSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedCommission)
        )/*->willReturn('anything')*/;

        $commissionSessionService->delete($data['id']);
    }

    public function testDeleteWithCommissionNotFoundException(): void
    {
        $data = [
            'id'         => 'an unexisting id',
            'hour'       => '2019-07-04T19:00',
            'commission' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new CommissionNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(CommissionNotFoundException::class);
        $commissionSessionService->delete($data);
    }

    public function testDeleteWithException(): void
    {
        // $data without id
        $data = [
            'id'         => 'an unexisting id',
            'hour'       => '2019-07-04T19:00',
            'commission' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\CommissionSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $commissionSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException()
    {
        // $data without idSession
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'commission' => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(IncompleteDataException::class);
        $commissionSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithCommissionNonNumeric(): void
    {
        // $data with non numeric commission
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'commission' => 'a non numeric value',
          'idSession' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(CommissionInvalidException::class);
        $commissionSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithCommissionNegativeValue(): void
    {
        // $data with negative commission
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'commission' => -50,
          'idSession' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $commissionSessionService = new CommissionSessionService($mockedEntityManager, []);

        $this->expectException(CommissionInvalidException::class);
        $commissionSessionService->checkGenericInputData($data);
    }
}
