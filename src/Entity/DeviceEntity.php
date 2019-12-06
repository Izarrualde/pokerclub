<?php

namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Solcre\SolcreFramework2\Common\BaseRepository")
 * @ORM\Table(name="devices")
 */
class DeviceEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;


    /**
     * @ORM\Column(type="string", name="device_token")
     */
    protected $deviceToken;


    /**
     * @ORM\Column(type="string")
     */
    protected $platform;


    /**
     * @ORM\Column(type="datetime", name="added_date")
     */
    protected $addedDate;


    /**
     * @ORM\OneToOne(targetEntity="Solcre\Pokerclub\Entity\UserEntity")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getUser()
    {
        return $this->user instanceof UserEntity ? $this->user->getId() : null;
    }

    /**
     * @param UserEntity|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getDeviceToken()
    {
        return $this->deviceToken;
    }

    /**
     * @param string $deviceToken
     */
    public function setDeviceToken($deviceToken)
    {
        $this->deviceToken = $deviceToken;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }

    /**
     * @return DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

    /**
     * @param DateTime $addedDate
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;
    }
}
