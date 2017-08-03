<?php
/**
 * Example application with Litero.
 * 
 * @link      https://github.com/bit55/litero
 * @copyright Copyright (c) 2017 Eugene Dementyev.
 * @license   https://opensource.org/licenses/BSD-3-Clause
 *
 * Routes may contents exact or  wildcard rules.
 * 
 * Windcards example:
 * `/page/:seg` - any characters in one segment like `/page/qwerty` or `/page/123`;
 * `/page/:num` - digits only like `/page/123`;
 * `/page/:any` - any characters like `/page/qwerty` or `/page/qwerty/123`;
 *
 * Route handler may be any callable (function name, closure) or string with controller class name and action method.
 * Router instantiate controller and execute action method automatically.
 * Wildcard parameters will be passed as function params in handler.
 * Note if you using Composer, add your controller classes to autoloading.
 */

require 'vendor/autoload.php';

/* @var $router Bit55\Litero\Router */
$router = Bit55\Litero\Router::fromGlobals();

// Add single rule with Closure handler.
$router->add('/', function () {
    echo 'Hello from Litero!';
});

// Or add array of routes.
$router->add([
    '/first'       => 'Bit55\Litero\ExampleController@firstAction',
    '/second/:any'  => 'Bit55\Litero\ExampleController@secondAction',
]);

// Start route processing
if ($router->isFound()) {
    $router->executeHandler(
        $router->getRequestHandler(),
        $router->getParams()
    );
} 
else {
    // Simple "Not found" handler
    $router->executeHandler(function () {
        http_response_code(404);
        echo '404 Not found';
    });
}
