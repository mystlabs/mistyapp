<?php

namespace MistyApp\Extension;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;

use MistyApp\User\SessionManager;
use MistyApp\Exception\ConfigurationException;

class SessionManagerExtension implements ExtensionInterface
{
    /**
     * @see ExtensionInterface
     */
    public function register($provider, $configuration)
    {
        // Selecting the configured session storage. Supported options are 'native' and 'memcached'
        $sessionStorageType = $configuration->get('session.storage.type', 'native');
        switch ($sessionStorageType) {
            case 'native':
                $handler = new NativeFileSessionHandler();
                break;

            case 'memcached':
                die('Not supported');
                //$handler = new MemcachedSessionHandler();
                break;

            default:
                throw new ConfigurationException(sprintf(
                    'Unknown session storage type: %s',
                    $sessionStorageType
                ));
        }

        // Creating the session storage
        $sessionStorageOptions = $configuration->get('session.storage.options', array());
        $sessionStorage = new NativeSessionStorage($sessionStorageOptions, $handler);

        // Creating the user storage
        $userStorageClass = $configuration->get('user.storage.class');
        $userStorage = $provider->proxy($userStorageClass, $provider->lookup('entity.manager'));

        $provider->register('session.manager', new SessionManager(
            $sessionStorage,
            $userStorage
        ));
    }
}
