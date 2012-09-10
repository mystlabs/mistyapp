<?php

use MistyApp\Routing\ModuleAdminRoute;

class ModuleAdminRouteTest extends MistyTesting\UnitTest
{
    public function testEncode()
    {
        $route = new ModuleAdminRoute();
        $this->assertEquals('/admin/news/view/id/1', $route->encode(array(
            'module' => 'News',
            'action' => 'view',
            'id' => 1
        )));

        $this->assertEquals('/admin/news/view', $route->encode(array(
            'module' => 'News',
            'action' => 'view',
        )));
    }

    public function testDecode()
    {
        $route = new ModuleAdminRoute();
        $this->assertNull($route->decode('/'));
        $this->assertNull($route->decode('/admin'));
        $this->assertNull($route->decode('/news/view'));

        $this->checkDecode('/admin/news/archive', 'News\Controller\NewsAdminController', 'archive', array(
            'module' => 'News',
            'action' => 'archive',
        ));

        $this->checkDecode('/admin/news/modify/id/1', 'News\Controller\NewsAdminController', 'modify', array(
            'module' => 'News',
            'action' => 'modify',
            'id' => 1,
        ));
    }

    private function checkDecode($path, $controller, $action, $params)
    {
        $route = new ModuleAdminRoute();
        $cap = $route->decode($path);

        $this->assertNotNull($cap);
        $this->assertEquals($controller, $cap->controller);
        $this->assertEquals($action, $cap->action);
        $this->assertEquals($params, $cap->params);
    }
}
