<?php namespace Cog;

use Cog\Exceptions\UndefinedPropertyException;

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
	 * @throws UndefinedPropertyException
	 */
	public function __get($name) {
		try {
			$reflection = new \ReflectionClass($this);
			throw new UndefinedPropertyException('GET', $reflection->getName(), $name);
		} catch (\ReflectionException $exception) {}

		return null;
	}

	/**
	 * Override method to perform a property "set"
	 * This will set the property $name to be $value
	 * All inherited objects that call __set() should always fall through
	 * to calling parent::__set() in a try/catch statement catching for CallerExceptions.
	 *
	 * @param string $name Name of the property to set
	 * @param mixed $value New value of the property
	 * @throws UndefinedPropertyException
	 * @return mixed
	*/
	public function __set($name, $value) {
		try {
			$reflection = new \ReflectionClass($this);
			throw new UndefinedPropertyException('SET', $reflection->getName(), $name);
		} catch (\ReflectionException $exception) {}

		return null;
	}

	public function __isset($name) {
		return false;
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
	final public function overrideAttributes(array $overrideArray) {
		// Iterate through the OverrideAttribute Array
		if ($overrideArray) {
			foreach ($overrideArray as $overrideItem) {
				if (\is_array($overrideItem)) {
					foreach ($overrideItem as $key => $value) {
						// Apply the override
						try {
							$this->__set($key, $value);
						} catch (Exception $exception) {
							$exception->incrementOffset();
							throw $exception;
						}
					}
				} else {
					// Extract the Key and Value for this OverrideAttribute
					$position = strpos($overrideItem, '=');
					if ($position === false) {
						throw new Exception(sprintf('Improperly formatted OverrideAttribute: %s', $overrideItem));
					}
					$key = substr($overrideItem, 0, $position);
					$value = substr($overrideItem, $position + 1);

					// Ensure that the Value is properly formatted (unquoted, single-quoted, or double-quoted)
					if (StringUtils::beginsWith($value, "'")) {
						if (StringUtils::endsWith($value, "'") === false) {
							throw new Exception(sprintf('Improperly formatted OverrideAttribute: %s', $overrideItem));
						}
						$value = substr($value, 1, -2);
					} elseif (StringUtils::beginsWith($value,'"')) {
						if (StringUtils::endsWith($value, '"') === false) {
							throw new Exception(sprintf('Improperly formatted OverrideAttribute: %s', $overrideItem));
						}
						$value = substr($value, 1, -2);
					}

					// Apply the override
					try {
						$this->__set($key, $value);
					} catch (Exception $exception) {
						$exception->incrementOffset();
						throw $exception;
					}
				}
			}
		}
	}
}
