<?php

namespace MistyApp\Test;

use MistyApp\Component\Configuration;
use MistyApp\Component\Initializer;
use MistyApp\Extension;
use MistyApp\Kernel;
use MistyDepMan\MockableProvider;
use MistyRouting\Router;
use MistyRouting\Urlifier;

class IntegTest extends \MistyDoctrine\Test\DoctrineTest
{
    use Initializer;

    /** @var Kernel */
    private $kernel;

    /** @var MockableProvider */
    protected $provider;

    /** @var Configuration */
    protected $configuration;

    /** @var Router */
    protected $router;

    /** @var Urlifier */
    protected $urlifier;

    public function before()
    {
        $root = $this->findAppFolder();

        $this->provider = new MockableProvider;
        $this->configuration = Configuration::fromFiles(array(
            $root . '/config/config.php',
            $root . '/config/config-dev.php',
            $root . '/config/config-test.php',
        ));

        // Create a kernel, and wrap it with an error handler
        $this->kernel = new Kernel(
            $this->configuration,
            $this->provider
        );

        // Setting up additional components
        $this->kernel->before(array(
            new Extension\AllErrorsExtension,
            new Extension\DoctrineExtension(require $root.'/config/models.php'),
            new Extension\RoutingExtension(require $root . '/config/site-routes.php'),
            new Extension\SessionManagerExtension,
            new Extension\ThemeExtension,
            new Extension\FiltersExtension('response.filters', array(
                'themefilter' => $this->provider->proxy('MistyApp\Filter\ThemeFilter'),
                //'htmlvalidator' => new MistyApp\Filter\HtmlValidator,
            )),
        ));

        // Setting up components to execute after the response has been sent
        $this->kernel->after(array(
            new Extension\DoctrineValidatorExtension,
        ));

        $this->kernel->executeBefore();
    }

    public function after()
    {
        //$this->kernel->executeAfter();
    }

    private function findAppFolder()
    {
        $folder = $_SERVER['PWD'];
        do {

            if (is_dir($folder . '/app')) {
                return $folder . '/app';
            }

            $folder = dirname($folder);

        } while($folder !== '/');

        throw new \Exception('Could not find the app folder');
    }

    function getProvider()
    {
        return $this->provider;
    }
}
