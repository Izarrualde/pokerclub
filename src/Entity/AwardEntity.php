<?php
namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Solcre\SolcreFramework2\Common\BaseRepository") @ORM\Table(name="awards")
 * @codeCoverageIgnore
 */
class AwardEntity
{

    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     *
     */
    protected $id;


    /** @ORM\Column(type="datetime", name="created_at")
     *
     */
    protected $createdAt;


    /** @ORM\Column(type="string")
     *
     */
    protected $name;


    /** @ORM\Column(type="string")
     *
     */
    protected $class;


    /** @ORM\Column(type="text")
     *
     */
    protected $description;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class): void
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }
}
