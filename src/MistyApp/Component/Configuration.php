<?php

namespace MistyApp\Component;

use MistyApp\Exception\ConfigurationException;

class Configuration extends ParameterBag
{
	/**
     * Retrieve a configuration parameter, or throw an exception if the parameter doesn't exist
     * and there is no default value
     *
     * @see ParameterBag
     * @throws MistyApp\Exception\ConfigurationException If th
     */
    public function get($name, $default = null)
    {
    	if (!$this->has($name) && $default === null) {
    		throw new ConfigurationException(sprintf(
    			"Missing configuration parameter '%s'",
    			$name
    		));
        }

        return parent::get($name, $default);
    }
}
