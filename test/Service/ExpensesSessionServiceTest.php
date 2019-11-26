<?php

use PHPUnit\Framework\TestCase;
use Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\ExpensesSessionService;
use Doctrine\ORM\EntityManager;
use Solcre\Pokerclub\Exception\BaseException;
use Solcre\Pokerclub\Exception\ExpensesExceptions;
use Solcre\Pokerclub\Exception\SessionExceptions;
use Solcre\SolcreFramework2\Common\BaseRepository;

class ExpensesSessionServiceTest extends TestCase
{
    public function testAdd(): void
    {
        $data = [
            'id'          => 1,
            'description' => 'gasto de sesión',
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
        );

        $expensesSessionService->add($data);
    }

    public function testUpdate(): void
    {
        $data = [
            'id'          => 1,
            'description' => 'gasto de sesión',
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
                'gasto de sesión',
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
    }

    public function testUpdateWithIncompleteDataException(): void
    {
        // $data without id
        $data = [
            'description' => 'description', 
            'amount'      => 100,
            'idSession'   => 3
        ];

        $idNull = null;

        $mockedEntityManager = $this->createMock(EntityManager::class);

        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(BaseException::class);

        $expensesSessionService->update($idNull, $data);
    }

    public function testUpdateWithExpenditureNotFoundException(): void
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
          ExpensesExceptions::expenditureNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpensesExceptions::class);

        $expensesSessionService->update($data['id'], $data);
    }

    public function testUpdateWithException(): void
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

    public function testDelete(): void
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
        );

        $expensesSessionService->delete($data['id']);
    }

    public function testDeleteWithExpenditureNotFoundException(): void
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
          ExpensesExceptions::expenditureNotFoundException())
        );

        $mockedEntityManager->method('getRepository')->willReturn($mockedRepository);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpensesExceptions::class);

        $expensesSessionService->delete($data);
    }

    public function testDeleteWithException(): void
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

    public function testCheckGenericInputDataWithIncompleteDataException(): void
    {
        // $data without idSession
        $data = [
            'description' => 'description',
            'amount'      => 100,
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(BaseException::class);

        $expensesSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealerTipNonNumeric(): void
    {
        $data = [
            'description' => 'description', 
            'amount'      => 'a non numeric value',
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpensesExceptions::class);

        $expensesSessionService->checkGenericInputData($data);
    }

    public function testCheckGenericInputDataWithDealearTipNegativeValue(): void
    {
        $data = [
            'description' => 'description', 
            'amount'      => -50,
            'idSession'   => 3
        ];

        $mockedEntityManager = $this->createMock(EntityManager::class);
        $expensesSessionService = new ExpensesSessionService($mockedEntityManager, []);

        $this->expectException(ExpensesExceptions::class);

        $expensesSessionService->checkGenericInputData($data);
    }
}
