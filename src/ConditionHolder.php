<?php
/**
 *
 * PHP version 5.6, 7.0 and 7.1
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
    protected $conditions = [];
    protected $bindAnd = true;

    protected $functionToExecuteIfTrue = null;
    protected $functionToExecuteIfFalse = null;

    public function add($condition)
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function bindAnd()
    {
        $this->bindAnd = true;
        return $this;
    }

    public function bindOr()
    {
        $this->bindAnd = false;
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


    public function doIt()
    {
        if ($this->check()) {
            if (!$this->functionToExecuteIfTrue) {
                return null;
            }
            return ($this->functionToExecuteIfTrue)();
        } else {
            if (!$this->functionToExecuteIfFalse) {
                return null;
            }
            return ($this->functionToExecuteIfFalse)();
        }
    }

    public function check()
    {
        // no conditions - to do anyway
        if (!$this->conditions) {
            return true;
        }
        $array = $this->conditions;
        $initial = array_shift($array);
        return array_reduce($array, $this->carry(), $initial);
    }


    protected function carry()
    {
        $self = $this;
        return function ($carry, $condition) use ($self) {
            if ($condition instanceof ConditionHolder) {
                $condition = $condition->check();
            }
            if ($self->bindAnd) {
                return ($carry and $condition);
            }
            return ($carry or $condition);
        };
    }
}