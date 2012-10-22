<?php

namespace MistyApp\Extension;

use MistyApp\Exception\ConfigurationException;
use Doctrine\ORM\Tools\SchemaValidator;

/**
 * Validate the database schema, but only if Docrine has been used
 */
class DoctrineValidatorExtension implements ExtensionInterface
{
    /**
     * @see ExtensionInterface
     */
    public function register($provider, $configuration)
    {
        if ($provider->isInitialized('entity.manager')) {
            // Skip the validation if the app didn't connect to the database
            return;
        }

        $validator = new SchemaValidator($provider->lookup('entity.manager'));
        $errors = $validator->validateMapping();

        if (!empty($errors)) {
            throw new ConfigurationException(sprintf(
                'The database schema is not valid: %s',
                print_r($errors, true)
            ));
        }

    }
}

