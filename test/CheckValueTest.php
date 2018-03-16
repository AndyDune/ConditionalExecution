<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 16.03.2018                            |
 * -----------------------------------------------
 *
 */


namespace AndyDuneTest\ConditionalExecution;
use AndyDune\ConditionalExecution\CheckValue;
use AndyDune\ConditionalExecution\ConditionHolder;
use AndyDune\ConditionalExecution\Example\CheckSome;
use PHPUnit\Framework\TestCase;


class CheckValueTest extends TestCase
{
    public function testAlone()
    {
        $checkValue = new CheckValue('rzn');
        $checkValue->isEqualTo('rzn1');
        $this->assertFalse($checkValue->check());

        $checkValue = new CheckValue('rzn');
        $checkValue->isEqualTo('rzn');
        $this->assertTrue($checkValue->check());

        $checkValue = new CheckValue('1');
        $checkValue->isEqualTo(1, false);
        $this->assertTrue($checkValue->check());

        $checkValue->isArray();
        $this->assertFalse($checkValue->check());


        $checkValue = new CheckValue('1');
        $checkValue->isEqualTo(1);
        $this->assertFalse($checkValue->check());


        $checkValue = new CheckValue(['ab', 'ba']);
        $checkValue->isHaveValue('ab');
        $this->assertTrue($checkValue->check());

        $checkValue->isHaveValue('ba');
        $this->assertTrue($checkValue->check());

        $checkValue->isHaveValue('abbb');
        $this->assertFalse($checkValue->check());


        $checkValue = new CheckValue(new CheckSome());
        $checkValue->isInstanceOf(CheckSome::class);
        $this->assertTrue($checkValue->check());


        $checkValue = new CheckValue('12');
        $checkValue->isInArray(23);
        $this->assertFalse($checkValue->check());

        $checkValue = new CheckValue('12');
        $checkValue->isInArray([12]);
        $this->assertTrue($checkValue->check());

        $checkValue = new CheckValue('12');
        $checkValue->isInArray(23);
        $checkValue->isInArray(12);
        $this->assertFalse($checkValue->check());

        $checkValue->bindOr();
        $this->assertTrue($checkValue->check());

    }

    public function testWithConditionHolder()
    {
        $checkValue = new CheckValue('12');
        $checkValue->isInArray(23);
        $checkValue->isInArray([12, 45]);
        $condition = new ConditionHolder();
        $condition->add($checkValue);

        $this->assertFalse($checkValue->check());

        $checkValue->bindOr();
        $this->assertTrue($condition->check());

        $condition->add(isset($val));
        $this->assertFalse($condition->check());

        $condition = new ConditionHolder();
        $condition->add(!isset($val));
        $this->assertTrue($condition->check());

        $checkValue->bindAnd();
        $condition->add($checkValue);
        $this->assertFalse($condition->check());

        $condition->bindOr();
        $this->assertTrue($condition->check());


    }
}