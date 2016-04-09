<?php namespace Cog;

use Cog\Exceptions\UndefinedPropertyException;
use ReflectionClass;

/**
 * This is the Base Class for ALL classes in the system.  It provides
 * proper error handling of property getters and setters.  It also
 * provides the OverrideAttribute functionality.
 */
abstract class Base {
	/**
	 * Override method to perform a property "Get" This will get the value of $strName
	 * All inherited objects that call __get() should always fall through
	 * to calling parent::__get() in a try/catch statement catching for CallerExceptions.
	 *
	 * @param string $name Name of the property to get
	 * @return mixed the returned property
	 * @throws \Cog\Exceptions\UndefinedPropertyException
	 */
	public function __get($name) {
		$reflection = new ReflectionClass($this);
		throw new UndefinedPropertyException('GET', $reflection->getName(), $name);
	}

	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName to be $mixValue
	 * All inherited objects that call __set() should always fall through
	 * to calling parent::__set() in a try/catch statement catching for CallerExceptions.
	 *
	 * @param string $name Name of the property to set
	 * @param string $value New value of the property
	 * @return mixed the property that was set
	 * @throws \Cog\Exceptions\UndefinedPropertyException
	 */
	public function __set($name, $value) {
		$reflection = new ReflectionClass($this);
		throw new UndefinedPropertyException('SET', $reflection->getName(), $name);
	}

	/**
	 * This allows you to set any properties, given by a name-value pair list in $overrideArray.
	 *
	 * Each item in mixOverrideArray needs to be either a string in the format
	 * of Property=Value or an array in the format of array(Property => Value).
	 * OverrideAttributes() will basically call
	 * $this->Property = Value for each string element in the array.
	 *
	 * Value can be surrounded by quotes... but this is optional.
	 *
	 * @param mixed[] $overrideArray the array of name-value pair items of properties/attributes to override
	 * @return void
	 * @throws \Cog\Exception
	 */
	public final function overrideAttributes($overrideArray) {
		// Iterate through the OverrideAttribute Array
		if ($overrideArray) {
			foreach ($overrideArray as $overrideItem) {
				if (is_array($overrideItem)) {
					foreach ($overrideItem as $key => $value)
						// Apply the override
						try {
							$this->__set($key, $value);
						} catch (\Cog\Exception $objExc) {
							$objExc->incrementOffset();
							throw $objExc;
						}
				} else {
					// Extract the Key and Value for this OverrideAttribute
					$position = strpos($overrideItem, '=');
					if ($position === false) {
						throw new \Cog\Exception(sprintf('Improperly formatted OverrideAttribute: %s', $overrideItem));
					}
					$key = substr($overrideItem, 0, $position);
					$value = substr($overrideItem, $position + 1);

					// Ensure that the Value is properly formatted (unquoted, single-quoted, or double-quoted)
					if (StringUtils::firstCharacter($value) === "'") {
						if (substr($value, -1) !== "'") {
							throw new \Cog\Exception(sprintf('Improperly formatted OverrideAttribute: %s', $overrideItem));
						}
						$value = substr($value, 1, -2);
					} elseif (StringUtils::firstCharacter($value) === '"') {
						if (substr($value, -1) !== '"') {
							throw new \Cog\Exception(sprintf('Improperly formatted OverrideAttribute: %s', $overrideItem));
						}
						$value = substr($value, 1, -2);
					}

					// Apply the override
					try {
						$this->__set($key, $value);
					} catch (\Cog\Exception $objExc) {
						$objExc->incrementOffset();
						throw $objExc;
					}
				}
			}
		}
	}
}