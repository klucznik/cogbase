<?php

use Cog\Utils;
use PHPUnit\Framework\TestCase;

class TestUtil extends TestCase {

	public function testExtendArray() {
		$toExtend = ['sample', 'array'];

		$this->assertSame($toExtend, Utils::extendArray($toExtend, null));
		$this->assertSame(['sample', 'array', false], Utils::extendArray($toExtend, false));
		$this->assertSame(array_merge($toExtend, $toExtend), Utils::extendArray($toExtend, $toExtend));
		$this->assertSame(array_merge($toExtend, ['string']), Utils::extendArray($toExtend, ['string']));
		$this->assertSame(array_merge($toExtend, ['string']), Utils::extendArray($toExtend, 'string'));
	}

	public function testPeriod() {
		$this->assertSame(36892800, Utils::getTimePeriodInSeconds('1 year 2 months'));
		$this->assertSame(36892801, Utils::getTimePeriodInSeconds('1 year 2 months 1 second'));
		$this->assertSame(0, Utils::getTimePeriodInSeconds(''));
		$this->assertSame(0, Utils::getTimePeriodInSeconds(null));
		$this->assertSame(3600, Utils::getTimePeriodInSeconds('1 hour'));
		$this->assertSame(0, Utils::getTimePeriodInSeconds(543));
	}
}
