<?php

use MistyApp\Controller\FrontController;
use MistyDepMan\Provider;
use MistyRouting\ControllerActionParams;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontControllerTest extends MistyTesting\UnitTest
{
    /** @var Provider */
    private $provider;

    /** @var Mockery\MockInterface */
    private $mockRouter;

    public function before()
    {
        $this->mockRouter = Mockery::mock('Provider');

        $this->provider = new Provider;
        $this->provider->register('router', $this->mockRouter);
    }

    public function testHandle()
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

    public function testHandle_requestFilters()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'checkRequestFilter'));

        $this->provider->register('request.filters', array(
            new FrontController_RequestFilter(function($request){
                $request->query->add(array(
                    'varFromFilter' => 'value'
                ));
            }),
        ));

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $controller->handle('/');
    }

    public function testHandle_requestFilterWithResponse()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'throwException'));

        $this->provider->register('request.filters', array(
            new FrontController_RequestFilter(function(){
                return new Response("request-filtered");
            }),
        ));

        $this->provider->register('response.filters', array(
            new FrontController_ResponseFilter(function($response){
                $response->setContent(sprintf(
                    "<%s>",
                    $response->getContent()
                ));
            }),
        ));

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $response = $controller->handle('/');

        $this->assertEquals("<request-filtered>", $response->getContent());
    }

    public function testHandle_responseFilters()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'doNothing'));

        $this->provider->register('response.filters', array(
            new FrontController_ResponseFilter(function($response){
                $response->setContent('altered-content');
            }),
        ));

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $response = $controller->handle('/');

        $this->assertEquals('altered-content', $response->getContent());
    }

    public function testHandle_handledException()
    {
        $this->mockRouter
            ->shouldReceive('decode')
            ->andReturn(new ControllerActionParams('FrontController_Controller', 'throwException'));

        $mockExceptionHandler = Mockery::mock();
        $mockExceptionHandler
            ->shouldReceive('handle')
            ->andReturn(new Response());

        $this->provider->register('exception.controller', $mockExceptionHandler);

        $controller = new FrontController();
        $controller->setupContainer($this->provider);
        $controller->handle('/');
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
        $controller->handle('/');
    }
}

class FrontController_Controller
{
    /** @var Request */
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
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }
    /**
     * @param $request Request
     */
    public function apply($request)
    {
        $func = $this->callback;
        return $func($request);
    }
}

class FrontController_ResponseFilter
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }
    /**
     * @param $response Response
     */
    public function apply($response)
    {
        $func = $this->callback;
        return $func($response);
    }
}

class FrontControllerException extends Exception
{

}
