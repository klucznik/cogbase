<?php require_once __DIR__ . '/../vendor/autoload.php';

	class TestExpired extends \SagePay\Test\TestBase {

		public function testExpiredCard() {
			$this->sagepay->execute();

			$this->assertEquals(\SagePay\StatusType::INVALID, $this->sagepay->status);
			$this->assertEquals('5013 : The card has expired.', $this->sagepay->error);
		}
	}