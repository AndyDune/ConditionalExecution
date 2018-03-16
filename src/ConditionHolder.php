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

    public function executeIfTrue(callable $function)
    {
        $this->functionToExecuteIfTrue = $function;
        return $this;
    }

    public function executeIfFalse(callable $function)
    {
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

    public function doIt()
    {
        if ($this->check()) {
            if ($this->functionToTriggerIfTrue) {
                array_walk($this->functionToTriggerIfTrue, function ($function, $key) {
                    call_user_func($function);
                });
            }
            if (!$this->functionToExecuteIfTrue) {
                return null;
            }
            return call_user_func($this->functionToExecuteIfTrue);
        } else {
            if ($this->functionToTriggerIfFalse) {
                array_walk($this->functionToTriggerIfFalse, function ($function, $key) {
                    call_user_func($function);
                });
            }

            if (!$this->functionToExecuteIfFalse) {
                return null;
            }
            return call_user_func($this->functionToExecuteIfFalse);
        }
    }

    public function check()
    {
        // no conditions - to do anyway
        if (!$this->conditions) {
            return true;
        }
        $array = $this->conditions;

        $result = array_shift($array);
        if ($result instanceof ConditionHolder or $result instanceof CheckValue) {
            $result = $result->check();
        }

        $result = (bool)$result;

        foreach($array as $condition) {
            if ($condition instanceof ConditionHolder or $condition instanceof CheckValue) {
                $condition = $condition->check();
            }
            if ($this->bindAnd) {
                $result = ($result and $condition);
                if (!$result) {
                    // don't need next check
                    break;
                }
                continue;
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