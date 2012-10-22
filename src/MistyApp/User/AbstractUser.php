<?php

namespace MistyApp\User;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use MistyApp\User\UserInterface;
use MistyDoctrine\Model;

abstract class AbstractUser extends Model implements UserInterface
{
    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     */
    protected $id;

    /** @Column(length=255,nullable=true) */
    protected $email;

    /** @Column(length=255,nullable=true) */
    protected $password;

    /** @Column(length=255,nullable=true) */
    protected $state;

    /** @Column(type="datetime") */
    protected $created_on;

    /** @Column(type="datetime",nullable=true) */
    protected $last_login_on;

    /** @Column(length=255,nullable=true) */
    protected $groups;

    public function setPassword($password)
    {
        $phpassHash = new \Phpass\Hash;
        $this->password = $phpassHash->hashPassword($password);

        return $this;
    }

    public function checkPassword($password)
    {
        $phpassHash = new \Phpass\Hash;
        return $phpassHash->checkPassword($password, $this->password);
    }

    /**
     * Add the user to a group
     *
     * @param string $name The name of the group
     * @return string User
     * @throws InvalidArgumentException
     */
    public function addGroup($name)
    {
        if (!preg_match('/[a-z0-9-]/', $name)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid group name "%s", it can only contains letters, numbers and "-"',
                $name
            ));
        }

        if (!$this->hasGroup($name)) {

        }

        return $this;
    }

    /**
     * Remove the user from a group
     *
     * @param string $name The name of the group
     * @return User
     * @throws InvalidArgumentException
     */
    public function removeGroup($name)
    {
        if (!$this->hasGroup($name)) {
            throw new InvalidArgumentException(sprintf(
                'User "%s" is not in the group "%s"',
                $this->email,
                $name
            ));
        }

        $this->groups = str_replace(",$name", '', $this->groups);

        return $this;
    }

    /**
     * Check if the user belongs to a group
     *
     * @param string $name The name of the group
     * @return bool
     */
    public function hasGroup($name)
    {
        return strpos($this->groups, ",$name") !== false;
    }


    /**
     * Check if the user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasGroup('admin');
    }

    /**
     * Return the user ID
     *
     * @return int The ID of the user
     */
    function getUserId()
    {
        return $this->id;
    }
}
