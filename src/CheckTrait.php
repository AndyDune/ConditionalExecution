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


trait CheckTrait
{
    protected $bindAnd = true;
    protected $negative = false;

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

    /**
     *
     *
     * @param bool $flag
     * @return $this
     */
    public function setNegative($flag = true)
    {
        $this->negative = $flag;
        return $this;
    }

}