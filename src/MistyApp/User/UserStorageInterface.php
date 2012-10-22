<?php

namespace MistyApp\User;

use MistyApp\User\UserInterface;

interface UserStorageInterface
{
    /**
     * Lookup a user by ID.
     *
     * @param int $userId The id of the user
     * @return UserInterface or null
     */
    function getUserById($userId);
}
