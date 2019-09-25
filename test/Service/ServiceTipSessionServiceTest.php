<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\ServiceTipSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\ServiceTipInvalidException;
use Solcre\Pokerclub\Repository\BaseRepository;
use Solcre\Pokerclub\Exception\ServiceTipNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;

class ServiceTipSessionServiceTest extends TestCase
{
    public function testAdd()
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

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $expectedServiceTip = new ServiceTipSessionEntity();
        $expectedServiceTip->setHour(new \DateTime($data['hour']));
        $expectedServiceTip->setServiceTip($data['serviceTip']);
        $session = new SessionEntity(3);
        $expectedServiceTip->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
           $this->equalTo($expectedServiceTip)
        )/*->willReturn('anything')*/;

        $serviceTipSessionService->add($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdate()
    {
        $data = [
          'id'        => 1, 
          'hour'      => '2019-07-04T19:00', 
          'serviceTip' => 100,
          'idSession' => 3
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

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $expectedServiceTip    = new ServiceTipSessionEntity();
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

        $serviceTipSessionService->update($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException()
    {
        // $data without id
        $data = [
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
        $serviceTipSessionService->update($data);
    }

    public function testUpdateWithServiceTipNotFoundException()
    {
        $data = [
            'id'         => 'an unexisting id',
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new ServiceTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(ServiceTipNotFoundException::class);
        $serviceTipSessionService->update($data);
    }

    public function testUpdateWithException()
    {
        $data = [
            'id'         => 'an unexisting id',
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

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(\Exception::class);
        $serviceTipSessionService->update($data);
    }

    public function testDelete()
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new ServiceTipSessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $expectedServiceTip = new ServiceTipSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedServiceTip)
        )/*->willReturn('anything')*/;

        $serviceTipSessionService->delete($data['id']);
    }

    public function testDeleteWithServiceTipNotFoundException()
    {
        $data = [
            'id'         => 'an unexisting id',
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new ServiceTipNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(ServiceTipNotFoundException::class);
        $serviceTipSessionService->delete($data);
    }

    public function testDeleteWithException()
    {
        $data = [
            'id'         => 'an unexisting id',
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

        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(\Exception::class);
        $serviceTipSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException()
    {
        // $data without idSession
          $data = [
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
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
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(ServiceTipInvalidException::class);
        $serviceTipSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithServiceTipNegativeValue()
    {
        $data = [
            'hour'       => '2019-07-04T19:00', 
            'serviceTip' => -50,
            'idSession'  => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $serviceTipSessionService = new ServiceTipSessionService($mockedEntityManager);

        $this->expectException(ServiceTipInvalidException::class);
        $serviceTipSessionService->checkGenericInputData($data);
    }

}
