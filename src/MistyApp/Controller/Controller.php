<?php

namespace MistyApp\Controller;

use MistyApp\View\Viewable;

use MistyDepMan\Container;
use MistyDepMan\Provider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    use Viewable, Container {
        setupContainer as _setupContainer;
    }

    protected $request;
    protected $router;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setupContainer(Provider $provider)
    {
        $this->_setupContainer($provider);

        $this->router = $this->provider->lookup('router');
    }

    /**
     * Execute the action on this controller, and wrap the return in a Response if necessary
     *
     * @param string $actionName The name of the action to execute on this controller
     */
    public function handle($actionName)
    {
        $response = $this->$actionName();

        // If the controller didn't return a response, we automatically wrap the content in one
        if (!$response instanceof Response) {
            $response = new Response($response);
        }

        return $response;
    }
}
