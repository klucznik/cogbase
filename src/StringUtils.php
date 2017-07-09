<?php namespace Cog;

/**
 * An abstract utility class to handle string manipulation.  All methods
 * are statically available.
 */
abstract class StringUtils {
	/**
	 * Returns the first character of a given string, or null if the given
	 * string is null.
	 * @param string $string
	 * @return string the first character, or null
	 */
	final public static function firstCharacter($string) {
		if (mb_strlen($string) > 0) {
			return mb_substr($string, 0, 1);
		}
		return null;
	}

	/**
	 * Returns the last character of a given string, or null if the given string is null.
	 * @param string $string
	 * @return string the last character, or null
	 */
	final public static function lastCharacter($string) {
		$length = mb_strlen($string);
		if ($length > 0) {
			return mb_substr($string, -1);
		}
		return null;
	}

	/**
	 * Checks if begins with the given string
	 * @param string $haystack input string
	 * @param string $needle beginning of the string to test against
	 * @return boolean
	 */
	final public static function beginsWith($haystack, $needle) {
		return 0 === mb_strpos($haystack, $needle);
	}

	/**
	 * Escapes the string so that it can be safely used in as an Xml Node (basically, adding CDATA if needed)
	 * @param string $string string to escape
	 * @return string the XML Node-safe StringUtils
	 */
	final public static function xmlEscape($string) {
		if ((mb_strpos($string, '<') !== false) ||
			(mb_strpos($string, '&') !== false)
		) {
			$string = str_replace(']]>', ']]]]><![CDATA[>', $string);
			$string = sprintf('<![CDATA[%s]]>', $string);
		}

		return $string;
	}

	/**
	 * Given an integer that represents a byte size, this will return a string
	 * displaying the value in bytes, KB, MB, GB, TB or PB
	 * @param integer $bytes
	 * @param integer $numberOfTenths
	 * @return string
	 */
	public static function getByteSize($bytes, $numberOfTenths = 1) {
		if (null === $bytes) {
			return 'N/A';
		}

		if ($bytes === 0) {
			return '0 bytes';
		}

		$toReturn = '';
		if ($bytes < 0) {
			$bytes *= -1;
			$toReturn .= '-';
		}

		if ($bytes === 1) {
			$toReturn = '1 byte';
		} elseif ($bytes < 1024) {
			$toReturn .= $bytes . ' bytes';
		} elseif ($bytes < (1024 * 1024)) {
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f KB', $bytes / 1024);
		} elseif ($bytes < (1024 * 1024 * 1024)) {
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f MB', $bytes / (1024 * 1024));
		} elseif ($bytes < (1024 * 1024 * 1024 * 1024)) {
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f GB', $bytes / (1024 * 1024 * 1024));
		} elseif ($bytes < (1024 * 1024 * 1024 * 1024 * 1024)) {
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f TB', $bytes / (1024 * 1024 * 1024 * 1024));
		} else {
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f PB', $bytes / (1024 * 1024 * 1024 * 1024 * 1024));
		}

		return $toReturn;
	}

	/**
	 * Checks if text length is between given bounds
	 * @param string $string Text to be checked
	 * @param integer $minimumLength Minimum acceptable length
	 * @param integer $maximumLength Maximum acceptable length
	 * @return boolean
	 */
	public static function isLengthBetween($string, $minimumLength, $maximumLength) {
		$length = mb_strlen($string);
		return ($length >= $minimumLength && $length <= $maximumLength);
	}

	/**
	 * Global/Central HtmlEntities command to perform the PHP equivalent of htmlentities.
	 * Feel free to override to specify encoding/quoting specific preferences (e.g. ENT_QUOTES/ENT_NOQUOTES, etc.)
	 *
	 * This method is also used by the global print "_p" function.
	 *
	 * @param string $text text string to perform html escaping
	 * @return string the html escaped string
	 */
	public static function htmlEntities($text) {
		return htmlentities($text, ENT_IGNORE, 'UTF-8');
	}

	/**
	 * Returns true if the string contains $needle, false otherwise. By default
	 * the comparison is case-sensitive, but can be made insensitive by setting
	 * $caseSensitive to false.
	 *
	 * @param  string $haystack      input string
	 * @param  string $needle        Substring to look for
	 * @param  bool   $caseSensitive Whether or not to enforce case-sensitivity
	 * @return bool   Whether or not $haystack contains $needle
	 */
	public static function contains($haystack, $needle, $caseSensitive = true) {
		if ($caseSensitive) {
			return (mb_strpos($haystack, $needle, 0) !== false);
		}
		return (mb_stripos($haystack, $needle, 0) !== false);
	}

	/**
	 * @param $string string input string
	 * @param $highlightWords string[] words to highlight in array
	 * @return mixed
	 */
	public static function highlightWords($string, $highlightWords) {
		foreach ($highlightWords as $word) {
			$string = str_ireplace($word, '<b>' . $word . '</b>', $string);
		}
		return $string;
	}
}
