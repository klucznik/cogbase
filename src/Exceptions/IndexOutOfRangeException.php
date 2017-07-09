<?php namespace Cog\Exceptions;

use Cog\Exception;

class IndexOutOfRangeException extends Exception {

	/**
	 * @param integer $index
	 * @param string $message the message of the exception
	 */
	public function __construct($index, $message) {
		if ($message) {
			$message = ': ' . $message;
		}
		parent::__construct(sprintf('Index (%s) is out of range%s', $index, $message), 2);
	}
}
