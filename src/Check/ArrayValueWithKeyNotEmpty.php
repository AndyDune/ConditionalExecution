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


class ArrayValueWithKeyNotEmpty extends CheckAbstract
{

    protected $array;

    public function __construct($array)
    {
        $this->array = $array;
    }

    public function check($value)
    {
        if (!array_key_exists($value, $this->array)) {
            return false;
        }
        return (bool)$this->array[$value];
    }
}