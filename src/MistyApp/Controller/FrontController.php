<?php

namespace MistyApp\Controller;

use MistyDepMan\Container;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontController
{
	use Container;

    protected $router;

    public function initialize()
    {
        $this->router = $this->provider->lookup('router');
    }

    /**
     * Use the Router to decode the path, and execute the corresponding controller/action
     *
     * @param string $path The path to be decoded and executed
     * @return Symfony\Component\HttpFoundation\Response
     * @throws \Exception If an exception is thrown and there isn't an exception.manager to handle it
     */
	public function handle($path)
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
                    $filter->apply($request);
                }
            }

            // Execute the appropriate controller/action
            $controller = $this->provider->create($controllerActionParams->controller, $request);
            $response = $controller->handle($controllerActionParams->action);

            // Apply optional filters on the Response
            if ($this->provider->has('response.filters')) {
                foreach ($this->provider->lookup('response.filters') as $filter) {
                    $filter->apply($response);
                }
            }

            return $response ;

        } catch (\Exception $e) {

            // Check if we have an exception controller
            if ($this->provider->has('exception.controller')) {
                $exceptionController = $this->provider->lookup('exception.controller');
                $response = $exceptionController->handle($e);

                if ($response instanceof Response) {
                    return $response;
                }
            }

            throw $e;
        }
	}
}
