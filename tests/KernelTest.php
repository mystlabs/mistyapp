<?php

use MistyApp\Kernel;

class KernelTest extends MistyTesting\UnitTest
{
	public function testInitialize()
	{

	}

	public function testDevelopmentMode()
	{

	}

	public function testProcessRequest()
	{
		$_GET['__q'] = '/request/path';

		$mockProvider = Mockery::mock('MistyDepMap\Provider');
		$kernel = new Kernel($mockProvider);
	}
}
