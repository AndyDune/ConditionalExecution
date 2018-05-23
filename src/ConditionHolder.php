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

class ConditionHolder
{
    use CheckTrait;
    protected $conditions = [];

    protected $functionToExecuteIfTrue = null;
    protected $functionToExecuteIfFalse = null;

    protected $functionToTriggerIfTrue = [];
    protected $functionToTriggerIfFalse = [];


    public function add($condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function cleanConditions()
    {
        $this->conditions = [];
        return $this;
    }

    /**
     * @param callable|ConditionHolder $function
     * @return ConditionHolder
     */
    public function executeIfTrue($function)
    {
        if ($function instanceof self) {
            $function = function ($value = null) use ($function) {
                return $function->doIt($value);
            };
        }

        $this->functionToExecuteIfTrue = $function;
        return $this;
    }

    /**
     * @param callable|ConditionHolder $function
     * @return ConditionHolder
     */
    public function executeIfFalse($function)
    {
        if ($function instanceof self) {
            $function = function ($value = null) use ($function) {
                return $function->doIt($value);
            };
        }
        $this->functionToExecuteIfFalse = $function;
        return $this;
    }

    public function triggerIfTrue(callable $function)
    {
        $this->functionToTriggerIfTrue[] = $function;
        return $this;
    }

    public function triggerIfFalse(callable $function)
    {
        $this->functionToTriggerIfFalse[] = $function;
        return $this;
    }

    public function doIt($value = null)
    {
        if ($this->check($value)) {
            if ($this->functionToTriggerIfTrue) {
                array_walk($this->functionToTriggerIfTrue, function ($function, $key) {
                    call_user_func($function);
                });
            }
            if (!$this->functionToExecuteIfTrue) {
                return null;
            }
            return call_user_func($this->functionToExecuteIfTrue, $value);
        } else {
            if ($this->functionToTriggerIfFalse) {
                array_walk($this->functionToTriggerIfFalse, function ($function, $key) {
                    call_user_func($function);
                });
            }

            if (!$this->functionToExecuteIfFalse) {
                return null;
            }
            return call_user_func($this->functionToExecuteIfFalse, $value);
        }
    }

    public function check($value = null)
    {
        // no conditions - to do anyway
        if (!$this->conditions) {
            return true;
        }
        $array = $this->conditions;

        $result = array_shift($array);
        if ($result instanceof ConditionHolder or $result instanceof CheckAbstract) {
            $result = $result->check($value);
        } else if (is_callable($result)) {
            $result = $result($value);
        }

        $result = (bool)$result;

        foreach($array as $condition) {
            if ($condition instanceof ConditionHolder or $condition instanceof CheckValue) {
                $condition = $condition->check($value);
            }
            if ($this->bindAnd) {
                if (is_callable($condition)) {
                    $condition = $condition($value);
                }
                $result = ($result and $condition);
                if (!$result) {
                    // don't need next check
                    break;
                }
                continue;
            }

            if (is_callable($condition)) {
                $condition = $condition($value);
            }
            $result = ($result or $condition);

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


    /**
     * @deprecated
     * @return \Closure
     */
    protected function carry()
    {
        $self = $this;
        return function ($carry, $condition) use ($self) {
            if ($condition instanceof ConditionHolder) {
                $condition = $condition->check();
            }
            if ($self->bindAnd) {
                $result = ($carry and $condition);
                if (!$result) {
                    // don't need next check
                    $exception = new Exception();
                    $exception->setValue(false);
                    throw $exception;
                }
                return $result;
            }
            $result = ($carry or $condition);

            if ($result) {
                // don't need next check
                $exception = new Exception();
                $exception->setValue(true);
                throw $exception;
            }
            return $result;
        };
    }
}