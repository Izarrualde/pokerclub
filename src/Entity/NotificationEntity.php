<?php

namespace Solcre\Pokerclub\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Solcre\Pokerclub\Repository\NotificationRepository")
 * @ORM\Table(name="notifications")
 */
class NotificationEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;


    /**
     * @ORM\Column(type="datetime", name="created_date")
     */
    protected $createdDate;


    /**
     * @ORM\Column(type="string")
     */
    protected $title;


    /**
     * @ORM\Column(type="string")
     */
    protected $message;


    /**
     * @ORM\Column(type="string")
     */
    protected $type;


    /**
     * @ORM\Column(type="boolean", name="was_read")
     */
    protected $read;


    /**
     * @ORM\Column(type="datetime", name="android_date_sent")
     */
    protected $androidDateSent;


    /**
     * @ORM\Column(type="datetime", name="ios_date_sent")
     */
    protected $iosDateSent;


    /**
     * @ORM\Column(type="datetime", name="windows_date_sent")
     */
    protected $windowsDateSent;


    /**
     * @ORM\Column(type="json_array", name="extra_data")
     */
    protected $extraData;


    /**
     * @ORM\OneToOne(targetEntity="Solcre\Pokerclub\Entity\UserEntity")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return DateTime
     */
    public function getAndroidDateSent(): DateTime
    {
        return $this->androidDateSent;
    }

    /**
     * @param DateTime $androidDateSent
     */
    public function setAndroidDateSent($androidDateSent): void
    {
        $this->androidDateSent = $androidDateSent;
    }

    /**
     * @return DateTime
     */
    public function getIosDateSent(): DateTime
    {
        return $this->iosDateSent;
    }

    /**
     * @param DateTime $iosDateSent
     */
    public function setIosDateSent($iosDateSent): void
    {
        $this->iosDateSent = $iosDateSent;
    }

    /**
     * @return DateTime
     */
    public function getWindowsDateSent(): DateTime
    {
        return $this->windowsDateSent;
    }

    /**
     * @param DateTime $windowsDateSent
     */
    public function setWindowsDateSent($windowsDateSent): void
    {
        $this->windowsDateSent = $windowsDateSent;
    }

    /**
     * @return int|null
     */
    public function getUser(): ?int
    {
        return $this->user instanceof UserEntity ? $this->user->getId() : null;
    }

    /**
     * @param UserEntity|null $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return boolean
     */
    public function getRead(): bool
    {
        return $this->read;
    }

    /**
     * @param boolean $read
     */
    public function setRead($read): void
    {
        $this->read = $read;
    }

    /**
     * @return string
     */
    public function getExtraData(): string
    {
        return $this->extraData;
    }

    /**
     * @param string $extraData
     */
    public function setExtraData($extraData): void
    {
        $this->extraData = $extraData;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDate(): DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param DateTime $createdDate
     */
    public function setCreatedDate($createdDate): void
    {
        $this->createdDate = $createdDate;
    }
}
