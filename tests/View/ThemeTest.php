<?php

use MistyTesting\UnitTest;
use MistyDepMan\Provider;
use MistyApp\Component\Configuration;
use MistyApp\View\Theme;

use Symfony\Component\HttpFoundation\Response;

class ThemeTest extends UnitTest
{
    /** @var Theme */
	private $theme;

    /** @var string */
	private $content;

    /** @var Provider */
	private $provider;

	public function before()
	{
		$this->provider = new Provider;
		$this->provider->register('configuration', new Configuration(array(
            'system.temp.folder' => __DIR__ . '/temp',
            'system.app.folder' => __DIR__ . '/temp',
		)));

		$this->theme = new Theme(
			array(
				'complete' => 'complete/completelayout.tpl',
				'simple' => 'simple/simplelayout.tpl'
			),
			'simple',
			__DIR__.'/themes/'
		);
		$this->theme->setupContainer($this->provider);

		$this->content = 'some content';
	}
	/**
	 * @expectedException MistyApp\Exception\ConfigurationException
	 */
	public function testSetLayout_notExisting()
	{
		$this->theme->setLayout('partial');
	}

	public function testSetLayout()
	{
		$result = $this->theme
			->setLayout('complete')
			->apply($this->content);

		$this->assertPattern('/<complete>some content<\/complete>/', $result);
	}

	public function testApplyDefault()
	{
		$result = $this->theme->apply($this->content);

		$this->assertPattern('/<simple>some content<\/simple>/', $result);
	}

	public function testSetNoLayout()
	{
		$result= $this->theme
			->setNoLayout()
			->apply($this->content);

		$this->assertPattern('/some content/', $result);
	}
}
