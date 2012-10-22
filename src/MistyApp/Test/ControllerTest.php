<?php

namespace MistyApp\Test;

use MistyTesting\DoctrineTest;

class ControllerTest extends IntegTest
{
    protected $frontController;

    public function before()
    {
        parent::before();

        $this->frontController = new \MistyApp\Controller\FrontController();
        $this->frontController->setupContainer($this->provider);
    }

    /**
     * Execute a GET on the given route
     *
     * @param string $routeName The name of the route
     * @param array $params The params for this route
     * @return Response
     */
    protected function doGet($routeName, $params)
    {
        $path = $this->router->encode($routeName, $params);
        return $this->frontController->handle($path);
    }

    /**
     * Execute a POST on the given route
     *
     * @param string $routeName The name of the route
     * @param array $params The params for this route
     * @param array $postParams The params to be posted
     * @return Response
     */
    protected function doPost($routeName, $params, $postParams)
    {
        $_POST = $postParams;
        $path = $this->router->encode($routeName, $params);
        return $this->frontController->handle($path);
    }

    /**
     * Assert that the response status is 200
     *
     * @param Response $response The response to check
     */
    protected function assert200Response($response)
    {
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Assert that the response is a redirect and check the url
     */
    protected function assertRedirectResponse($response, $routeName, $params, $options = array())
    {
        $this->assertTrue($response->isRedirect());
        $this->assertEquals($this->urlifier->url(
                $routeName,
                $params,
                array_merge($options, array('absolute' => 1))
            ),
            $response->getTargetUrl()
        );
    }
}
