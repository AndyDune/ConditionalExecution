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


namespace AndyDune\ConditionalExecution\Check;


class ArrayHasNotEmptyValueOrKeyNotExist extends CheckAbstract
{

    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function check($value)
    {
        if (!array_key_exists($this->key, $value)) {
            return true;
        }

        if ($value[$this->key]) {
            return true;
        }

        return false;
    }

}