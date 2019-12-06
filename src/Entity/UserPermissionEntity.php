<?php

namespace Solcre\Pokerclub\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permisos_usuarios")
 */
class UserPermissionEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @ORM\Column(type="integer", name="d")
     */
    protected $delete;


    /**
     * @ORM\Column(type="integer", name="r")
     */
    protected $read;


    /**
     * @ORM\Column(type="integer", name="w")
     */
    protected $write;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\Pokerclub\Entity\UserEntity")
     * @ORM\JoinColumn(name="id_usuario", referencedColumnName="id")
     */
    protected $user;


    /**
     * @ORM\ManyToOne(targetEntity="Solcre\Pokerclub\Entity\PermissionEntity")
     * @ORM\JoinColumn(name="id_permiso", referencedColumnName="id")
     */
    protected $permission;


    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }


    /**
     * Gets the value of user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->user->getId();
    }


    /**
     * Sets the value of user.
     *
     * @param mixed $user the user group
     *
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    /**
     * Gets the value of delete.
     *
     * @return mixed
     */
    public function getDelete()
    {
        return $this->delete;
    }


    /**
     * Sets the value of delete.
     *
     * @param mixed $delete the delete
     *
     * @return self
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;
    }


    /**
     * Gets the value of read.
     *
     * @return mixed
     */
    public function getRead()
    {
        return $this->read;
    }


    /**
     * Sets the value of read.
     *
     * @param mixed $read the read
     *
     * @return self
     */
    public function setRead($read)
    {
        $this->read = $read;
    }


    /**
     * Gets the value of write.
     *
     * @return mixed
     */
    public function getWrite()
    {
        return $this->write;
    }


    /**
     * Sets the value of write.
     *
     * @param mixed $write the write
     *
     * @return self
     */
    public function setWrite($write)
    {
        $this->write = $write;
    }


    /**
     * Gets the value of permission.
     *
     * @return mixed
     */
    public function getPermission()
    {
        return $this->permission->getId();
    }


    /**
     * Sets the value of permission.
     *
     * @param mixed $permission the permission
     *
     * @return self
     */
    public function setPermission($permission): ?self
    {
        $this->permission = $permission;
    }
}
