# Litero

Extremely simple router for small web applications.
Small footprint and no overhead.

Router class in this package route HTTP requests by URL pattern to callbacks. When the current request URL matches one of the patterns, the respective callback function is called to handle the request.

Note it's component not support PSR-7 HTTP Message Interface out of the box.

## Installing

Create project based on Litero in current directory:

```
composer create-project bit55/litero .
```

And add your application code to autoloading section in `composer.json`.

Or include Litero in your existing project:

```
composer require bit55/litero
```

Or download this repository and include file `Router.php` in your project.

Your webserver must point to the index.php file for any URI entered. See `.htaccess` for Apache and `example.nginx.conf` for Nginx configuration example.

## Usage

Create Instance of Router class.

```php
$router = Bit55\Litero\Router::fromGlobals();
```

Add routing rules. Routes may contents exact or wildcard rules.

Wildcards example:
 * `/page/:seg` - any characters in one segment like `/page/qwerty` or `/page/123`;
 * `/page/:num` - digits only like `/page/123`;
 * `/page/:any` - any characters like `/page/qwerty` or `/page/qwerty/123`;

Route handler may be any callable (function name, closure) or string with controller class name and action method. Router instantiate controller and execute action method automatically.

Wildcard parameters will be passed as function params in handler.

Note if you using Composer, add your controller classes to autoloading. 

Add single rule with Closure handler:

```php
$router->add('/', function () {
    echo 'Hello from Litero!';
});
```

Or add array of routes.

```php
$router->add([
    '/first'       => 'Bit55\Litero\ExampleController@firstAction',
    '/second/:any'  => 'Bit55\Litero\ExampleController@secondAction',
]);
```

Start route processing.


```php
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
```

Usage example code see in `index.php` file.
