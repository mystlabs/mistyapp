<?php

use MistyApp\Controller\FrontController;
use MistyDepMan\Provider;
use MistyRouting\ControllerActionParams;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontControllerTest extends MistyTesting\UnitTest
{
    private $provider;
    private $mockRouter;

    public function before()
    {
        $this->mockRouter = Mockery::mock('Provider');

        $this->provider = new Provider;
        $this->provider->register('router', $this->mockRouter);
    }

    public function testHandleRequest()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'checkRequest', array(
                'varFromPath' => 'value'
            )));


        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $controller->handle('/');
    }

    public function testHandleRequest_requestFilters()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'checkRequestFilter'));

        $this->provider->register('request.filters', array(
            new FrontController_RequestFilter(),
        ));

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $controller->handle('/');
    }

    public function testHandleRequest_responseFilters()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'doNothing'));

        $this->provider->register('response.filters', array(
            new FrontController_ResponseFilter(),
        ));

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $response = $controller->handle('/');

        $this->assertEquals('altered-content', $response->getContent());
    }

    public function testHandleRequest_handledException()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'throwException'));

        $mockExceptionHandler = Mockery::mock();
        $mockExceptionHandler
            ->shouldReceive('handle')
            ->andReturn(new Response());

        $this->provider->register('exception.handler', $mockExceptionHandler);

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $response = $controller->handle('/');
    }

    /**
     * @expectedException FrontControllerException
     */
    public function testHandleRequest_unhandledException()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'throwException'));

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $response = $controller->handle('/');
    }
}

class FrontController_Controller
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function handle($action)
    {
        switch ($action) {
            case 'checkRequest':
                if ($this->request->query->get('varFromPath') !== 'value') {
                    throw new \FrontControllerException();
                }
                break;

            case 'checkRequestFilter':
                if ($this->request->query->get('varFromFilter') !== 'value') {
                    throw new \FrontControllerException();
                }
                break;

            case 'checkResponseFilter':
                if ($this->request->query->get('varFromFilter') !== 'value') {
                    throw new \FrontControllerException();
                }
                break;

            case 'throwException':
                throw new \FrontControllerException();
        }

        return new Response();
    }
}

class FrontController_RequestFilter
{
    public function apply($request)
    {
        $request->query->add(array(
            'varFromFilter' => 'value'
        ));
    }
}

class FrontController_ResponseFilter
{
    public function apply($response)
    {
        $response->setContent('altered-content');
    }
}

class FrontControllerException extends Exception
{

}
