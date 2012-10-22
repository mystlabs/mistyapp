<?php

namespace MistyApp\Component;

use MistyDepMan\Container;
use MistyDoctrine\Transaction\Transactional;

class Service extends Transactional
{
    use Container, Initializer;

    /**
     * @param \Exception $exception
     * @param \Callable $errorCallback
     */
    protected function handleError($exception, $errorCallback)
    {
        echo $exception->getMessage();
    }

    /**
     * @see Initializer
     */
    function getProvider()
    {
        return $this->provider;
    }
}
