<?php

namespace MistyApp;

use MistyDepMan\Provider;

class Kernel
{
    protected $provider;

    public function __construct(Provider $provider = null)
    {
        $this->provider = $provider ? $provider : new Provider;
    }

    /**
     * Initialize the kernel by loading all the passed files
     *
     * @param array $initFiles Array of files to load to initialize the kernel
     * @param array $debugFiles Array of files to load only if the system is in debug mode
     */
    public function initialize($initFiles = array(), $debugFiles = array())
    {
        foreach ($initFiles as $initFile) {
            require $initFile;
        }

        $configuration = $this->provider->lookup('configuration');
        if ($configuration->get('system.development.mode')) {
            foreach ($debugFiles as $debugFile) {
                require $debugFile;
            }
        }
    }

    /**
     * Parse the request and generate the page
     */
    public function processRequest()
    {
        $path = isset($_GET['__q']) ? $_GET['__q'] : '/';

        $frontController = $this->provider->create('MistyApp\Controller\FrontController');
        $response = $frontController->handle($path);
        $response->send();
    }
}
