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


class GetFirstSuccessResult
{
    protected $functions = [];

    protected $resultChecker = false;

    public function add(callable $function)
    {
        $this->functions[] = $function;
        return $this;
    }

    /**
     * Add result checker.
     * This function gets result from each work function.
     * Return true if result is suitable and false if not.
     *
     * @param callable $function
     * @return $this
     */
    public function setResultChecker(callable $function)
    {
        $this->resultChecker = $function;
        return $this;
    }

    /**
     * @return bool|mixed
     */
    public function get()
    {
        $params = func_get_args();
        foreach ($this->functions as $function) {
            $result = call_user_func_array($function, $params);
            if ($this->resultChecker) {
                if (call_user_func($this->resultChecker, $result)) {
                    return $result;
                }
            }
            if ($result) {
                return $result;
            }
        }
        return false;
    }

}