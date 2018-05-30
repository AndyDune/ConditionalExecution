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


namespace AndyDuneTest\ConditionalExecution;
use AndyDune\ConditionalExecution\Check\ArrayHasNotEmptyValueOrKeyNotExist;
use AndyDune\ConditionalExecution\Check\ArrayValueWithKeyNotEmpty;
use AndyDune\ConditionalExecution\ConditionHolder;
use PHPUnit\Framework\TestCase;

class InstanceofCheckAbstractTest extends TestCase
{
    public function testArrayValueWithKeyNotEmpty()
    {
        $array = [
            'one' => 1
        ];

        $condition = new ConditionHolder();
        $condition->add(new ArrayValueWithKeyNotEmpty('one'));
        $this->assertTrue($condition->check($array));
        $condition->setNegative();
        $this->assertFalse($condition->check($array));

        $condition = new ConditionHolder();
        $condition->add(new ArrayValueWithKeyNotEmpty('two'));
        $this->assertFalse($condition->check($array));
        $condition->setNegative();
        $this->assertTrue($condition->check($array));
    }

    public function testArrayHasNotEmptyValueOrKeyNotExist()
    {
        $array = [
            'one' => 1,
            'two' => '',
            'three' => 0
        ];

        $condition = new ConditionHolder();
        $condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('one'));
        $this->assertTrue($condition->check($array));

        $condition = new ConditionHolder();
        $condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('two'));
        $this->assertFalse($condition->check($array));

        $condition = new ConditionHolder();
        $condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('three'));
        $this->assertFalse($condition->check($array));

        $condition = new ConditionHolder();
        $condition->add(new ArrayHasNotEmptyValueOrKeyNotExist('four'));
        $this->assertTrue($condition->check($array));


    }
}