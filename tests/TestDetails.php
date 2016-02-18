<?php require_once __DIR__ . '/../vendor/autoload.php';

	use SagePay\Validate;

	class TestDetails extends \PHPUnit_Framework_TestCase {
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
		}

		public function tearDown() {
			unset($this->customerData);
		}

		public function testCountryPL() {
			$this->customerData->deliveryCountry = 'PL';
			$this->assertEquals('PL', $this->customerData->deliveryCountry);
		}

		public function testCountryPLN() {
			$this->customerData->deliveryCountry = 'PLN';
			$this->assertEquals('GB', $this->customerData->deliveryCountry);
		}

		public function testFirstname() {
			$this->customerData->deliveryFirstnames = 'Adam & Co';
			$this->assertEquals('Adam & Co', $this->customerData->deliveryFirstnames);
		}

		public function testAt() {
//			$this->customerData->deliveryFirstnames = '@ Michał';
			//$this->assertEquals('Michał', $this->customerData->deliveryFirstnames);
		}

		public function testCardHolder() {
			$this->assertTrue(Validate::cardHolder("O'malley"));
			$this->assertTrue(Validate::cardHolder('fdfgasd & co'));
			$this->assertTrue(Validate::cardHolder('fsdad - fdsaf'));
			$this->assertTrue(Validate::cardHolder('fdfads. fdsfads'));
			$this->assertTrue(Validate::cardHolder('fdfdsa \ fdsaf fa /'));
		}
	}