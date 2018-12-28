<?php

namespace components\Enum;

use ReflectionClass;
use RuntimeException;


/**
 * Class Enum
 * @package components\Enum
 */
abstract class Enum
{
	/** @var null */
	private static $constCacheArray = NULL;
	/** @var mixed */
	private $value;

	/**
	 * @param $value
	 */
	protected function __construct($value)
	{
		$this->value = $value;
	}

    /**
     * @param $name
     * @return static
     * @throws \ReflectionException
     */
	public static function byName($name)
	{
		if (!self::isValidName($name))
			throw new RuntimeException("Bad name . " . $name . " for " . get_called_class());

		$value = self::getConstants()[$name];

		return new static($value);
	}

    /**
     * @param $value
     * @return static
     * @throws \ReflectionException
     */
	public static function byValue($value)
	{
		if (!self::isValidValue($value))
			throw new RuntimeException("Bad value . " . $value . " for " . get_called_class());

		return new static($value);
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

    /**
     * @return mixed
     * @throws \ReflectionException
     */
	public static function getConstants()
	{
		if (self::$constCacheArray == NULL) {
			self::$constCacheArray = [];
		}
		$calledClass = get_called_class();
		if (!array_key_exists($calledClass, self::$constCacheArray)) {
			$reflect = new ReflectionClass($calledClass);
			self::$constCacheArray[$calledClass] = $reflect->getConstants();
		}
		return self::$constCacheArray[$calledClass];
	}

    /**
     * @param $name
     * @param bool $strict
     * @return bool
     * @throws \ReflectionException
     */
	public static function isValidName($name, $strict = false)
	{
		$constants = self::getConstants();

		if ($strict) {
			return array_key_exists($name, $constants);
		}

		$keys = array_map('strtolower', array_keys($constants));
		return in_array(strtolower($name), $keys);
	}

    /**
     * @param $value
     * @param bool $strict
     * @return bool
     * @throws \ReflectionException
     */
	public static function isValidValue($value, $strict = false)
	{
		$values = self::getValues();
		return in_array($value, $values, $strict);
	}

    /**
     * @return array
     * @throws \ReflectionException
     */
	public static function getValues()
	{
		return array_values(self::getConstants());
	}

    /**
     * @return Enum
     * @throws \ReflectionException
     */
	public static function getRandomValue()
	{
		$values = static::getValues();
		return self::byValue($values[array_rand($values)]);
	}

    /**
     * @param $name
     * @param $arguments
     * @return Enum
     * @throws \ReflectionException
     */
	public static function __callStatic($name, $arguments)
	{
		return self::byName($name);
	}

	/**
	 * @param $equals
	 * @return bool
	 */
	public function equals($equals)
	{
		return $this->value == $equals;
	}

	/**
	 * @return mixed
	 */
	final public function __toString()
	{
		return $this->value;
	}

}
