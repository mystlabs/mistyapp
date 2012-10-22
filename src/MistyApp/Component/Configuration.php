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
     * @throws ConfigurationException If the request property doesn't exist
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

    /**
     * Create a configuration object by merging N configuration files
     *
     * @param array $configurationFiles Array of configuration files
     * @return Configuration
     */
    public static function fromFiles($configurationFiles)
    {
        $values = array();
        foreach ($configurationFiles as $configuration) {
            $values = array_merge(
                $values,
                require $configuration
            );
        }

        return new self($values);
    }
}
