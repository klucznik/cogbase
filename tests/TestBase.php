<?php

use Cog\Exceptions\UndefinedPropertyException;

class TestBase extends \PHPUnit\Framework\TestCase {

	public $testObject;

	public function setUp() {
		$this->testObject = new BaseObject();
	}

	public function tearDown() {
		$this->testObject = null;
	}

	public function testMissingPropertyOverride() {
		$this->expectException(UndefinedPropertyException::class);
		$this->testObject->overrideAttributes([['missingProperty' => 'Setting value']]);
	}

	public function testArrayOverride() {
		$this->assertNull($this->testObject->overrideProperty);
		$this->testObject->overrideAttributes([['overrideProperty' => 'Setting value']]);
		$this->assertEquals($this->testObject->overrideProperty, 'Setting value');

		$this->testObject->overrideAttributes([['overrideProperty' => null]]);
		$this->assertNull($this->testObject->overrideProperty);

		$this->testObject->overrideAttributes([['overrideProperty' => '']]);
		$this->assertEmpty($this->testObject->overrideProperty);
	}

	public function testStringOverride() {
		$this->assertNull($this->testObject->overrideProperty);
		$this->testObject->overrideAttributes(['overrideProperty=Unquoted value']);
		$this->assertEquals($this->testObject->overrideProperty, 'Unquoted value');

		$this->testObject->overrideAttributes(['overrideProperty=""']);
		$this->assertEmpty($this->testObject->overrideProperty);

		$this->testObject->overrideAttributes(['overrideProperty="Double quoted value"']);
		$this->assertEquals($this->testObject->overrideProperty, 'Double quoted value');

		$this->testObject->overrideAttributes(["overrideProperty=''"]);
		$this->assertEmpty($this->testObject->overrideProperty);

		$this->testObject->overrideAttributes(["overrideProperty='Single quoted value'"]);
		$this->assertEquals($this->testObject->overrideProperty, 'Single quoted value');
	}

	public function testStringOverrideValidity() {
		$this->expectException(\Cog\Exception::class);
		$this->testObject->overrideAttributes(['overrideProperty="value']);
	}

	public function testStringOverrideValidity2() {
		$this->expectException(\Cog\Exception::class);
		$this->testObject->overrideAttributes(["overrideProperty='value"]);
	}

	public function testStringOverrideValidity3() {
		$this->expectException(\Cog\Exception::class);
		$this->testObject->overrideAttributes(["overridePropertyvalue"]);
	}

	public function testMagicProperty() {
		$this->assertNull($this->testObject->MagicProperty);

		$this->assertTrue(isset($this->testObject->MagicProperty));
		$this->assertFalse(isset($this->testObject->MissingProperty));

		$this->testObject->MagicProperty = 'Value';
		$this->assertEquals($this->testObject->MagicProperty, 'Value');
	}

	public function testUndefinedProperty() {
		$this->expectException(UndefinedPropertyException::class);
		$this->testObject->MissingProperty;
	}

	public function testUndefinedPropertySet() {
		$this->expectException(UndefinedPropertyException::class);
		$this->testObject->MissingProperty = 'Something';
	}
}
