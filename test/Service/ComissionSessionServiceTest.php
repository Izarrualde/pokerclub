<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\ComissionSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\ComissionSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\ComissionInvalidException;
use Solcre\Pokerclub\Exception\ComissionNotFoundException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Service\BaseService;
use Solcre\Pokerclub\Repository\BaseRepository;

class ComissionSessionServiceTest extends TestCase
{
    public function testAdd()
    {
        $data = [
          'id'        => 1, 
          'hour'      => '2019-07-04T19:00', 
          'comission' => 100,
          'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $expectedComission    = new ComissionSessionEntity();
        $expectedComission->setHour(new \DateTime($data['hour']));
        $expectedComission->setComission($data['comission']);
        $session = new SessionEntity(3);
        $expectedComission->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
           $this->equalTo($expectedComission)
        )/*->willReturn('anything')*/;

        $comissionSessionService->add($data);
        // y que se llame metodo flush con anythig

    }

    public function testUpdate()
    {
        $data = [
          'id'        => 1, 
          'hour'      => '2019-07-04T19:00', 
          'comission' => 100,
          'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
            new ComissionSessionEntity(
              1,
              new \DateTime('2019-07-04T12:00'),
              80,
              new SessionEntity(3)
            )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $expectedComission    = new ComissionSessionEntity();
        $expectedComission->setId(1);
        $expectedComission->setHour(new \DateTime($data['hour']));
        $expectedComission->setComission($data['comission']);
        $session = new SessionEntity(3);
        $expectedComission->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
           $this->equalTo($expectedComission)
        );

        $comissionSessionService->update($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException()
    {
        // $data without id
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'comission' => 100,
          'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
        $comissionSessionService->update($data);
    }

    public function testUpdateWithComissionNotFoundException()
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'comission' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new ComissionNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(ComissionNotFoundException::class);
        $comissionSessionService->update($data);
    }

    public function testUpdateWithException()
    {
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'comission' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(\Exception::class);
        $comissionSessionService->update($data);
    }

    public function testDelete()
    {
        $data = [
          'id'        => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new ComissionSessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $expectedComission = new ComissionSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedComission)
        )/*->willReturn('anything')*/;

        $comissionSessionService->delete($data['id']);
    }

    public function testDeleteWithComissionNotFoundException()
    {
        // $data without id
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'comission' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new ComissionNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(ComissionNotFoundException::class);
        $comissionSessionService->delete($data);
    }

    public function testDeleteWithException()
    {
        // $data without id
        $data = [
            'id'        => 'an unexisting id',
            'hour'      => '2019-07-04T19:00', 
            'comission' => 100,
            'idSession' => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\ComissionSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);

        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(\Exception::class);
        $comissionSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException()
    {
        // $data without idSession
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'comission' => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(IncompleteDataException::class);
        $comissionSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithComissionNonNumeric()
    {
        // $data with non numeric comission
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'comission' => 'a non numeric value',
          'idSession' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(ComissionInvalidException::class);
        $comissionSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithComissionNegativeValue()
    {
        // $data with negative comission
        $data = [
          'hour'      => '2019-07-04T19:00', 
          'comission' => -50,
          'idSession' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $comissionSessionService = new ComissionSessionService($mockedEntityManager);

        $this->expectException(ComissionInvalidException::class);
        $comissionSessionService->checkGenericInputData($data);
    }
}
