<?php

use Cog\Exceptions\InvalidCastException;
use Cog\Type;
use Stringy\Stringy;

class TestType extends \PHPUnit\Framework\TestCase {

	public function testShortcuts() {
		$this->assertEquals(Type::castArray(['gffdsg']), ['gffdsg']);

		$this->assertEquals(Type::castArrayStrict(null), []);
		$this->assertFalse(Type::castBoolStrict(null, false));

		$this->assertNull(Type::castArray(null));
		$this->assertNull(Type::castBool(null));
	}

	public function testNullCast() {
		$this->assertFalse(Type::cast(null, Type::BOOLEAN, false));
		$this->assertEmpty(Type::cast(null, Type::STRING, false));
		$this->assertEquals(Type::cast(null, Type::INTEGER, false), 0);
		$this->assertEquals(Type::cast(null, Type::FLOAT, false), 0.0);
		$this->assertEquals(Type::cast(null, Type::ARRAY, false), []);
		$this->assertNull(Type::cast(null, Type::OBJECT, false));
	}

	public function testNullCastWithPreservation() {
		$this->assertNull(Type::cast(null, Type::BOOLEAN));
		$this->assertNull(Type::cast(null, Type::STRING));
		$this->assertNull(Type::cast(null, Type::INTEGER));
		$this->assertNull(Type::cast(null, Type::FLOAT));
		$this->assertNull(Type::cast(null, Type::ARRAY));
		$this->assertNull(Type::cast(null, Type::OBJECT));
	}

	public function testBooleanCast() {
		$this->assertTrue(Type::cast('string', Type::BOOLEAN));
		$this->assertTrue(Type::cast(1, Type::BOOLEAN));
		$this->assertFalse(Type::cast(0, Type::BOOLEAN));
		$this->assertFalse(Type::cast(null, Type::BOOLEAN, false));
	}

	public function testStringCast() {
		$this->assertEquals(Type::cast('string', Type::STRING), 'string');
		$this->assertEquals(Type::cast('string', Type::STRING), "string");
		$this->assertEquals(Type::cast(1, Type::STRING), '1');
		$this->assertEquals(Type::cast(0, Type::STRING), '0');
		$this->assertEquals(Type::cast(null, Type::STRING, false), '');
	}

	public function testConstantCodeGenerator() {
		$this->assertEquals(Type::constant('string'), 'Type::STRING');
		$this->assertEquals(Type::constant('object'), 'Type::OBJECT');
		$this->assertEquals(Type::constant('integer'), 'Type::INTEGER');
		$this->assertEquals(Type::constant('double'), 'Type::FLOAT');
		$this->assertEquals(Type::constant('boolean'), 'Type::BOOLEAN');
		$this->assertEquals(Type::constant('array'), 'Type::ARRAY');
		$this->assertEquals(Type::constant('Carbon'), 'Type::DATETIME');
	}

	public function testConstantCodeGeneratorException() {
		$this->expectException(InvalidCastException::class);
		Type::constant('missing');
	}

	public function testArrayCast() {
		$array = ['array', 'with', 'stuff'];
		$this->assertEquals(Type::cast($array, Type::ARRAY), $array);

		$this->expectException(InvalidCastException::class);
		Type::cast($array, Type::BOOLEAN);
	}

	public function testObjectCast() {
		$obj = new stdClass();

		$this->expectException(InvalidCastException::class);
		Type::cast($obj, Type::ARRAY);
	}

	public function testObjectCast2() {
		$obj = new stdClass();
		$this->assertEquals(Type::cast($obj, 'stdClass'), $obj);
	}

	public function testObjectCast3() {
		$this->assertEquals(Type::cast(new Stringy('stringy'), Type::STRING), 'stringy');

		$date = new Carbon\Carbon('2001-10-10');
		$this->assertEquals(Type::cast($date, Type::DATETIME), $date);
	}

	public function testInvalidCast() {
		$this->expectException(InvalidCastException::class);
		Type::cast('sgdgd', Type::ARRAY);
	}

	public function testCastValueTo() {
		$this->assertFalse(Type::cast(false, Type::BOOLEAN));
		$this->assertFalse(Type::cast('', Type::BOOLEAN));
		$this->assertFalse(Type::cast('false', Type::BOOLEAN));
		$this->assertTrue(Type::cast('true', Type::BOOLEAN));

		$this->assertEquals(Type::cast('string', Type::STRING), 'string');

		$this->assertEquals(Type::cast('1.324', Type::FLOAT), '1.324');
		$this->assertNull(Type::cast('', Type::FLOAT));

		$this->expectException(InvalidCastException::class);
		$this->assertEquals(Type::cast('1.32443767654765655475674756747423223432', Type::FLOAT), '1.32443767654765655475674756747423223432');
	}

	public function testSimpleXml() {
		$xml = new SimpleXMLElement('<foo>bar</foo>');

		$this->assertEquals(Type::cast($xml, Type::STRING), 'bar');
		$this->assertTrue(Type::cast($xml, Type::BOOLEAN));
		$this->assertTrue(Type::cast(new SimpleXMLElement('<foo>true</foo>'), Type::BOOLEAN));
		$this->assertFalse(Type::cast(new SimpleXMLElement('<foo>false</foo>'), Type::BOOLEAN));

		$this->assertEquals(Type::cast(new SimpleXMLElement('<foo>2</foo>'), Type::INTEGER), 2);

		$this->expectException(InvalidCastException::class);
		$this->assertEquals(Type::cast(new SimpleXMLElement('<foo>string</foo>'), Type::INTEGER), 2);
	}
}
