<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\ExpensesSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\ExpensesInvalidException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Exception\ExpenditureNotFoundException;
use Solcre\SolcreFramework2\Common\BaseRepository;

class ExpensesSessionServiceTest extends TestCase
{
    public function testAdd()
    {
        $data = [
          'id'          => 1, 
          'description' => 'gasto de sesion', 
          'amount'      => 100,
          'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('persist')->willReturn(true);
        $mockedEntityManager->method('getReference')->willReturn(new SessionEntity(3));

        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $expectedExpenditure    = new ExpensesSessionEntity();
        $expectedExpenditure->setDescription($data['description']);
        $expectedExpenditure->setAmount($data['amount']);
        $session = new SessionEntity(3);
        $expectedExpenditure->setSession($session);

        $mockedEntityManager->expects($this->once())
        ->method('persist')
        ->with(
            $this->equalTo($expectedExpenditure)
        )/*->willReturn('anything')*/;

        $expensesSessionService->add($data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdate()
    {
        $data = [
          'id'          => 1, 
          'description' => 'gasto de sesion', 
          'amount'      => 100,
          'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(
            new ExpensesSessionEntity(
              1,
              new SessionEntity(3),
              'gasto de sesion',
              100
            )
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $expectedExpenditure    = new ExpensesSessionEntity();
        $expectedExpenditure->setId(1);
        $expectedExpenditure->setDescription($data['description']);
        $expectedExpenditure->setAmount($data['amount']);
        $expectedExpenditure->setSession(new SessionEntity(3));

        $mockedEntityManager->expects($this->once())
        ->method('flush')
        ->with(
            $this->equalTo($expectedExpenditure)
        );

        $expensesSessionService->update($data['id'], $data);
        // y que se llame metodo flush con anythig
    }

    public function testUpdateWithIncompleteDataException()
    {
        // $data without id
        $data = [
            'description' => 'description', 
            'amount'      => 100,
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(IncompleteDataException::class);

        $fakeIdForTesting = 1111;
        $expensesSessionService->update($fakeIdForTesting, $data);
    }

    public function testUpdateWithExpenditureNotFoundException()
    {
        $data = [
            'id'          => 'an unexisting id',
            'description' => 'description', 
            'amount'      => 100,
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new ExpenditureNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpenditureNotFoundException::class);
        $expensesSessionService->update($data['id'], $data);
    }

    public function testUpdateWithException()
    {
        $data = [
            'id'          => 'an unexisting id',
            'description' => 'description', 
            'amount'      => 100,
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\ExpenditureSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $expensesSessionService->update($data['id'], $data);
    }

    public function testDelete()
    {
        $data = [
          'id' => 1
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('remove')->willReturn(true);
        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->willReturn(new ExpensesSessionEntity(1));

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $expectedExpenditure = new ExpensesSessionEntity($data['id']);

        $mockedEntityManager->expects($this->once())
        ->method('remove')
        ->with(
            $this->equalTo($expectedExpenditure)
        )/*->willReturn('anything')*/;

        $expensesSessionService->delete($data['id']);
    }

    public function testDeleteWithExpenditureNotFoundException()
    {
        $data = [
            'id'          => 'an unexisting id',
            'description' => 'description', 
            'amount'      => 100,
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new ExpenditureNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpenditureNotFoundException::class);
        $expensesSessionService->delete($data);
    }

    public function testDeleteWithException()
    {
        $data = [
            'id'          => 'an unexisting id',
            'description' => 'description', 
            'amount'      => 100,
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $mockedEntityManager->method('flush')->willReturn(true);

        $mockedRepository = $this->createMock(BaseRepository::class);
        $mockedRepository->method('find')->will($this->throwException(
          new \Exception('Solcre\Pokerclub\Entity\DealerTipSessionEntity' . " Entity not found", 404))
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(\Exception::class);
        $expensesSessionService->delete($data);
    }

    public function testCheckGenericInputDataWithIncompleteDataException()
    {
        // $data without idSession
          $data = [
            'description' => 'description', 
            'amount'      => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(IncompleteDataException::class);
        $expensesSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealerTipNonNumeric()
    {
        $data = [
            'description' => 'description', 
            'amount'      => 'a non numeric value',
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpensesInvalidException::class);
        $expensesSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealearTipNegativeValue()
    {
        $data = [
            'description' => 'description', 
            'amount'      => -50,
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpensesInvalidException::class);
        $expensesSessionService->checkGenericInputData($data);
    }
}
