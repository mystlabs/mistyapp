<?php

namespace MistyApp\View;

use MistyApp\Component\Initializer;
use MistyApp\Controller\Redirecter;
use MistyDepMan\Container;

abstract class Handler implements \MistyForms\Handler
{
    use Container, Initializer, Redirecter;

    public function initializeView($view)
    {

    }

    public function getProvider()
    {
        return $this->provider;
    }
}
