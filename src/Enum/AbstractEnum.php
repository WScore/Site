<?php
namespace WScore\Site\Enum;

abstract class AbstractEnum
{
    /**
     * values that can be selected.
     * [ value => label name, ... ]
     *
     * @var array
     */
    protected static $choices = array();

    /**
     * @var array
     */
    protected $selection = [];

    /**
     * Enum value
     *
     * @var mixed
     */
    protected $value;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param string $value
     * @throws \InvalidArgumentException
     */
    public function __construct($value = null)
    {
        if (is_null($value)) {
            $value = constant("static::__DEFAULT");
        }
        if (!static::isValidChoice($value)) {
            throw new \InvalidArgumentException("no such value: " . $value);
        }
        $this->selection = static::$choices;
        $this->value     = $value;
    }

    /**
     * @param string $const
     * @param array  $args
     * @return static
     */
    public static function __callStatic($const, $args)
    {
        if (defined("static::{$const}")) {
            return new static(constant("static::{$const}"));
        }
        throw new \BadMethodCallException;
    }

    /**
     * Returns all possible values and strings as an array
     *
     * @param null|string $value
     * @return array|string Constant name in key, constant value in value
     */
    public static function getChoices($value = null)
    {
        if (is_null($value)) {
            return static::$choices;
        }
        return static::isValidChoice($value) ? static::$choices[$value] : null;
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isValidChoice($value)
    {
        return array_key_exists($value, static::$choices);
    }

    // +----------------------------------------------------------------------+
    //  object method
    // +----------------------------------------------------------------------+
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @param array $selection
     * @return AbstractEnum
     */
    protected function cloneWithSelection(array $selection)
    {
        if (!array_key_exists($this->value, $selection)) {
            throw new \InvalidArgumentException;
        }
        $status            = clone($this);
        $status->selection = $selection;
        return $status;
    }

    /**
     * @param string $method
     * @param array  $args
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 2) == 'is') {
            $const = strtoupper(substr($method, 2));
            if (defined("static::{$const}")) {
                return $this->is(constant("static::{$const}"));
            }
            return false;
        }
        throw new \InvalidArgumentException("no such method: " . $method);
    }

    /**
     * @return array
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @return string
     */
    public function value()
    {
        return (string)$this->value;
    }

    /**
     * @return string
     */
    public function label()
    {
        return array_key_exists($this->value, $this->selection) ?
            $this->selection[$this->value] : null;
    }

    /**
     * @param $value
     * @return bool
     */
    public function is($value)
    {
        return $value === $this->value;
    }
    // +----------------------------------------------------------------------+
}