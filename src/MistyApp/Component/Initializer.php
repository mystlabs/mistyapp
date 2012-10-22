<?php

namespace MistyApp\Component;

use MistyDepMan\Provider;
use MistyDoctrine\ModelDao;

trait Initializer
{
    /**
     * Convenient method to obtain a proxy Dao object from the Provider
     *
     * @param string $daoClass The name of the Dao we want a proxy for
     * @return ModelDao Dao object
     */
    protected function dao($daoClass)
    {
        $provider = $this->getProvider();
        return $provider->proxy(
            $daoClass,
            $provider->lookup('entity.manager')
        );
    }

    /**
     * Convenient method to obtain a proxy Service object from the Provider
     *
     * @param string $serviceClass The name of the Service we want a proxy for
     * @return Service object
     */
    protected function service($serviceClass)
    {
        $provider = $this->getProvider();

        if (!$provider->has('dao')) {
            $provider->register('dao', new \MistyDoctrine\Dao(
                $provider->lookup('entity.manager')
            ));
        }

        return $provider->proxy(
            $serviceClass,
            $provider->lookup('dao')
        );
    }

    /**
     * @return Provider
     */
    abstract function getProvider();
}
