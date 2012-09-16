<?php

use MistyTesting\UnitTest;
use MistyDepMan\Provider;
use MistyApp\Component\Configuration;
use MistyApp\View\Theme;

use Symfony\Component\HttpFoundation\Response;

class ThemeTest extends UnitTest
{
	private $theme;
	private $response;
	private $provider;

	public function before()
	{
		$this->provider = new Provider;
		$this->provider->register('configuration', new Configuration(array(
            'system.temp.folder' => __DIR__ . '/temp',
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

		$this->response = new Response(
			'some content',
			200,
			array(
				'Content-type' => 'text/html'
			)
		);
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
		$this->theme
			->setLayout('complete')
			->apply($this->response);

		$this->assertPattern('/<complete>some content<\/complete>/', $this->response->getContent());
	}

	public function testApplyDefault()
	{
		$this->theme->apply($this->response);

		$this->assertPattern('/<simple>some content<\/simple>/', $this->response->getContent());
	}

	public function testSetNoLayout()
	{
		$this->theme
			->setNoLayout()
			->apply($this->response);

		$this->assertPattern('/some content/', $this->response->getContent());
	}

	public function testNonHtmlContent()
	{
		$this->response->headers->set('Content-type', 'text/json');

		$this->theme->apply($this->response);

		$this->assertPattern('/some content/', $this->response->getContent());
	}
}
