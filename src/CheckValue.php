<?php
/**
 *
 * PHP version 5.6, 7.X
 *
 * @package andydune/conditional-execution
 * @link  https://github.com/AndyDune/ConditionalExecution for the canonical source repository
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @author Andrey Ryzhov  <info@rznw.ru>
 * @copyright 2018 Andrey Ryzhov
 */


namespace AndyDune\ConditionalExecution;
use AndyDune\ConditionalExecution\Check\CheckAbstract;

class CheckValue extends CheckAbstract
{
    use CheckTrait;
    protected $value;

    protected $checkFunctions = [];

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function check($value = null)
    {
        // no conditions - to do anyway
        if (!$this->checkFunctions) {
            return true;
        }
        $array = $this->checkFunctions;

        $result = array_shift($array);
        $result = (bool)$result($this->value);

        foreach($array as $condition) {
            if ($this->bindAnd) {
                $result = ($result and (bool)$condition($this->value));
                if (!$result) {
                    // don't need next check
                    break;
                }
                continue;
            }

            $result = ($result or (bool)$condition($this->value));

            if ($result) {
                // don't need next check
                break;
            }
        }

        if ($this->negative) {
            $result = !$result;
        }
        return $result;
    }

    public function isArray()
    {
        $this->checkFunctions[] = function ($value) {
            return is_array($value);
        };
        return $this;
    }

    public function isInArray($array)
    {
        $this->checkFunctions[] = function ($value) use ($array) {
            if (!is_array($array)) {
                $array = [$array];
            }
            return in_array($value, $array);
        };
        return $this;
    }

    public function isEqualTo($checkValue, $strong = true)
    {
        $this->checkFunctions[] = function ($value) use ($checkValue, $strong) {
            if ($strong) {
                return ($value === $checkValue);
            }
            return ($value == $checkValue);
        };
        return $this;
    }

    /**
     * Use it if verifiable value is array
     * For more correction
     *
     * @param $checkValue
     * @return $this
     */
    public function isHaveValue($checkValue)
    {
        $this->checkFunctions[] = function ($value) use ($checkValue) {
            if (!is_array($value)) {
                return false;
            }
            return in_array($checkValue, $value);
        };
        return $this;
    }

    /**
     * Add custom function to check value.
     *
     * Function mast have one parameter ant return boolean value.
     *
     * @param callable $function
     * @return $this
     */
    public function add(callable $function)
    {
        $this->checkFunctions[] = $function;
        return $this;
    }

    public function isInstanceOf($checkValue)
    {
        if (is_object($checkValue)) {
            $checkValue = get_class($checkValue);
        }
        $this->checkFunctions[] = function ($value) use ($checkValue) {
            return ($value instanceof $checkValue) ? true : false;
        };
        return $this;
    }

}