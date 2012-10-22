<?php

namespace FakeModuleName;

use MistyApp\Component\Configuration;
use MistyApp\View\Viewable;
use MistyTesting\UnitTest;

class ViewableTest extends UnitTest
{
    use Viewable;

    private $configuration;

    public function before()
    {
        $this->configuration = new Configuration(array(
            'system.app.folder' => __DIR__,
            'system.temp.folder' => __DIR__ . '/temp',
        ));
    }

    public function testRender()
    {
        $output = $this
            ->assign('number', 100)
            ->assign('what', 'dollar')
            ->render('test.tpl');

        $expected = '<p>There are 100 cents in one dollar</p>';
        $this->assertEquals($expected, trim($output));
    }

    public function testAssignAll()
    {
        $output = $this
            ->assign(array(
                'number' => 100,
                'what' => 'dollar',
            ))
            ->render('test.tpl');

        $expected = '<p>There are 100 cents in one dollar</p>';
        $this->assertEquals($expected, trim($output));
    }

    public function testInitializeWithDifferentFolder()
    {
        $output = $this
            ->initializeView(__DIR__ . '/frontend')
            ->assign(array(
                'number' => 100,
                'what' => 'dollar',
            ))
            ->render('test.tpl');

        $expected = '<p>100 dollars</p>';
        $this->assertEquals($expected, trim($output));
    }

    /**
     * @return Configuration
     */
    function getConfiguration()
    {
        return $this->configuration;
    }
}
