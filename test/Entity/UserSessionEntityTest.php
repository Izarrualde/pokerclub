<?php

use PHPUnit\Framework\TestCase;
// use ReflectionMethod;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\Common\Collections\ArrayCollection;

class UserSessionEntityTest extends TestCase
{

  public function testCreate()
  {
    $userSession = new UserSessionEntity();

    $this->assertInstanceOf(ArrayCollection::class, $userSession->getBuyins());
  }

 public function testCreateWithParams()
  {
    $id = 10;
    $session = new SessionEntity(3);
    $idUser = 11;
    $isApproved = true;
    $accumulatedPoints = 100;
    $cashout = 500;
    $start = date_create('2019-06-26 19:00:00');
    $end = date_create('2019-06-26 23:00:00');
    $minimumHours = null;
    $user = New UserEntity(5);

    $userSession = new UserSessionEntity(
      $id,
      $session,
      $idUser,
      $isApproved,
      $accumulatedPoints,
      $cashout,
      $start,
      $end,
      $minimumHours,
      $user
    );

    $this->assertEquals($id, $userSession->getId());
    $this->assertTrue($userSession->getIsApproved());
    $this->assertEquals($accumulatedPoints, $userSession->getAccumulatedPoints());
    $this->assertEquals($cashout, $userSession->getCashout());
    $this->assertEquals($start, $userSession->getStart());
    $this->assertEquals($end, $userSession->getEnd());
    $this->assertSame($session, $userSession->getSession());
    $this->assertSame($user, $userSession->getUser());
    $this->assertEquals(3, $userSession->getSession()->getId());
    $this->assertEquals(5, $userSession->getUser()->getId());
  }

  public function testGetDurationWithMoreThanOneDay() {

    $userSession = new UserSessionEntity();

    $userSession->setStart(date_create('2019-06-25 19:00:00'));
    $userSession->setEnd(date_create('2019-06-27 20:00:00'));

    $this->assertEquals(49, $userSession->getDuration());
  } 

  public function testGetDurationWithDecimals() {

    $userSession = new UserSessionEntity();

    $userSession->setStart(date_create('2019-06-26 19:00:00'));
    $userSession->setEnd(date_create('2019-06-26 20:15:00'));

    $this->assertEquals(1.25, $userSession->getDuration());
  }

  public function testGetDurationRounding() {


    $userSession = new UserSessionEntity();

    $userSession->setStart(date_create('2019-06-26 19:00:00'));
    $userSession->setEnd(date_create('2019-06-26 20:29:59'));

    $this->assertEquals(1.25, $userSession->getDuration());
  }

  public function testGetCashin(){

    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);

    $this->assertEquals(650, $userSession->getCashin());
  }

  public function testGetCashinWithoutBuyin()
  {
    $userSession = new UserSessionEntity();

    $this->assertEquals(0, $userSession->getCashin());
  }
  public function testGetResult()
  {
    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);
    $userSession->setCashout(1000);

    $this->assertEquals(350, $userSession->getResult());
  }

  public function testGetResultWithoutCashout()
  {
    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);

    $this->assertEquals(-650, $userSession->getResult());
  }

  public function testGetTotalCreditWithoutBuyins()
  {
    $userSession = new UserSessionEntity();

    $this->assertEquals(0, $userSession->getTotalCredit());
  }

  public function testGetTotalCreditWithBuyins()
  {
    $userSession = new UserSessionEntity();

    $buyin = new BuyinSessionEntity(1, null, null, $userSession);
    $buyin->setAmountCash(500);
    $buyin->setAmountCredit(100);

    $buyin2 = new BuyinSessionEntity(2, null, null, $userSession);
    $buyin2->setAmountCash(15);
    $buyin2->setAmountCredit(35);

    $buyins = New ArrayCollection();
    $buyins[] = $buyin;
    $buyins[] = $buyin2;

    $userSession->setBuyins($buyins);

    $this->assertEquals(135, $userSession->getTotalCredit());
  }

  public function testInminutes()
  {
    $date1 = new \DateTime('2019-07-26T22:00:00');
    $date2 = new \DateTime('2019-07-27T22:00:00');

    $userSession = new UserSessionEntity();

    $this->assertEquals(1440, $userSession->inMinutes($date1, $date2));

  }



  // 1
  public function testGetPercentagePlayedEnclosing()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T23:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T20:00:00');
    $to = new \DateTime('2019-06-26T21:00:00');


   $this->assertEquals(100, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 2
  public function testGetPercentagePlayedEnclosingAndStartTouching()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T23:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T19:00:00');
    $to = new \DateTime('2019-06-26T21:00:00');


   $this->assertEquals(100, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 3
  public function testGetPercentagePlayedEnclosingAndEndTouching()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T23:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T20:00:00');
    $to = new \DateTime('2019-06-26T23:00:00');


   $this->assertEquals(100, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 4
  public function testGetPercentagePlayedAfter()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T23:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T17:00:00');
    $to = new \DateTime('2019-06-26T18:00:00');


   $this->assertEquals(0, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 5
  public function testGetPercentagePlayedAfterAndTouchin()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T23:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T17:00:00');
    $to = new \DateTime('2019-06-26T19:00:00');


   $this->assertEquals(0, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 6
  public function testGetPercentagePlayedBefore()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T21:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T22:00:00');
    $to = new \DateTime('2019-06-26T23:00:00');


   $this->assertEquals(0, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 7
  public function testGetPercentagePlayedBeforeAndTouching()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T21:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T21:00:00');
    $to = new \DateTime('2019-06-26T22:00:00');


   $this->assertEquals(0, $userSession->getPercentagePlayed($from, $to)); 
  }


  // 8
  public function testGetPercentagePlayedStartInside()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T19:00:00');
    $endUserSession = new \DateTime('2019-06-26T23:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T17:00:00');
    $to = new \DateTime('2019-06-26T21:00:00');


   $this->assertEquals(50, $userSession->getPercentagePlayed($from, $to)); 
  }


  // 9
  public function testGetPercentagePlayedEndInside()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T17:00:00');
    $endUserSession = new \DateTime('2019-06-26T20:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T19:00:00');
    $to = new \DateTime('2019-06-26T23:00:00');


   $this->assertEquals(25, $userSession->getPercentagePlayed($from, $to)); 
  }

  //10
  public function testGetPercentagePlayedInside()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T17:00:00');
    $endUserSession = new \DateTime('2019-06-26T18:30:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T16:00:00');
    $to = new \DateTime('2019-06-26T22:00:00');


   $this->assertEquals(25, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 11
  public function testGetPercentagePlayedInsideAndStartTouching()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T14:00:00');
    $endUserSession = new \DateTime('2019-06-26T16:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T14:00:00');
    $to = new \DateTime('2019-06-26T22:00:00');


   $this->assertEquals(25, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 12
  public function testGetPercentagePlayedInsideAndEndTouching()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T22:00:00');
    $endUserSession = new \DateTime('2019-06-26T20:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T14:00:00');
    $to = new \DateTime('2019-06-26T22:00:00');


   $this->assertEquals(25, $userSession->getPercentagePlayed($from, $to)); 
  }

  // 13
  public function testGetPercentagePlayedExactMatch()
  {
    $userSession = new UserSessionEntity();
    $startUserSession = new \DateTime('2019-06-26T17:00:00');
    $endUserSession = new \DateTime('2019-06-26T19:00:00');
    $userSession->setStart($startUserSession);
    $userSession->setEnd($endUserSession);

    $from = new \DateTime('2019-06-26T17:00:00');
    $to = new \DateTime('2019-06-26T19:00:00');


   $this->assertEquals(100, $userSession->getPercentagePlayed($from, $to)); 
  }

  public function testToArray()
  {
    $user = new UserEntity(3);
    $user->setName('Diego');
    $user->setLastname('Rod');

    $session = new SessionEntity(2);

    $userSession = new UserSessionEntity();
    $userSession->setUser($user);
    $userSession->setSession($session);
    $userSession->setId(1);
    $userSession->setIdUser($user->getId());
    $userSession->setIsApproved(1);
    $userSession->setCashout(0);
    $userSession->setStart(date_create('2019-06-26 19:00:00'));
    $userSession->setEnd(date_create('2019-06-26 23:00:00'));

    $buyin1 = new BuyinSessionEntity(1, 1000, 200, $userSession);
    $buyins1 = New ArrayCollection();
    $buyins1[] = $buyin1;
    $userSession->setBuyins($buyins1);

    $expectedArray = [
      'id'             => 1,
      'idSession'      => 2,
      'idUser'         => 3,
      'isApproved'     => 1,
      'cashout'        => 0,
      'startTime'      => date_create('2019-06-26 19:00:00'),
      'endTime'        => date_create('2019-06-26 23:00:00'),
      'cashin'         => 1200,
      'totalCredit'    => 200,
      'totalCash'      => 1000,
      'points'         => 0,
      'user'           => $user->toArray(),
      'session'        =>$session->toArray(),
      'minimumMinutes' => null
    ];

    $this->assertEquals($expectedArray, $userSession->toArray());
  }
}
