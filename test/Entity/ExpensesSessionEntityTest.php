<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;

class ExpensesSessionEntityTest extends TestCase
{

  public function testCreateWithParams(): void
  {
    $id = 1;
    $description = 'dinner';
    $amount = 25;
    $session = new SessionEntity();

    $expenditure = new ExpensesSessionEntity(
      $id,
      $session,
      $description,
      $amount
    );

    $this->assertEquals($id, $expenditure->getId());
    $this->assertSame($session, $expenditure->getSession());
    $this->assertEquals($description, $expenditure->getDescription());
    $this->assertEquals($amount, $expenditure->getAmount());
  }

  public function testToArray(): void
  {

    $session = new SessionEntity();
    $session->setId(2);

    $expenditure = new ExpensesSessionEntity();
    $expenditure->setId(1);
    $expenditure->setSession($session);
    $expenditure->setDescription('dinner');
    $expenditure->setAmount(100);


    $expectedArray = [
      'id'          => 1,
      'idSession'   => 2,
      'description' => 'dinner',
      'amount'      => 100
    ];  

    $this->assertEquals($expectedArray, $expenditure->toArray());
  }
}
