<?php
namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\Pokerclub\Repository\BuyinSessionRepository")
 * @ORM\Table(name="session_buyins")
 */
class BuyinSessionEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="amount_of_cash_money")
     */
    protected $amountCash;


    /**
     * @ORM\Column(type="integer", name="amount_of_credit_money")
     */
    protected $amountCredit;


    /**
     * @ORM\Column(type="integer", name="currency_id")
     */
    protected $currency;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $hour;


    /**
     * @ORM\Column(type="integer", name="approved")
     */
    protected $isApproved;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\Pokerclub\Entity\UserSessionEntity", inversedBy="buyins")
     * @ORM\JoinColumn(name="session_user_id",                              referencedColumnName="id")
     */
    protected $userSession;


    public function __construct(
        $id = null,
        $amountCash = null,
        $amountCredit = null,
        UserSessionEntity $userSession = null,
        $hour = null,
        $currency = null,
        $isApproved = null
    ) {
        $this->setId($id);
        $this->setamountCash($amountCash);
        $this->setamountCredit($amountCredit);
        $this->setUserSession($userSession);
        $this->setHour($hour);
        $this->setCurrency($currency);
        $this->setIsApproved($isApproved);
    }

    // @codeCoverageIgnoreStart
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getIdSession()
    {
        $userSession = $this->getUserSession();

        if (!($userSession instanceof UserSessionEntity)) {
            return null;
        }

        $session = $userSession->getSession();

        return ($session instanceof SessionEntity) ?
        $session->getId() :
        null;
    }

    public function getSessionUserId()
    {
        $userSession = $this->getUserSession();

        return ($userSession instanceof UserSessionEntity) ?
        $userSession->getId() :
        null;
    }

    public function getAmountCash()
    {
        return $this->amountCash;
    }

    public function setAmountCash($amountCash)
    {
        $this->amountCash = $amountCash;
        return $this;
    }

    public function getAmountCredit()
    {
        return $this->amountCredit;
    }

    public function setAmountCredit($amountCredit)
    {
        $this->amountCredit = $amountCredit;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getHour()
    {
        return $this->hour;
    }

    public function setHour($hour)
    {
        $this->hour = $hour;
        return $this;
    }

    public function getIsApproved()
    {
        return $this->isApproved;
    }

    public function setIsApproved($isApproved)
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function getSession()
    {
        return $this->getUserSession()->getSession();
    }

    public function getUserSession()
    {
        return $this->userSession;
    }

    public function setUserSession(UserSessionEntity $userSession = null)
    {
        $this->userSession = $userSession;
        return $this;
    }
    // @codeCoverageIgnoreEnd
    public function toArray()
    {
        $ret = [
        'id'           => $this->getId(),
        'idSession'    => $this->getIdSession(),
        'amountCash'   => $this->getAmountCash(),
        'amountCredit' => $this->getAmountCredit(),
        'hour'         => $this->getHour(),
        'approved'     => $this->getIsApproved()
        ];
  
        $userSession = $this->getUserSession();

        if ($userSession instanceof UserSessionEntity) {
            $ret['user_session'] = $userSession->toArray();
        }
        
        return $ret;
    }
}
