<?php
namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\SolcreFramework2\Common\BaseRepository")
 * @ORM\Table(name="session_commissions")
 */
class CommissionSessionEntity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\Pokerclub\Entity\SessionEntity", inversedBy="sessionCommissions")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    protected $session;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $hour;


    /**
     * @ORM\Column(type="integer")
     */
    protected $commission;


    public function __construct(
        $id = null,
        $hour = null,
        $commission = null,
        $session = null
    ) {
        $this->setId($id);
        $this->setSession($session);
        $this->setHour($hour);
        $this->setCommission($commission);
    }
 
    // @codeCoverageIgnoreStart
    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }


    public function getIdSession()
    {
        $session = $this->getSession();

        return ($session instanceof SessionEntity) ?
        $session->getId() :
        null;
    }

    public function getSession(): SessionEntity
    {
        return $this->session;
    }

    public function setSession($session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getHour()
    {
        return $this->hour;
    }

    public function setHour($hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getCommission()
    {
        return $this->commission;
    }

    public function setCommission($commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    // @codeCoverageIgnoreEnd
    public function toArray(): array
    {
        return  [
        'id'        => $this->getId(),
        'idSession' => $this->getSession()->getId(),
        'hour'      => $this->getHour(),
        'commission' => $this->getCommission()
        ];
    }
}
