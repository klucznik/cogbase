<?php namespace SagePay\Test;

	abstract class TestBase extends \PHPUnit_Framework_TestCase {

		/** @var \SagePay\SagePay */
		protected $sagepay;
		/** @var \SagePay\CustomerDetails */
		protected $customerData;

		public function setUp() {
			$this->customerData = new \SagePay\CustomerDetails();

			$this->customerData->cardHolder = 'Adam Test & Co.-\'';
			$this->customerData->cardNumber = ' 4929000000006';
			$this->customerData->expiryDate = '0112';
			$this->customerData->cardType = \SagePay\CardType::VISA;
			$this->customerData->cv2 = '123';

			$this->customerData->billingFirstnames = 'Adam';
			$this->customerData->billingSurname = 'Testing';
			$this->customerData->billingAddress1 = '88';
			$this->customerData->billingAddress2 = '432 Testing Road';
			$this->customerData->billingCity = 'Test Town';
			$this->customerData->billingCountry = 'GB';
			$this->customerData->billingPostCode = '412';
			$this->customerData->billingPhone = '';

			$this->customerData->deliveryFirstnames = 'Adam';
			$this->customerData->deliverySurname = 'Testing';
			$this->customerData->deliveryAddress1 = '88';
			$this->customerData->deliveryAddress2 = 'f';
			$this->customerData->deliveryCity = 'Test Town';
			$this->customerData->deliveryCountry = 'GB';
			$this->customerData->deliveryPostCode = '412';
			$this->customerData->deliveryPhone = '';

			$this->sagepay = new \SagePay\SagePay('megrivers', \SagePay\ModeType::TEST);
			$this->sagepay->customerDetails = $this->customerData;
			$this->sagepay->amount = 2.33;
			$this->sagepay->vendorTxCode = 1;
			$this->sagepay->description = 'test order';
		}

		protected function onNotSuccessfulTest($e) {
			dump($this->customerData);
			dump($this->sagepay->error);
			dump($this->sagepay->status);
			throw $e;
		}
	}