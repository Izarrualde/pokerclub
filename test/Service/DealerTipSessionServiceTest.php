<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\DealerTipSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\DealerTipExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Common\BaseRepository;

class DealerTipSessionServiceTest extends TestCase
{
    public function testAdd(): void
    {
        $data = [
            'id'        => 1, 
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $expectedDealerTip    = new DealerTipSessionEntity();
        $expectedDealerTip->setHour(new \DateTime($data['hour']));
        $expectedDealerTip->setDealerTip($data['dealerTip']);
        $session = new SessionEntity(3);
        $expectedDealerTip->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
            $this->equalTo($expectedDealerTip)
        )/*->willReturn('anything')*/;

        $dealerTipSessionService->add($data);
    }

    public function testUpdate(): void
    {

        $data = [
            'id'        => 1, 
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new DealerTipSessionEntity(
        1,
        new \DateTime('2019-07-04T15:00'),
        80,
        new SessionEntity(3)
        )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $expectedDealerTip    = new DealerTipSessionEntity();
        $expectedDealerTip->setId(1);
        $expectedDealerTip->setHour(new \DateTime($data['hour']));
        $expectedDealerTip->setDealerTip($data['dealerTip']);
        $session = new SessionEntity(3);
        $expectedDealerTip->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
            $this->equalTo($expectedDealerTip)
        );

        $dealerTipSessionService->update($data['id'], $data);
    }

    public function testUpdateWithIncompleteDataException(): void
    {
        // $data without id
        $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $idNull = null;

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(BaseException::class);

        $fakeIdForTesting = 1111;
        $dealerTipSessionService->update($idNull, $data);
    }

    public function testUpdateWithDealerTipNotFoundException(): void
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          DealerTipExceptions::dealerTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(DealerTipExceptions::class);
        $dealerTipSessionService->update($data['id'], $data);
    }

    public function testUpdateWithException(): void
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\DealerTipSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $dealerTipSessionService->update($data['id'], $data);
    }

    public function testDelete(): void
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new DealerTipSessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $expectedDealerTip = new DealerTipSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedDealerTip)
        )/*->willReturn('anything')*/;

        $dealerTipSessionService->delete($data['id']);
    }

    public function testDeleteWithDealerTipNotFoundException(): void
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          DealerTipExceptions::dealerTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(DealerTipExceptions::class);
        $dealerTipSessionService->delete($data);
    }

    public function testDeleteWithException(): void
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\DealerTipSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $dealerTipSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException(): void
    {
        // $data without idSession
          $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(BaseException::class);
        $dealerTipSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealerTipNonNumeric(): void
    {
        $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 'a non numeric value',
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(DealerTipExceptions::class);
        $dealerTipSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealearTipNegativeValue(): void
    {
        $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => -50,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager, []);

        $this->expectException(DealerTipExceptions::class);
        $dealerTipSessionService->checkGenericInputData($data);
    }
}
