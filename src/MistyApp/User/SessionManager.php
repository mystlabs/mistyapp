<?php

namespace MistyApp\User;

use MistyApp\User\UserInterface;
use MistyApp\User\UserStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class SessionManager
{
    const USER_ID = '_USER_ID';

    /** @var SessionStorageInterface */
    private $sessionStorage;

    /** @var UserStorageInterface */
    private $userStorage;

    /** @var Session */
    private $session;

    /** @var UserInterface */
    private $user;

    /**
     * @param SessionStorageInterface $sessionStorage
     * @param UserStorageInterface $userStorage
     */
    public function __construct($sessionStorage, $userStorage)
    {
        $this->sessionStorage = $sessionStorage;
        $this->userStorage = $userStorage;

        $this->session = new Session($this->sessionStorage);
    }

    /**
     * Check if the user is logged in or not
     *
     * @return bool Whether the user is logged in or not
     */
    public function isLoggedIn()
    {
        return $this->getUser() !== null;
    }


    /**
     * @return Session
     */
    function getSession()
    {
        return $this->session;
    }

    /**
     * Return the User object, or null if the user is not signed in
     *
     * @return UserInterface
     */
    function getUser()
    {
        if ($this->user === null) {
            $this->readUserFromSession();
        }

        return $this->user;
    }

    /**
     * Login the given user
     *
     * @param UserInterface $user
     */
    function login($user)
    {
        $this->session->set(self::USER_ID, $user->getUserId());
    }

    /**
     * Destroy the user session
     */
    function logout()
    {
        $this->session->invalidate();
    }

    /**
     * Read the user from the session
     */
    private function readUserFromSession()
    {
        $userId = $this->session->get(self::USER_ID);
        if (!$userId) {
            // No user id, not signed in
            return;
        }

        $user = $this->userStorage->getUserById($userId);
        if (!$user) {
            // Malformed user session, removing the user id
            $this->session->remove(self::USER_ID);
            return;
        }

        $this->user = $user;
    }
}
