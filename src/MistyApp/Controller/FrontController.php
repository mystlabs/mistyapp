<?php

namespace MistyApp\Controller;

use MistyApp\Filter\RequestFilter;
use MistyApp\Filter\ResponseFilter;
use MistyDepMan\Container;
use MistyRouting\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontController
{
	use Container;

    /** @var Router */
    protected $router;

    public function initialize()
    {
        $this->router = $this->provider->lookup('router');
    }

    /**
     * Use the Router to decode the path, and execute the corresponding controller/action
     *
     * @param string $path The path to be decoded and executed
     * @return Response
     * @throws \Exception If an exception is thrown and there isn't an exception.manager to handle it
     */
	public function handle($path)
	{
        $response = $this->handleRequest($path);

        // Apply optional filters on the Response
        if ($this->provider->has('response.filters')) {
            foreach ($this->provider->lookup('response.filters') as $filter) {

                // The Response filter can replace the response object
                /** @var $filter ResponseFilter */
                $newResponse = $filter->apply($response);
                if ($newResponse instanceof Response) {
                    $response = $newResponse;
                }
            }
        }

        return $response;
	}

    /**
     * Apply the request filters, call the front controller and returns a Response
     *
     * @param string $path
     * @return Response
     * @throws \Exception
     */
    private function handleRequest($path)
    {
        try {
            // Create a request object
            $request = Request::createFromGlobals();

            // Decode the path and add the params to the request
            $controllerActionParams = $this->router->decode($path);
            $request->query->add($controllerActionParams->params);

            // Apply optional filters on the Request
            if ($this->provider->has('request.filters')) {
                foreach ($this->provider->lookup('request.filters') as $filter) {

                    // The request filter can return a Response. If it does, the controller won't be called
                    /** @var $filter RequestFilter */
                    $response = $filter->apply($request);
                    if ($response instanceof Response) {
                        return $response;
                    }
                }
            }

            // Execute the appropriate controller/action
            $controller = $this->provider->create($controllerActionParams->controller, $request);
            return $controller->handle($controllerActionParams->action);

        } catch (\Exception $e) {

            // Check if we have an exception controller
            if (!$this->provider->has('exception.controller')) {
                throw $e;
            }

            $exceptionController = $this->provider->lookup('exception.controller');
            return $exceptionController->handle($e);
        }
    }

}
