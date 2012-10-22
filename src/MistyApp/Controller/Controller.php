<?php

namespace MistyApp\Controller;

use MistyApp\Component\Initializer;
use MistyApp\View\Viewable;
use MistyApp\User\SessionManager;
use MistyDepMan\Container;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    use Viewable, Container, Initializer, Redirecter;

    /** @var Request */
    protected $request;

    /** @var \MistyRouting\Router */
    protected $router;

    /** @var \MistyRouting\Urlifier */
    protected $urlifier;

    /** @var \MistyApp\Component\Configuration */
    protected $configuration;

    /** @var SessionManager */
    protected $sessionManager;

    /**
     * @param Request $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    protected function initialize()
    {
        $this->router = $this->provider->lookup('router');
        $this->urlifier = $this->provider->lookup('urlifier');
        $this->configuration = $this->provider->lookup('configuration');
        $this->sessionManager = $this->provider->lookup('session.manager');
    }

    /**
     * Execute the action on this controller, and wrap the return in a Response if necessary
     *
     * @param string $actionName The name of the action to execute on this controller
     */
    public function handle($actionName)
    {
        try {
            $response = $this->$actionName();
        } catch (RedirectTo $redirect) {
            return $redirect->getResponse();
        }

        // If the controller didn't return a response, we automatically wrap the content in one
        if (!$response instanceof Response) {
            $response = new Response($response);
            $response->headers->set('Content-type', 'text/html');
        }

        return $response;
    }

    /**
     * @see Viewable
     */
    protected function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @see Initializer
     */
    protected function getProvider()
    {
        return $this->provider;
    }
}
