<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\ServiceTipSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\ServiceTipExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Common\BaseRepository;

class ServiceTipSessionServiceTest extends TestCase
{
    public function testAdd(): void
    {
        $data = [
          'id'        => 1, 
          'hour'      => '2019-07-04T19:00', 
          'serviceTip' => 100,
          'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $expectedServiceTip = new ServiceTipSessionEntity();
        $expectedServiceTip->setHour(new \DateTime($data['hour']));
        $expectedServiceTip->setServiceTip($data['serviceTip']);
        $session = new SessionEntity(3);
        $expectedServiceTip->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
           $this->equalTo($expectedServiceTip)
        );

        $serviceTipSessionService->add($data);

    }

    public function testUpdate(): void
    {
        $data = [
            'id'         => 1,
            'hour'       => '2019-07-04T19:00',
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new ServiceTipSessionEntity(
                1,
                new \DateTime('2019-07-04T15:00'),
                80,
                new SessionEntity(3)
            )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $expectedServiceTip = new ServiceTipSessionEntity();
        $expectedServiceTip->setId(1);
        $expectedServiceTip->setHour(new \DateTime($data['hour']));
        $expectedServiceTip->setServiceTip($data['serviceTip']);
        $session = new SessionEntity(3);
        $expectedServiceTip->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
           $this->equalTo($expectedServiceTip)
        );

        $serviceTipSessionService->update($data['id'], $data);
    }

    public function testUpdateWithIncompleteDataException(): void
    {
        // $data without id
        $data = [
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $idNull = null;

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $this->expectException(BaseException::class);

        $fakeIdForTesting = 1111;
        $serviceTipSessionService->update($idNull, $data);
    }

    public function testUpdateWithServiceTipNotFoundException(): void
    {
        $data = [
            'id'         => 'a non-existent id',
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
            ServiceTipExceptions::serviceTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $this->expectException(ServiceTipExceptions::class);

        $serviceTipSessionService->update($data['id'], $data);
    }

    public function testUpdateWithException(): void
    {
        $data = [
            'id'         => 'a non-existent id',
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
            new \Exception('Solcre\Pokerclub\Entity\ServiceTipSessionEntity' . ' Entity not found', 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $serviceTipSessionService->update($data['id'], $data);
    }

    public function testDelete(): void
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new ServiceTipSessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $expectedServiceTip = new ServiceTipSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedServiceTip)
        )/*->willReturn('anything')*/;

        $serviceTipSessionService->delete($data['id']);
    }

    public function testDeleteWithServiceTipNotFoundException(): void
    {
        $data = [
            'id'         => 'a non-existent id',
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          ServiceTipExceptions::serviceTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $this->expectException(ServiceTipExceptions::class);

        $serviceTipSessionService->delete($data);
    }

    public function testDeleteWithException(): void
    {
        $data = [
            'id'         => 'a non-existent id',
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\ServiceTipSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);
        
        $this->expectException(\Exception::class);
        $serviceTipSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException(): void
    {
        // $data without idSession
          $data = [
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $this->expectException(BaseException::class);

        $serviceTipSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithServiceTipNonNumeric()
    {
        $data = [
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 'a non numeric value',
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $this->expectException(ServiceTipExceptions::class);

        $serviceTipSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithServiceTipNegativeValue(): void
    {
        $data = [
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => -50,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager, []);

        $this->expectException(ServiceTipExceptions::class);

        $serviceTipSessionService->checkGenericInputData($data);
    }

}
