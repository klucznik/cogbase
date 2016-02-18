<?php namespace Cog;

use Cog;
use QApplication;

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
	public final static function firstCharacter($string) {
		if (mb_strlen($string) > 0)
			return mb_substr($string, 0, 1);
		else
			return null;
	}

	/**
	 * Returns the last character of a given string, or null if the given
	 * string is null.
	 * @param string $string
	 * @return string the last character, or null
	 */
	public final static function lastCharacter($string) {
		$length = mb_strlen($string);
		if ($length > 0)
			return mb_substr($string, $length - 1);
		else
			return null;
	}

	/**
	 * Escapes the string so that it can be safely used in as an Xml Node (basically, adding CDATA if needed)
	 * @param string $string string to escape
	 * @return string the XML Node-safe StringUtils
	 */
	public final static function xmlEscape($string) {
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
	 * @return string
	 */
	public static function getByteSize($bytes, $numberOfTenths = 1) {
		if (is_null($bytes))
			return _t('qcodo.notAvailable');
		if ($bytes == 0)
			return '0 bytes';

		$toReturn = '';
		if ($bytes < 0) {
			$bytes = $bytes * -1;
			$toReturn .= '-';
		}

		if ($bytes == 1)
			$toReturn = '1 byte';
		else if ($bytes < 1024)
			$toReturn .= $bytes . ' bytes';
		else if ($bytes < (1024 * 1024))
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f KB', $bytes / (1024));
		else if ($bytes < (1024 * 1024 * 1024))
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f MB', $bytes / (1024 * 1024));
		else if ($bytes < (1024 * 1024 * 1024 * 1024))
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f GB', $bytes / (1024 * 1024 * 1024));
		else if ($bytes < (1024 * 1024 * 1024 * 1024 * 1024))
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f TB', $bytes / (1024 * 1024 * 1024 * 1024));
		else
			$toReturn .= sprintf('%.' . $numberOfTenths . 'f PB', $bytes / (1024 * 1024 * 1024 * 1024 * 1024));

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
		$intStringLength = mb_strlen($string);
		if (($intStringLength < $minimumLength) || ($intStringLength > $maximumLength))
			return false;
		else
			return true;
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
		return htmlentities($text, ENT_IGNORE, QApplication::$EncodingType);
	}

	public static function contains($string, $needle) {
		if (strpos($string, $needle) !== false)
			return true;

		return false;
	}

	public static function highlightWords($string, $highlightWords) {
		foreach ($highlightWords as $strWord) {
			$string = str_ireplace($strWord, '<b>' . $strWord . '</b>', $string);
		}

		return $string;
	}
}