<?php

namespace MistyApp\Extension;

use MistyRouting\Router;
use MistyRouting\PathDecorator;
use MistyRouting\Urlifier;

class RoutingExtension implements ExtensionInterface
{
    private $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @see ExtensionInterface
     */
    public function register($provider, $configuration)
    {
        $router = new Router($this->routes);

        $decorator = new PathDecorator(
            $configuration->get('current.hostname'),
            $configuration->get('pathdecorator.options', array())
        );

        $urlifier = new Urlifier(
            $router,
            $decorator
        );

        $provider->register('router', $router);
        $provider->register('urlifier', $urlifier);
    }
}
