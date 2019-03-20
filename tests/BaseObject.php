<?php

use Cog\Base;

/**
 * @property string $MagicProperty
 */
class BaseObject extends Base {

	private $property;
	public $overrideProperty;

	/**
	 * @param string $name Name of the property to get
	 * @return mixed
	 * @throws \Cog\Exception
	 */
	public function __get($name) {
		switch ($name) {
			case 'MagicProperty':
				return $this->property;

			default:
				try {
					return parent::__get($name);
				} catch (\Cog\Exception $exception) {
					$exception->incrementOffset();
					throw $exception;
				}
		}
	}

	public function __isset($name) {
		switch ($name) {
			case 'MagicProperty':
				return true;

			default:
				try {
					return parent::__isset($name);
				} catch (\Cog\Exception $exception) {
					$exception->incrementOffset();
					throw $exception;
				}
		}
	}

	/**
	 * @param string $name Name of the property to set
	 * @param string $value New value of the property
	 * @return mixed
	 * @throws \Cog\Exception
	 */
	public function __set($name, $value) {
		switch ($name) {
			case 'MagicProperty':
				try {
					return $this->property = $value;
				} catch (\Cog\Exception $exception) {
					$exception->incrementOffset();
					throw $exception;
				}

			default:
				try {
					return parent::__set($name, $value);
				} catch (\Cog\Exception $exception) {
					$exception->incrementOffset();
					throw $exception;
				}
		}
	}
}
