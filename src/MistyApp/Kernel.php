<?php

namespace MistyApp;

use MistyApp\Component\Configuration;
use MistyApp\Component\ParameterBag;
use MistyApp\Controller\FrontController;
use MistyApp\Extension\ExtensionInterface;
use MistyDepMan\Provider;
use Symfony\Component\HttpFoundation\Response;

class Kernel
{
    protected $provider;

    protected $beforeExtensions = array();
    protected $afterExtensions = array();

    /**
     * @param Configuration $configuration The configuration object
     * @param Provider $provider The provider, if not given it will be created
     */
    public function __construct(Configuration $configuration, Provider $provider = null)
    {
        $this->provider = $provider ? $provider : new Provider;
        $this->provider->register('configuration', $configuration);
        $this->provider->register('request.filters', new ParameterBag);
        $this->provider->register('response.filters', new ParameterBag);

        global $provider;
        $provider = $this->provider;
    }

    /**
     * Initialize the kernel by executing all the passed extensions
     *
     * @param ExtensionInterface[] $extensions Array of extensions to register to initialize the kernel
     */
    public function before($extensions)
    {
        $this->beforeExtensions = $extensions;
    }

    /**
     * Initialize the kernel by executing all the passed extensions
     *
     * @param ExtensionInterface[] $extensions Array of extensions to run after the request has been executed and the response sent
     */
    public function after($extensions)
    {
        $this->afterExtensions = $extensions;
    }

    /**
     * Parse the request and generate the page
     */
    public function run()
    {
        // Start by executing the 'before' extensions
        $this->executeBefore();

        // get the path from the query string
        $path = isset($_GET['__q']) ? $_GET['__q'] : '/';

        // then retrieve the front controller and handle the request
        /** @var $frontController FrontController */
        $frontController = $this->provider->create('MistyApp\Controller\FrontController');

        /** @var $response Response */
        $response = $frontController->handle($path);
        $response->send();

        // and finally execute the 'after' extensions
        $this->executeAfter();
    }

    public function executeBefore()
    {
        $this->executeExtensions($this->beforeExtensions);
    }

    public function executeAfter()
    {
        $this->executeExtensions($this->afterExtensions);
    }

    /**
     * Execute the given extensions
     *
     * @param ExtensionInterface[] $extensions Array of extensions to run
     */
    private function executeExtensions($extensions)
    {
        // Get the configuration from the provider
        $configuration = $this->provider->lookup('configuration');

        // Execute the extensions
        foreach ($extensions as $extension) {
            $extension->register($this->provider, $configuration); // fixme 'register' sucks as name
        }
    }
}
