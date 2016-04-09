<?php namespace Cog\Exceptions;

class UndefinedPropertyException extends \Cog\Exception {
	public function __construct($type, $class, $property) {
		parent::__construct(sprintf('Undefined %s property or variable in "%s" class: %s', $type, $class, $property), 2);
	}
}