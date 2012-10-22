<?php

namespace MistyApp\User;

interface UserInterface
{
    /**
     * Return the user ID
     *
     * @return int The ID of the user
     */
    function getUserId();
}