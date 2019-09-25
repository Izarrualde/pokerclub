<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\DealerTipSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\DealerTipInvalidException;
use Solcre\Pokerclub\Exception\DealerTipNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Repository\BaseRepository;

class DealerTipSessionServiceTest extends TestCase
{
    public function testAdd()
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

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

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
        // y que se llame metodo flush con anythig
    }

    public function testUpdate()
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

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

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

        $dealerTipSessionService->update($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException()
    {
        // $data without id
        $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
        $dealerTipSessionService->update($data);
    }

    public function testUpdateWithDealerTipNotFoundException()
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
          new DealerTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);
        $this->expectException(DealerTipNotFoundException::class);
        $dealerTipSessionService->update($data);
    }

    public function testUpdateWithException()
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

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $this->expectException(\Exception::class);
        $dealerTipSessionService->update($data);
    }

    public function testDelete()
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

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $expectedDealerTip = new DealerTipSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedDealerTip)
        )/*->willReturn('anything')*/;

        $dealerTipSessionService->delete($data['id']);
    }

    public function testDeleteWithDealerTipNotFoundException()
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
          new DealerTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $this->expectException(DealerTipNotFoundException::class);
        $dealerTipSessionService->delete($data);
    }

    public function testDeleteWithException()
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

        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $this->expectException(\Exception::class);
        $dealerTipSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException()
    {
        // $data without idSession
          $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
        $dealerTipSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealerTipNonNumeric()
    {
        $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => 'a non numeric value',
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $this->expectException(DealerTipInvalidException::class);
        $dealerTipSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealearTipNegativeValue()
    {
        $data = [
            'hour'      => '2019-07-04T19:00', 
            'dealerTip' => -50,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $dealerTipSessionService = new DealerTipSessionService($mockedEntityManager);

        $this->expectException(DealerTipInvalidException::class);
        $dealerTipSessionService->checkGenericInputData($data);
    }
}
