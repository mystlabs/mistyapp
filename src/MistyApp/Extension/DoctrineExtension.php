<?php

namespace MistyApp\Extension;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineExtension implements ExtensionInterface
{
    private $models;

    public function __construct($models)
    {
        $this->models = $models;
    }

    /**
     * @see ExtensionInterface
     */
    public function register($provider, $configuration)
    {
        $provider->register('entity.manager', function($provider) use ($configuration){

            // See: http://docs.doctrine-project.org/en/latest/reference/configuration.html#obtaining-an-entitymanager
            $config = Setup::createAnnotationMetadataConfiguration(
                $this->models,
                $configuration->get('system.development.mode')
            );

            // Creating an entity manager
            return EntityManager::create(
                $configuration->get('database.params'),
                $config
            );

        });
    }
}
