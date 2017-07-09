<?php namespace Cog\Exceptions;

use Cog\Exception;

/**
 * The exception that is thrown by Type::cast
 * if an invalid cast is performed.  InvalidCastException
 * derives from CallerException, and therefore should be handled
 * similar to how CallerExceptions are handled (e.g. incrementOffset should
 * be called whenever an InvalidCastException is caught and rethrown).
 */
class InvalidCastException extends Exception {

	/**
	 * @param string $message the message of the exception
	 * @param integer $offset the optional offset value (currently defaulted to 2)
	 */
	public function __construct($message, $offset = 2) {
		parent::__construct($message, $offset);
	}
}
