<?php
/*
 * @link      https://github.com/bit55/litero
 * @copyright Copyright (c) 2017 Eugene Dementyev.
 * @license   https://opensource.org/licenses/BSD-3-Clause
 */

namespace Bit55\Litero;

class ExampleController
{
    /**
     * First Action
     */
    public static function firstAction()
    {
        echo 'Hello from '.__METHOD__.'!';
    }
    
    /**
     * Second Action
     */
    public static function secondAction($var)
    {
        echo 'Hello from '.__METHOD__.'( "'.$var.'" )!';
    }
}
