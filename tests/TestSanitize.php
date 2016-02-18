<?php require_once __DIR__ . '/../vendor/autoload.php';

	use SagePay\Sanitize;

	class TestSanitize extends \PHPUnit_Framework_TestCase {

		public function testNames() {
			$string = 'Adam & Co';
			$this->assertEquals($string, Sanitize::names($string));
			$this->assertEquals('Adam ', Sanitize::names('Adam!'));
		}

		public function testAddress() {
			$string = 'Adam & Co 48 road.';
			$this->assertEquals($string, Sanitize::address($string));
			$this->assertEquals('Adam ', Sanitize::address('Adam!'));
			$this->assertEquals(' & () +', Sanitize::address('!#$%^&*()_+'));
		}

		public function testDigits() {
			$this->assertEquals('5543543254353', Sanitize::digits('5543543254353'));
			$this->assertEquals('4929000000006', Sanitize::digits('4929 0000 0000 6'));
			$this->assertEquals('4929000000006', Sanitize::digits('4929-0000-0000-6'));
		}

		public function testCardHolder() {
			$string = 'Card holder. &/-\\';
			$this->assertEquals($string, Sanitize::cardHolder($string));
			//$this->assertEquals('Złomnik. &/-\\', Sanitize::cardHolder('Złomnik. &/-\\!!!!'));
		}

		public function testPostcode() {
			$string = 'Zlomnik -';
			$this->assertEquals($string, Sanitize::postcode($string));
			$string = '00-079';
			$this->assertEquals($string, Sanitize::postcode($string));
			$string = 'M22 4XE';
			$this->assertEquals($string, Sanitize::postcode($string));
		}

		public function testPhone() {
			$string = '(+48) 604 453 543';
			$this->assertEquals($string, Sanitize::phone($string));
			$string = 'miacha';
			$this->assertEquals($string, Sanitize::phone($string));
		}
	}