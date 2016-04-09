<?php namespace Cog;

use Cog\Exceptions\InvalidCastException;
use ReflectionClass;
use ReflectionException;
use Carbon\Carbon;
use Stringy\Stringy;

/**
 * Type Library to add some support for strongly named types.
 *
 * PHP does not support strongly named types.  The Qcodo type library
 * and Qcodo typing in general attempts to bring some structure to types
 * when passing in values, properties, parameters to/from Qcodo framework objects
 * and methods.
 *
 * The Type library attempts to allow as much flexibility as possible to
 * set and cast variables to other types, similar to how PHP does it natively,
 * but simply adds a big more structure to it.
 *
 * For example, regardless if a variable is an integer, boolean, or string,
 * Type::Cast will allow the flexibility of those values to interchange with
 * each other with little to no issue.
 *
 * In addition to value objects (integers, booleans, floats, strings), the Type library
 * also supports object casting.  While technically casting one object to another
 * is not a true cast, Type::Cast does at least ensure that the tap being "casted"
 * to is a legitimate subclass of the object being "cast".  So if you have ParentClass,
 * and you have a ChildClass that extends ParentClass,
 *        $objChildClass = new ChildClass();
 *        $objParentClass = new ParentClass();
 *        Type::Cast($objChildClass, 'ParentClass'); // is a legal cast
 *        Type::Cast($objParentClass, 'ChildClass'); // will throw an InvalidCastException
 *
 * For values, specifically int to string conversion, one different between
 * Type::Cast and PHP (in order to add structure) is that if an integer contains
 * alpha characters, PHP would normally allow that through w/o complaint, simply
 * ignoring any numeric characters past the first alpha character.  Type::Cast
 * would instead throw an InvalidCastException to let the developer immediately
 * know that something does not look right.
 *
 * In theory, the type library should maintain the same level of flexibility
 * PHP developers are accustomed to, while providing a mechanism to limit
 * careless coding errors and tough to figure out mistakes due to PHP's sometimes
 * overly relaxed type conversions.
 */
abstract class Type {
	const STRING = 'string';
	const INTEGER = 'integer';
	const FLOAT = 'double';
	const BOOLEAN = 'boolean';
	const OBJECT = 'object';
	const ARRAYTYPE = 'array';
	const DATETIME = 'Carbon';

	private static function castObjectTo($item, $type) {
		$reflection = new ReflectionClass($item);

		try {
			if ($reflection->getName() === 'SimpleXMLElement') {
				switch ($type) {
					case Type::STRING:
						return (string)$item;

					case Type::INTEGER:
						try {
							return Type::cast((string)$item, Type::INTEGER);
						} catch (\Cog\Exception $objExc) {
							$objExc->incrementOffset();
							throw $objExc;
						}

					case Type::BOOLEAN:
						$strItem = strtolower(trim((string)$item));
						return ($strItem !== 'false' || !$strItem);
				}
			}
		} catch (Exception $objExc) {}

		if ($type === Type::DATETIME && $item instanceof Carbon) {
			return $item;
		}

		//convert stringy to string
		if ($type === Type::STRING && $item instanceof Stringy) {
			return (string)$item;
		}

		if ($item instanceof $type) {
			return $item;
		}

		throw new InvalidCastException(sprintf('Unable to cast %s object to %s', $reflection->getName(), $type));
	}

	private static function castValueTo($item, $type) {
		$itemType = gettype($item);

		switch ($type) {
			case Type::BOOLEAN:
				if ($itemType === Type::BOOLEAN) {
					return $item;
				}

				if (null === $item) {
					return false;
				}

				if ($item === '') {
					return false;
				}

				if (strtolower($item) === 'false') {
					return false;
				}

				settype($item, $type);
				return $item;

			case Type::INTEGER:
			case Type::FLOAT:
				if ($item === '') {
					return null;
				}

				$original = $item;
				settype($item, $type);

				// Check to make sure the value hasn't changed significantly
				$mixTest = $item;
				settype($mixTest, gettype($original));

				// Has it?
				if ($mixTest != $original) {
					// Yes -- therefore this is an invalid cast
					throw new InvalidCastException(sprintf('Unable to cast %s value to %s: %s', $itemType, $type, $original));
				}

				return $item;

			case Type::STRING:
				settype($item, $type);

				/*
				// Check to make sure the value hasn't changed significantly
				$test = $item;
				settype($test, gettype($original));

				// Has it?
				if ($test != $original)
					// Yes -- therefore this is an invalid cast
					throw new InvalidCastException(sprintf('Unable to cast %s value to %s: %s', $itemType, $type, $original));
				*/

				return $item;

			default:
				throw new InvalidCastException(sprintf('Unable to cast %s value to %s', $itemType, $type));
		}
	}

	/**
	 * Used to cast a variable to another type.  Allows for moderate
	 * support of strongly-named types.
	 *
	 * Will throw an exception if the cast fails, causes unexpected side effects,
	 * if attempting to cast an object to a value (or vice versa), or if an object
	 * is being cast to a class that isn't a subclass (e.g. parent).  The exception
	 * thrown will be an InvalidCastException, which extends CallerException.
	 *
	 * @param mixed $item the value, array or object that you want to cast
	 * @param string $type the type to cast to.  Can be a Type::XXX constant (e.g. Type::Integer), or the name of a Class
	 * @return mixed the passed in value/array/object that has been cast to strType
	 * @throws \Cog\Exception
	 */
	public final static function cast($item, $type) {
		// Automatically Return NULLs
		if (null === $item) {
			return null;
		}

		// Figure out what PHP thinks the type is
		$strPhpType = gettype($item);

		switch ($strPhpType) {
			case Type::OBJECT:
				try {
					return Type::castObjectTo($item, $type);
				} catch (\Cog\Exception $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case Type::STRING:
			case Type::INTEGER:
			case Type::FLOAT:
			case Type::BOOLEAN:
				try {
					return Type::castValueTo($item, $type);
				} catch (\Cog\Exception $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			case Type::ARRAYTYPE:
				try {
					if ($type === Type::ARRAYTYPE) {
						return $item;
					} else {
						throw new InvalidCastException(sprintf('Unable to cast Array to %s', $type));
					}
				} catch (\Cog\Exception $objExc) {
					$objExc->incrementOffset();
					throw $objExc;
				}

			default:
				throw new InvalidCastException(sprintf('Unable to determine type of item to be cast: %s', $item));
		}
	}

	/**
	 * Used by the Qcodo Code Generator to allow for the code generation of
	 * the actual "Type::Xxx" constant, instead of the text of the constant,
	 * in generated code.
	 *
	 * It is rare for Constant to be used manually outside of Code Generation.
	 *
	 * @param string $type the type to convert to 'constant' form
	 * @return string the text of the Text:Xxx Constant
	 * @throws InvalidCastException
	 */
	public final static function constant($type) {
		switch ($type) {
			case Type::OBJECT:
				return 'Type::OBJECT';
			case Type::STRING:
				return 'Type::STRING';
			case Type::INTEGER:
				return 'Type::INTEGER';
			case Type::FLOAT:
				return 'Type::FLOAT';
			case Type::BOOLEAN:
				return 'Type::BOOLEAN';
			case Type::ARRAYTYPE:
				return 'Type::ARRAYTYPE';
			case Type::DATETIME:
				return 'Type::DATETIME';

			default:
				// Could not determine type
				throw new InvalidCastException(sprintf('Unable to determine type of item to lookup its constant: %s', $type));
		}
	}

	public final static function typeFromPhpDoc($type) {
		switch (strtolower($type)) {
			case 'string':
			case 'str':
				return Type::STRING;

			case 'integer':
			case 'int':
				return Type::INTEGER;

			case 'float':
			case 'flt':
			case 'double':
			case 'dbl':
			case 'single':
			case 'decimal':
				return Type::FLOAT;

			case 'bool':
			case 'boolean':
			case 'bit':
				return Type::BOOLEAN;

			case 'datetime':
			case 'date':
			case 'time':
			case 'carbon':
				return Type::DATETIME;

			case 'null':
			case 'void':
				return 'void';

			default:
				try {
					new ReflectionClass($type);
					return $type;
				} catch (ReflectionException $objExc) {
					throw new InvalidCastException(sprintf('Unable to determine type of item from PHPDoc Comment to lookup its Type or Class: %s', $type));
				}
		}
	}
}
