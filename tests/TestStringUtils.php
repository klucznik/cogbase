<?php

use Cog\StringUtils;
use PHPUnit\Framework\TestCase;

class TestStringUtils extends TestCase {

	protected $string = 'here is the colorful kahless?';
	protected $multiByteString = 'żółć gęślą jaźń';
	protected $xmlString = 'Escape this & this.';


	public function testFirstCharacter() {
		$this->assertEquals(StringUtils::firstCharacter($this->string), 'h');
		$this->assertEquals(StringUtils::firstCharacter($this->multiByteString), 'ż');

		$this->assertEquals(StringUtils::firstCharacter(3), '3');

		$this->assertNull(StringUtils::firstCharacter(''));

		$this->assertEquals(StringUtils::firstCharacter(true), '1');
		$this->assertNull(StringUtils::firstCharacter(null));
		$this->assertNull(StringUtils::firstCharacter(false));
	}

	public function testLastCharacter() {
		$this->assertEquals(StringUtils::lastCharacter($this->string), '?');
		$this->assertEquals(StringUtils::lastCharacter($this->multiByteString), 'ń');

		$this->assertEquals(StringUtils::lastCharacter(3), '3');

		$this->assertNull(StringUtils::lastCharacter(''));

		$this->assertEquals(StringUtils::lastCharacter(true), '1');
		$this->assertNull(StringUtils::lastCharacter(null));
		$this->assertNull(StringUtils::lastCharacter(false));
	}

	public function testByte() {
		$this->assertEquals(StringUtils::getByteSize(null), 'N/A');
		$this->assertEquals(StringUtils::getByteSize(0), '0 bytes');
		$this->assertEquals(StringUtils::getByteSize(1), '1 byte');
		$this->assertEquals(StringUtils::getByteSize(512), '512 bytes');
		$this->assertEquals(StringUtils::getByteSize(-512), '-512 bytes');
		$this->assertEquals(StringUtils::getByteSize(1680), '1.6 KB');
		$this->assertEquals(StringUtils::getByteSize(512 * 1024 * 1024), '512.0 MB');
		$this->assertEquals(StringUtils::getByteSize(512 * 1024 * 1024 * 1024), '512.0 GB');
		$this->assertEquals(StringUtils::getByteSize(512 * 1024 * 1024 * 1024 * 1024), '512.0 TB');
		$this->assertEquals(StringUtils::getByteSize(512 * 1024 * 1024 * 1024 * 1024 * 1024), '512.0 PB');
	}

	public function testContains() {
		$this->assertTrue(StringUtils::contains($this->string, 'the'));
		$this->assertTrue(StringUtils::contains($this->string, 'The', false));

		$this->assertTrue(StringUtils::contains($this->multiByteString, 'gęślą'));
		$this->assertTrue(StringUtils::contains($this->multiByteString, 'GĘŚLĄ', false));
	}

	public function testLength() {
		$this->assertFalse(StringUtils::isLengthBetween($this->string, 0, 10));
		$this->assertFalse(StringUtils::isLengthBetween($this->string, -1, 10));
		$this->assertTrue(StringUtils::isLengthBetween(' ', 1, 1));
		$this->assertTrue(StringUtils::isLengthBetween('', 0, 1));
	}

	public function testHighlight() {
		$this->assertEquals(StringUtils::highlightWords($this->string, 'colorful'), 'here is the <b>colorful</b> kahless?');
		$this->assertEquals(StringUtils::highlightWords($this->multiByteString, 'gęślą'), 'żółć <b>gęślą</b> jaźń');
	}

	public function testEntities() {
		$this->assertEquals(StringUtils::htmlEntities($this->string), 'here is the colorful kahless?');
		$this->assertEquals(StringUtils::htmlEntities($this->multiByteString), 'ż&oacute;łć gęślą jaźń');
	}

	public function testXml() {
		$this->assertEquals(StringUtils::xmlEscape($this->string), 'here is the colorful kahless?');
		$this->assertEquals(StringUtils::xmlEscape($this->multiByteString), 'żółć gęślą jaźń');
		$this->assertEquals(StringUtils::xmlEscape($this->xmlString), '<![CDATA[Escape this & this.]]>');
	}
}

