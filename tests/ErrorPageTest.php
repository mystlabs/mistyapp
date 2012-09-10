<?php

use MistyApp\ErrorPage;
use MistyApp\Kernel;

class ErrorPageTest extends MistyTesting\UnitTest
{
    public function testRenderException()
    {
        ob_start();

        $mockKernel = Mockery::mock('MistyApp\Kernel');
        $mockKernel->shouldReceive('processRequest')
            ->andThrow('Exception', 'Error message');

        $errorPage = new ErrorPage($mockKernel);
        $errorPage->processRequest();

        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(1, preg_match('/Error message/', $content));
    }
}
