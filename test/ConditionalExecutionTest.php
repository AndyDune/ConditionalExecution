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

use AndyDune\ConditionalExecution\ConditionHolder;
use AndyDune\ConditionalExecution\Example\CheckSome;
use PHPUnit\Framework\TestCase;


class ConditionalExecutionTest extends TestCase
{
    public function testEmpty()
    {
        $instance = new ConditionHolder();
        $this->assertTrue($instance->check());
    }

    public function testAndBind()
    {
        $instance = new ConditionHolder();
        $instance->add(3 > 2);
        $this->assertTrue($instance->check());

        $instance = new ConditionHolder();
        $instance->add(3 < 2);
        $this->assertFalse($instance->check());

        $instance = new ConditionHolder();
        $instance->add(3 > 2)->add(4 > 3);
        $this->assertTrue($instance->check());

        $instance = new ConditionHolder();
        $instance->add(3 > 2)->add(4 < 3);
        $this->assertFalse($instance->check());
    }

    public function testOrBind()
    {
        $instance = new ConditionHolder();
        $instance->bindOr();
        $instance->add(3 > 2);
        $this->assertTrue($instance->check());

        $instance = new ConditionHolder();
        $instance->bindOr();
        $instance->add(3 < 2);
        $this->assertFalse($instance->check());

        $instance = new ConditionHolder();
        $instance->bindOr();
        $instance->add(3 > 2)->add(4 > 3);
        $this->assertTrue($instance->check());

        $instance = new ConditionHolder();
        $instance->bindOr();
        $instance->add(3 > 2)->add(4 < 3);
        $this->assertTrue($instance->check());

        $instance = new ConditionHolder();
        $instance->bindOr();
        $instance->add(3 < 2)->add(4 < 3);
        $this->assertFalse($instance->check());

    }

    public function testNested()
    {
        $instance1 = new ConditionHolder();
        $instance1->add(3 > 2);

        $instance2 = new ConditionHolder();
        $instance2->add(3 > 2)->add('r');


        $instance = new ConditionHolder();
        $instance->add($instance1)->add(56)->add($instance2);
        $this->assertTrue($instance->check());

        $instance2->add('');
        $this->assertFalse($instance->check());

        $instance2->bindOr();
        $this->assertTrue($instance->check());
    }

    public function testExecute()
    {
        $instance1 = new ConditionHolder();
        $instance1->executeIfTrue(function () {
            return 'yes';
        });
        $instance1->executeIfFalse(function () {
            return 'no';
        });

        $instance1->add(3 > 2);
        $this->assertEquals('yes', $instance1->doIt());

        $instance1->add(0);
        $this->assertEquals('no', $instance1->doIt());

        $instance1->bindOr();
        $this->assertEquals('yes', $instance1->doIt());

    }

    public function testExecuteObjectMethods()
    {
        $example = new CheckSome();

        $instance = new ConditionHolder();
        $instance->executeIfTrue([$example, 'doIfTrue']);
        $instance->executeIfFalse($example);

        $instance->add(3 > 2);
        $this->assertEquals('yes', $instance->doIt());

        $instance->add(false);
        $this->assertEquals('callable', $instance->doIt());

        $instance->executeIfTrue([$this, 'doItIfTrue']);
        $instance->bindOr();
        $this->assertEquals('self', $instance->doIt());
    }

    public function testTriggers()
    {
        $trigger = 0;
        $instance = new ConditionHolder();
        $instance->triggerIfTrue(function () use (&$trigger) {
            $trigger += 10;
        });
        $instance->triggerIfFalse(function () use (&$trigger) {
            $trigger += 100;
        });
        $instance->doIt();
        $this->assertEquals(10, $trigger);

        $instance->add('');

        $instance->doIt();
        $this->assertEquals(110, $trigger);

        $instance->doIt();
        $this->assertEquals(210, $trigger);

        $instance->triggerIfTrue(function () use (&$trigger) {
            $trigger += 10;
        });

        $instance->add('1')->bindOr();

        $instance->doIt();
        $this->assertEquals(230, $trigger);

    }

    public function doItIfTrue()
    {
        return 'self';
    }

    public function testNegative()
    {
        $instance = new ConditionHolder();
        $instance->executeIfTrue(function () {
            return true;
        });
        $instance->executeIfFalse(function () use (&$trigger) {
            return false;
        });
        $instance->add('');

        $this->assertFalse($instance->doIt());

        $instance->setNegative();
        $this->assertTrue($instance->doIt());

    }
}