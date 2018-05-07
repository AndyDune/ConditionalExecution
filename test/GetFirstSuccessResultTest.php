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
use AndyDune\ConditionalExecution\GetFirstSuccessResult;
use PHPUnit\Framework\TestCase;


class GetFirstSuccessResultTest extends TestCase
{

    public function testSimple()
    {
        $instance = new GetFirstSuccessResult();
        $instance->add(function () {
            return 'one';
        });
        $instance->add(function () {
            return 'two';
        });

        $this->assertEquals('one', $instance->get());

        $instance = new GetFirstSuccessResult();
        $instance->add(function () {
            return '';
        });
        $instance->add(function () {
            return 'two';
        });

        $this->assertEquals('two', $instance->get());

    }

    public function testWithParam()
    {
        $instance = new GetFirstSuccessResult();
        $instance->add(function ($string, $length = 5) {
            if (strlen($string) < $length) {
                return $string . '<';
            }
            return false;
        });
        $instance->add(function ($string, $length = 5) {
            if (strlen($string) > $length) {
                return $string . '>';
            }
            return false;
        });

        $this->assertEquals('two<', $instance->get('two', 4));
        $this->assertEquals('two>', $instance->get('two', 2));
        $this->assertEquals('onetwo>', $instance->get('onetwo', 4));
        $this->assertEquals(false, $instance->get('tw', 2));

    }
}