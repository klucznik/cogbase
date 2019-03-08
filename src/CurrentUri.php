<?php namespace Cog;

use League\Uri\Components\Query;
use League\Uri\Http as HttpUri;
use League\Uri\Http;
use League\Uri\UriException;

abstract class CurrentUri {

	/** @var string */
	public static $scheme;

	/** @var string */
	public static $host;

	/**
	 * Extended path Information after the script URL (if applicable)
	 * So for "http://www.domain.com/folder/script.php/15/225" it would be "/15/255"
	 * @var string
	 */
	public static $pathInfo;

	/** @var string[] */
	protected static $pathInfoArray = [];

	/**
	 * Query string after the script URL (if applicable)
	 * So for "http://www.domain.com/folder/script.php?item=15&value=22" $strQueryString would be "item=15&value=22"
	 * @var string
	 */
	public static $queryString;

	/** @var string[] */
	protected static $queryArray = [];

	/**
	 * The full RequestURI that was requested
	 * So for "http://www.domain.com/folder/script.php/15/25/?item=15&value=22"
	 * $requestUri would be "/folder/script.php/15/25/?item=15&value=22"
	 * @var string
	 */
	public static $requestUri;

	/**
	 * This should be the first call to initialize all the static variables
	 * @throws \UnexpectedValueException
	 * @return void
	 */
	public static function initialize() : void {
		$uri = null;

		try {
			$uri = HttpUri::createFromServer($_SERVER);
		} catch (UriException $e) {}

		if ($uri instanceof Http) {
			self::$scheme = $uri->getScheme();
			self::$host = $uri->getHost();

			// Ensure both are set, or we'll have to abort
			if (!Path::$scriptFilename || !Path::$scriptName) {
				throw new \UnexpectedValueException('Error on self::initialize() - scriptFilename or scriptName was not set');
			}

			// Setup queryString
			self::$queryString = $uri->getQuery();

			$query = new Query(self::$queryString);
			self::$queryArray = $query->getPairs();

			// Setup path
			self::$pathInfo = $uri->getPath();

			if (StringUtils::beginsWith(self::$pathInfo, Path::$scriptName)) { //clean up path
				self::$pathInfo = substr(self::$pathInfo, \strlen(Path::$scriptName));
			}

			if (self::$pathInfo === false) {
				self::$pathInfo = '';
			}

			if (self::$pathInfo !== null && self::$pathInfo !== false && self::$pathInfo !== '') {
				$pathInfo = self::$pathInfo; // store path info array
				if (0 === strncmp($pathInfo, '/', 1)) { //begins with '/'
					$pathInfo = substr($pathInfo, 1); // Remove Trailing '/'
				}

				self::$pathInfoArray = explode('/', $pathInfo);
			}

			// Setup requestUri
			if (array_key_exists('REQUEST_URI', $_SERVER)) {
				self::$requestUri = $_SERVER['REQUEST_URI'];
			} else {
				self::$requestUri = sprintf(
					'%s%s%s',
					Path::$scriptName,
					self::$pathInfo,
					self::$queryString ? sprintf('?%s', self::$queryString) : ''
				);
			}
		}
	}

	/**
	 * Gets the value of the pathInfo item at given index. Will return null if it doesn't exist.
	 *
	 * The way pathInfo index is determined is, for example, given a URL '/folder/page.php/id/15/blue',
	 * CurrentUri::pathInfo(0) will return 'id'
	 * CurrentUri::pathInfo(1) will return '15'
	 * CurrentUri::pathInfo(2) will return 'blue'
	 *
	 * @param integer | null $index
	 * @return array | string | null
	 */
	 final public static function pathInfo($index = null) {
		if ($index === null) {
			return self::$pathInfoArray;
		}

		if (array_key_exists($index, self::$pathInfoArray)) {
			return self::$pathInfoArray[$index];
		}

		return null;
	}

	/**
	 * Gets the value of the queryString item $item.  Will return NULL if it doesn't exist.
	 * @param string $item
	 * @return string | null
	 */
	public static function queryString($item) : ?string {
		if (array_key_exists($item, self::$queryArray)) {
			return self::$queryArray[$item];
		}

		return null;
	}

	/**
	 * For development purposes, this static method outputs all the Paths
	 * @return array
	 */
	final public static function dump() : array {
		return [
			'scheme' => self::$scheme,
			'host' => self::$host,
			'pathInfoString' => self::$pathInfo,
			'pathInfoArray' => self::$pathInfoArray,
			'queryString' => self::$queryString,
			'queryArray' => self::$queryArray,
			'requestUri' => self::$requestUri
		];
	}
}

CurrentUri::initialize();
