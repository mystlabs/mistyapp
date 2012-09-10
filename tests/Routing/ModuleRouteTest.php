<?php

use MistyApp\Routing\ModuleRoute;

class ModuleRouteTest extends MistyTesting\UnitTest
{
    public function testEncode()
    {
        $route = new ModuleRoute();
        $this->assertEquals('/news/view/id/1', $route->encode(array(
            'module' => 'News',
            'action' => 'view',
            'id' => 1
        )));

        $this->assertEquals('/news/view', $route->encode(array(
            'module' => 'News',
            'action' => 'view',
        )));
    }

    public function testDecode()
    {
        $route = new ModuleRoute();
        $this->assertNull($route->decode('/'));
        $this->assertNull($route->decode('/admin'));

        $this->checkDecode('/news/archive', 'News\Controller\NewsController', 'archive', array(
            'module' => 'News',
            'action' => 'archive',
        ));

        $this->checkDecode('/news/modify/id/1', 'News\Controller\NewsController', 'modify', array(
            'module' => 'News',
            'action' => 'modify',
            'id' => 1,
        ));
    }

    private function checkDecode($path, $controller, $action, $params)
    {
        $route = new ModuleRoute();
        $cap = $route->decode($path);

        $this->assertNotNull($cap);
        $this->assertEquals($controller, $cap->controller);
        $this->assertEquals($action, $cap->action);
        $this->assertEquals($params, $cap->params);
    }
}
