<?php

use MistyApp\Component\ParameterBag;

class ParameterBagTest extends MistyTesting\UnitTest
{
	public function testSet()
	{
		$bag = new ParameterBag();
		$bag->set('a', 'ValueA');
		$bag->set('b', 'ValueB');

		$this->assertEquals(array(
			'a' => 'ValueA',
			'b' => 'ValueB',
		), $bag->all());
	}

	public function testSetAll()
	{
		$bag = new ParameterBag(array(
			'a' => 'ValueA',
			'b' => 'ValueB',
		));
		$bag->setAll(array(
			'b' => 'ValueB2',
			'c' => 'ValueC',
		));

		$this->assertEquals(array(
			'a' => 'ValueA',
			'b' => 'ValueB2',
			'c' => 'ValueC',
		), $bag->all());
	}

	public function testHas()
	{
		$bag = new ParameterBag(array(
			'a' => 'ValueA',
			'b' => null,
		));

		$this->assertTrue($bag->has('a'));
		$this->assertTrue($bag->has('b'));
		$this->assertFalse($bag->has('c'));
	}

	public function testGet()
	{
		$bag = new ParameterBag(array(
			'a' => 'ValueA',
			'b' => null,
		));

		$this->assertEquals('ValueA', $bag->get('a'));
		$this->assertNull($bag->get('b', 'NotNull'));
		$this->assertEquals('defaultValue', $bag->get('c', 'defaultValue'));
	}

	public function testRemove()
	{
		$bag = new ParameterBag();

		$bag->set('A', 10);
		$this->assertTrue($bag->has('A'));

		$bag->remove('A');
		$this->assertFalse($bag->has('A'));
	}
}
