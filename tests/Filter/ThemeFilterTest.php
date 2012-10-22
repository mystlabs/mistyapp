<?php

use MistyTesting\UnitTest;
use MistyApp\Component\AnonymousObject;
use MistyDepMan\Provider;
use MistyApp\Component\Configuration;
use MistyApp\Filter\ThemeFilter;

use Symfony\Component\HttpFoundation\Response;

class ThemeFilterTest extends UnitTest
{
    /** @var ThemeFilter */
	private $theme;

    /** @var Response */
	private $response;

    /** @var Provider */
	private $provider;

	public function before()
	{
		$this->provider = new Provider;
		$this->provider->register('theme', new AnonymousObject(array(
            'apply' => function($content){
                return '<themed>' . $content . '</themed>';
            }
        )));

		$this->theme = new ThemeFilter();
		$this->theme->setupContainer($this->provider);

		$this->response = new Response('some content');
	}

	public function testHtmlContent()
	{
		$this->theme->apply($this->response);

		$this->assertPattern('/<themed>some content<\/themed>/', $this->response->getContent());
	}

	public function testNonHtmlContent()
	{
		$this->response->headers->set('Content-type', 'text/json');

		$this->theme->apply($this->response);

		$this->assertPattern('/some content/', $this->response->getContent());
	}
}
