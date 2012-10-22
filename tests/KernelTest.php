<?php

use MistyApp\Component\Configuration;
use MistyApp\Extension\ExtensionInterface;
use MistyApp\Kernel;
use MistyDepMan\Provider;
use Symfony\Component\HttpFoundation\Response;

class KernelTest extends MistyTesting\UnitTest
{
	private $provider;
	private $mockProvider;
	private $configuration;

    /** @var Kernel */
	private $kernel;

	public function before()
	{
		$this->provider = new Provider;
		$this->mockProvider = \Mockery::mock($this->provider);
		$this->configuration = new Configuration;

		$mockFrontController = Mockery::mock();
		$mockFrontController
			->shouldReceive('handle')
			->zeroOrMoreTimes()
			->andReturn(new Response('test content'));

		$this->mockProvider
			->shouldReceive('create')
			->zeroOrMoreTimes()
			->andReturn($mockFrontController);

        ob_start();
		$this->kernel = new Kernel($this->configuration, $this->mockProvider);
	}

    public function after()
    {
        ob_end_clean();
    }

	public function testRun()
	{
        $this->kernel->run();
        $content = ob_get_contents();

        $this->assertEquals('test content', $content);
	}

	public function testBefore()
	{
		$this->kernel->before(array(
			new KernelTestExtension
		));

		try {
			$this->kernel->run();
			$this->fail();
		} catch (InvalidArgumentException $e) {
		}
	}

	public function testAfter()
	{
		$this->kernel->after(array(
			new KernelTestExtension
		));

		try {
			$this->kernel->run();
			$this->fail();
		} catch (InvalidArgumentException $e) {
		}
	}
}

class KernelTestExtension implements ExtensionInterface
{
	public function register($provider, $configuration)
	{
		throw new InvalidArgumentException();
	}
}
