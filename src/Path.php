<?php namespace Cog;

use UnexpectedValueException;

abstract class Path {
	/**
	 * Path of the "web root" of the web server, points to the /www subdirectory
	 * Like "/home/www/htdocs/www" on Linux/Unix or "c:\inetpub\wwwroot\www" on Windows
	 * @var string
	 */
	public static $webRoot;

	/**
	 * Path of the "application root" or "document root" of the web server
	 * Like "/home/www/htdocs" on Linux/Unix or "c:\inetpub\wwwroot" on Windows
	 * @var string
	 */
	public static $appRoot;

	/**
	 * Full path of the actual PHP script being run Like "/home/www/htdocs/folder/script.php" on Linux/Unix
	 * or "c:\inetpub\wwwroot" on Windows
	 * @var string
	 */
	public static $scriptFilename;

	/**
	 * Web-relative path of the actual PHP script being run.
	 * So for "http://www.domain.com/folder/script.php", it would be "/folder/script.php"
	 * @var string
	 */
	public static $scriptName;

	/** @var boolean A flag to indicate whether or not this script is run as a CLI (Command Line Interface) */
	protected static $cliMode;

	/**
	 * This should be the first call to initialize all the static variables The application object also has
	 * static methods that are miscellaneous web development utilities, etc.
	 * @throws UnexpectedValueException
	 * @return void
	 */
	public static function initialize(): void {
		// Are we running as CLI?
		self::$cliMode = !array_key_exists('SERVER_PROTOCOL', $_SERVER);

		if (self::$cliMode === true) {
			self::initializeCli();
		} else {
			self::initializeWeb();
		}

		self::$appRoot = dirname(self::$webRoot);
	}

	protected static function initializeCli(): void {
		$path = dirname(__DIR__);
		while (is_dir($path . '/www') === false) {
			$path = dirname($path);
		}
		self::$webRoot = $path . '/www';
	}

	protected static function initializeWeb() : void {
		// Setup ScriptFilename and ScriptName
		self::$scriptFilename = $_SERVER['SCRIPT_FILENAME'];

		if ($_SERVER['SCRIPT_NAME']) {
			self::$scriptName = $_SERVER['SCRIPT_NAME'];
		} else {
			self::$scriptName = $_SERVER['PHP_SELF'];
		}

		// Ensure both are set, or we'll have to abort
		if (!self::$scriptFilename || !self::$scriptName) {
			throw new UnexpectedValueException('Error on Cog\Path::initialize() - scriptFilename or scriptName was not set');
		}

		// Setup WebRoot -- WebRoot will NOT be set and therefore needs to be magically
		if (array_key_exists('DOCUMENT_ROOT', $_SERVER) && $_SERVER['DOCUMENT_ROOT']) {
			self::$webRoot = $_SERVER['DOCUMENT_ROOT'];
		}

		$fc = self::firstCharacter(self::$scriptFilename);

		if (substr(self::$scriptFilename, 1, 2) === ':\\') {
			// looks like Windows files system, we need to first ascertain a DOS-compatible "Script Name"
			$scriptName = str_replace('/', '\\', self::$scriptName);
		} elseif ($fc === '/' || $fc === '.') { // Unix
			$scriptName = self::$scriptName;
		} else {
			throw new UnexpectedValueException(
				'Error on Cog\Path::initialize() - Could not ascertain file system type from scriptFilename'
			);
		}

		// Ensure that ScriptFilename ENDS with ScriptName
		$substrResult = strpos(self::$scriptFilename, $scriptName);
		$strlenResult = strlen(self::$scriptFilename) - strlen($scriptName);
		if ($substrResult === $strlenResult) {
			self::$webRoot = substr(self::$scriptFilename, 0, $strlenResult);
		} else {
			throw new UnexpectedValueException(
				'Error on Cog\Path::initialize() - scriptFilename does not end with scriptName'
			);
		}

		// Cleanup WebRoot -- path should not end with a trailing / or \
		while (self::lastCharacter(self::$webRoot) === '/' || self::lastCharacter(self::$webRoot) === '\\') {
			self::$webRoot = substr(self::$webRoot, 0, -1);
		}
	}

	/**
	 * Returns true if the environment is command line
	 * @return bool
	 */
	public static function isCLI(): bool {
		return self::$cliMode;
	}

	/**
	 * @param string $string input string
	 * @return string | null
	 */
	protected static function firstCharacter($string): ?string {
		if (strlen($string) > 0) {
			return $string[0];
		}
		return null;
	}

	/**
	 * @param string $string input string
	 * @return string | null
	 */
	protected static function lastCharacter($string): ?string {
		$length = strlen($string);
		if ($length > 0) {
			return $string[$length - 1];
		}
		return null;
	}

	/**
	 * For development purposes, this static method outputs all the Paths
	 * @return array
	 */
	final public static function dump(): array {
		return [
			'appRoot' => self::$appRoot,
			'webRoot' => self::$webRoot,
			'scriptFilename' => self::$scriptFilename,
			'scriptName' => self::$scriptName,
			'cli' => self::isCLI()
		];
	}
}

Path::initialize();
