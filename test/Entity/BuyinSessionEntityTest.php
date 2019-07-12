<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\lmsuy\Entity\BuyinSessionEntity;
use Solcre\lmsuy\Entity\userSessionEntity;
use Solcre\lmsuy\Entity\SessionEntity;

class BuyinSessionEntityTest extends TestCase
{

  public function testCreateWithParams()
  {    
    $id = 5;
    $amountCash = 500;
    $amountCredit = 100;
    $currency = 1;
    $hour = date_create('2019-06-26 19:00:00');
    $isApproved = 1;
    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(
      $id,
      $amountCash,
      $amountCredit,
      $userSession,
      $hour,
      $currency,
      $isApproved
    );

    $this->assertEquals($id, $buyin->getId());
    $this->assertEquals($amountCash, $buyin->getAmountCash());
    $this->assertEquals($amountCredit, $buyin->getAmountCredit());
    $this->assertEquals($hour, $buyin->getHour());
    $this->assertEquals($currency, $buyin->getCurrency());
    $this->assertEquals($isApproved, $buyin->getIsApproved());
    $this->assertSame($userSession, $buyin->getUserSession());
  }

  public function testToArray()
  {

    $session = new SessionEntity();
    $session->setId(2);

    $userSession = new UserSessionEntity(3);
    $userSession->setSession($session);

    $buyin = new BuyinSessionEntity();
    $buyin->setUserSession($userSession);
    $buyin->setId(1);
    $buyin->setAmountCash(300);
    $buyin->setAmountCredit(200);
    //$buyin->setUserSession($user);
    $buyin->setHour(date_create('2019-06-26 19:00:00'));
    //$buyin->setCurrency(1);
    //$buyin->setIsApproved(1);

    $expectedArray = [
      'id'           => 1,
      'idSession'    => 2,
      'amountCash'   => 300,
      'amountCredit' => 200,
      'hour'         => date_create('2019-06-26 19:00:00'),
      'user_session' => $userSession->toArray()
    ]; 

    $this->assertEquals($expectedArray, $buyin->toArray());
  }
}