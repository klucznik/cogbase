<?php namespace Cog;

/**
 * This is the main exception to be thrown by any
 * method to indicate that the CALLER is responsible for
 * causing the exception.  This works in conjunction with
 * error handling/reporting, so that the correct file/line-number is
 * displayed to the user.
 *
 * So for example, for a class that contains the method GetItemAtIndex($intIndex),
 * it is conceivable that the caller could call GetItemAtIndex(15), where 15 does not exist.
 * GetItemAtIndex would then thrown an IndexOutOfRangeException (which extends CallerException).
 * If the CallerException is not caught, then the Exception will be reported to the user.  The CALLER
 * (the script who CALLED GetItemAtIndex) would have that line highlighted as being responsible
 * for calling the error.
 *
 * The PHP default for exception reporting would normally say that the "throw Exception" line in GetItemAtIndex
 * is responsible for throwing the exception.  While this is technically true, in reality, it was the line that
 * CALLED GetItemAtIndex which is responsible.  In short, this allows for much cleaner exception reporting.
 *
 * On a more in-depth note, in general, suppose a method OuterMethod takes in parameters, and ends up passing those
 * parameters into ANOTHER method InnerMethod which could throw a CallerException.  OuterMethod is responsible
 * for catching and rethrowing the caller exception.  And when this is done, incrementOffset() MUST be called on
 * the exception object, to indicate that OuterMethod's CALLER is responsible for the exception.
 *
 * So the code snippet to call InnerMethod by OuterMethod should look like:
 *    function OuterMethod($mixValue) {
 *        try {
 *            InnerMethod($mixValue);
 *        } catch (\Cog\Exception\Exception $objExc) {
 *            $objExc->incrementOffset();
 *            throw $objExc;
 *        }
 *        // Do Other Stuff
 *    }
 * Again, this will assure the user that the line of code responsible for the exception is properly being reported
 * by the error reporting/handler.
 */
class Exception extends \Exception {
	private $offset;
	private $traceArray;

	/**
	 * The constructor of CallerExceptions.  Takes in a message string
	 * as well as an optional Offset parameter (defaults to 1).
	 * The Offset specifies how many calls up the call stack is responsible
	 * for the exception.  By definition, when a CallerException is called,
	 * at the very least the Caller of the most immediate function, which is
	 * 1 up the call stack, is responsible.  So therefore, by default, intOffset
	 * is set to 1.
	 *
	 * It is rare for intOffset to be set to an integer other than 1.
	 *
	 * Normally, the Offset would be altered by calls to incrementOffset
	 * at every step the CallerException is caught/rethrown up the call stack.
	 * @param string $message the Message of the exception
	 * @param integer $offset the optional Offset value (currently defaulted to 1)
	 * @return \Cog\Exception the new exception
	 */
	public function __construct($message, $offset = 1) {
		parent::__construct($message);
		$this->offset = $offset;
		$this->traceArray = debug_backtrace();

		$this->file = $this->traceArray[$this->offset]['file'];
		$this->line = $this->traceArray[$this->offset]['line'];
	}

	public function incrementOffset() {
		$this->offset++;
		if (array_key_exists('file', $this->traceArray[$this->offset]))
			$this->file = $this->traceArray[$this->offset]['file'];
		else
			$this->file = '';
		if (array_key_exists('line', $this->traceArray[$this->offset]))
			$this->line = $this->traceArray[$this->offset]['line'];
		else
			$this->line = '';
	}

	public function decrementOffset() {
		$this->offset--;
		if (array_key_exists('file', $this->traceArray[$this->offset]))
			$this->file = $this->traceArray[$this->offset]['file'];
		else
			$this->file = '';
		if (array_key_exists('line', $this->traceArray[$this->offset]))
			$this->line = $this->traceArray[$this->offset]['line'];
		else
			$this->line = '';
	}

	public function __get($strName) {
		if ($strName == "Offset")
			return $this->offset;
		else if ($strName == "BackTrace") {
			$objTraceArray = debug_backtrace();
			return (var_export($objTraceArray, true));
		} else if ($strName == "TraceArray") {
			return $this->traceArray;
		}

		return null;
	}

	public function setMessage($message) {
		$this->message = $message;
	}
}