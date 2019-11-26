<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\Pokerclub\Entity\CommissionSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Service\BaseService;

class CommissionSessionEntityTest extends TestCase
{

  public function testCreateWithParams(): void
  {
    $id = 1;
    $hour = date_create('2019-06-26 19:00:00');
    $amount = 25;
    $session = new SessionEntity();

    $commission = new CommissionSessionEntity(
      $id,
      $hour,
      $amount,
      $session
    );

    $this->assertEquals(1, $commission->getId());
    $this->assertEquals($hour, $commission->getHour());
    $this->assertEquals($amount, $commission->getCommission());
    $this->assertSame($session, $commission->getSession());
  }

  public function testGetSessionUndefined(): void
  {
    $commission = new CommissionSessionEntity();

    $this->assertEquals(null, $commission->getSession());
  }

  public function testToArray(): void
  {

    $session = new SessionEntity();
    $session->setId(2);

    $commission = new CommissionSessionEntity();
    $commission->setId(1);
    $commission->setSession($session);
    $commission->setHour(date_create('2019-06-26 19:00:00'));
    $commission->setCommission(100);


    $expectedArray = [
      'id'        => 1,
      'idSession' => 2,
      'hour'      => date_create('2019-06-26 19:00:00'),
      'commission' => 100
    ];  

    $this->assertEquals($expectedArray, $commission->toArray());
  }
}
