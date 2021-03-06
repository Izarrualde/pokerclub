<?php
namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 * @ORM\Entity(repositoryClass="Solcre\SolcreFramework2\Common\BaseRepository")
 * @ORM\Table(name="session_service_tips")
 */
class ServiceTipSessionEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\Pokerclub\Entity\SessionEntity", inversedBy="sessionServiceTips")
     * @ORM\JoinColumn(name="session_id", referencedColumnName="id")
     */
    protected $session;


    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $hour;


    /**
     * @ORM\Column(type="integer", name="service_tip")
     */
    protected $serviceTip;


    public function __construct(
        $id = null,
        $hour = '',
        $tip = null,
        $session = null
    ) {
        $this->setId($id);
        $this->setHour($hour);
        $this->setServiceTip($tip);
        $this->setSession($session);
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
        return $this->getSession()->getId();
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

    public function getServiceTip()
    {
        return $this->serviceTip;
    }

    public function setServiceTip($tip): self
    {
        $this->serviceTip = $tip;
        return $this;
    }
    
    // @codeCoverageIgnoreEnd
    public function toArray(): array
    {
        return  [
        'id'         => $this->getId(),
        'idSession'  => $this->getSession()->getId(),
        'hour'       => $this->getHour(),
        'serviceTip' => $this->getServiceTip()
        ];
    }
}
