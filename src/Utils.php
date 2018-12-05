<?php namespace Cog;

/**
 * Other helpful functions
 */
abstract class Utils {

	/**
	 * This function merges two arrays or adds the value to the and of the given array.
	 * Return false if first param is not an array
	 * @param array $toBeExtended
	 * @param array|mixed $object
	 * @return array
	 */
	public static function extendArray(array $toBeExtended, $object) : array {
		if ($object === null) {
			return $toBeExtended;
		}

		if (is_array($object)) {
			$toBeExtended = array_merge($toBeExtended, $object);
		} else {
			$toBeExtended[] = $object;
		}

		return $toBeExtended;
	}

	/**
	 * Converts a human readable period to a number of seconds.
	 * For example "1 year", "2 months 1 second", "1 hour 1 second" etc.
	 * @param $period
	 * @return integer number in seconds
	 * @throws \Exception
	 */
	public static function getTimePeriodInSeconds($period) : int {
		return abs((new \DateTime($period))->getTimestamp() - (new \DateTime)->getTimestamp());
	}
}
