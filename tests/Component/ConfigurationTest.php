<?php

use MistyApp\Component\Configuration;

class ConfigurationTest extends MistyTesting\UnitTest
{
	/**
	 * @expectedException MistyApp\Exception\ConfigurationException
	 */
	public function testGet_missingValue()
	{
		$bag = new Configuration();
		$bag->get('missing.configuration');
	}

	public function testGet_useDefault()
	{
		$bag = new Configuration();
		$this->assertEquals('default', $bag->get('missing.configuration', 'default'));
	}
}
