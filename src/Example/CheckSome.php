<?php
/**
 * ----------------------------------------------
 * | Author: Andrey Ryzhov (Dune) <info@rznw.ru> |
 * | Site: www.rznw.ru                           |
 * | Phone: +7 (4912) 51-10-23                   |
 * | Date: 06.03.2018                            |
 * -----------------------------------------------
 *
 */

namespace AndyDune\ConditionalExecution\Example;

class CheckSome
{
    public function __invoke()
    {
        return 'callable';
    }

    public function doIfTrue()
    {
        return 'yes';
    }
}