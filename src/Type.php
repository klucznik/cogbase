<?php namespace Cog;

use Cog\Exceptions\InvalidCastException;
use Carbon\Carbon;
use Stringy\Stringy;

/**
 * Type Library to add some support for strongly named types.
 *
 * PHP does not support strongly named types.  The type library
 * and typing in general attempts to bring some structure to types
 * when passing in values, properties, parameters to/from framework objects
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
 *        $childClass = new ChildClass();
 *        $parentClass = new ParentClass();
 *        Type::Cast($childClass, 'ParentClass'); // is a legal cast
 *        Type::Cast($parentClass, 'ChildClass'); // will throw an InvalidCastException
 *
 * For values, specifically int to string conversion, one different between
 * Type::Cast and PHP (in order to add structure) is that if an integer contains
 * alpha characters, PHP would normally allow that through w/o complaint, simply
 * ignoring any numeric characters past the first alpha character. Type::Cast
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
		$reflection = new \ReflectionClass($item);

		try {
			if ($reflection->getName() === 'SimpleXMLElement') {
				switch ($type) {
					case self::STRING:
						return (string)$item;

					case self::INTEGER:
						try {
							return self::cast((string)$item, self::INTEGER);
						} catch (Exception $exception) {
							$exception->incrementOffset();
							throw $exception;
						}

					case self::BOOLEAN:
						$item = strtolower(trim((string)$item));
						return ($item !== 'false' || !$item);
				}
			}
		} catch (Exception $exception) {}

		if ($type === self::DATETIME && $item instanceof Carbon) {
			return $item;
		}

		//convert stringy to string
		if ($type === self::STRING && $item instanceof Stringy) {
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
			case self::BOOLEAN:
				if ($itemType === self::BOOLEAN) {
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

			case self::INTEGER:
			case self::FLOAT:
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

			case self::STRING:
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
	 * @param string $type the type to cast to.  Can be a Type::XXX constant (e.g. Type::INTEGER), or the name of a Class
	 * @return mixed the passed in value/array/object that has been cast to strType
	 * @throws \Cog\Exception
	 */
	public final static function cast($item, $type) {
		// Automatically Return NULLs
		if (null === $item) {
			return null;
		}

		// Figure out what PHP thinks the type is
		switch (gettype($item)) {
			case self::OBJECT:
				try {
					return self::castObjectTo($item, $type);
				} catch (Exception $exception) {
					$exception->incrementOffset();
					throw $exception;
				}

			case self::STRING:
			case self::INTEGER:
			case self::FLOAT:
			case self::BOOLEAN:
				try {
					return self::castValueTo($item, $type);
				} catch (Exception $exception) {
					$exception->incrementOffset();
					throw $exception;
				}

			case self::ARRAYTYPE:
				try {
					if ($type === self::ARRAYTYPE) {
						return $item;
					}
					throw new InvalidCastException(sprintf('Unable to cast Array to %s', $type));
				} catch (Exception $exception) {
					$exception->incrementOffset();
					throw $exception;
				}

			default:
				throw new InvalidCastException(sprintf('Unable to determine type of item to be cast: %s', $item));
		}
	}

	/**
	 * Used by the Code Generator to allow for the code generation of
	 * the actual "Type::XXX" constant, instead of the text of the constant,
	 * in generated code.
	 *
	 * It is rare for Constant to be used manually outside of Code Generation.
	 *
	 * @param string $type the type to convert to 'constant' form
	 * @return string the text of the Type:XXX Constant
	 * @throws InvalidCastException
	 */
	public final static function constant($type) {
		switch ($type) {
			case self::OBJECT:
				return 'self::OBJECT';
			case self::STRING:
				return 'self::STRING';
			case self::INTEGER:
				return 'self::INTEGER';
			case self::FLOAT:
				return 'self::FLOAT';
			case self::BOOLEAN:
				return 'self::BOOLEAN';
			case self::ARRAYTYPE:
				return 'self::ARRAYTYPE';
			case self::DATETIME:
				return 'self::DATETIME';

			default:
				// Could not determine type
				throw new InvalidCastException(sprintf('Unable to determine type of item to lookup its constant: %s', $type));
		}
	}

	public final static function typeFromPhpDoc($type) {
		switch (strtolower($type)) {
			case 'string':
			case 'str':
				return self::STRING;

			case 'integer':
			case 'int':
				return self::INTEGER;

			case 'float':
			case 'flt':
			case 'double':
			case 'dbl':
			case 'single':
			case 'decimal':
				return self::FLOAT;

			case 'bool':
			case 'boolean':
			case 'bit':
				return self::BOOLEAN;

			case 'datetime':
			case 'date':
			case 'time':
			case 'carbon':
				return self::DATETIME;

			case 'null':
			case 'void':
				return 'void';

			default:
				try {
					new \ReflectionClass($type);
					return $type;
				} catch (\ReflectionException $exception) {
					throw new InvalidCastException(sprintf('Unable to determine type of item from PHPDoc Comment to lookup its Type or Class: %s', $type));
				}
		}
	}
}
