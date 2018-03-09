<?php

/**
 * Extra light router and dispatcher for small web applications.
 * Small footprint, no overhead.
 *
 * @link      https://github.com/bit55/litero
 * @copyright Copyright (c) 2017 Eugene Dementyev.
 * @license   https://opensource.org/licenses/BSD-3-Clause
 *
 * Routes may contents exact or  wildcard rules.
 *
 * Wildcards example:
 * `/page/:seg` - any characters in one segment like `/page/qwerty` or `/page/123`;
 * `/page/:num` - digits only like `/page/123`;
 * `/page/:any` - any characters like `/page/qwerty` or `/page/qwerty/123`;
 *
 * Route handler may be any callable (function name, closure) or string with controller class name and action method.
 * Router instantiate controller and execute action method automatically.
 * Wildcard parameters will be passed as function params in handler.
 * Note if you using Composer, add your controller classes to autoloading.
 */

namespace Bit55\Litero;

class Router
{
    protected $routes = [];
    
    protected $requestUri;
    
    protected $requestMethod;
    
    protected $requestHandler;
    
    protected $params = [];
    
    protected $placeholders = [
        ':seg' => '([^\/]+)',
        ':num'  => '([0-9]+)',
        ':any'  => '(.+)'
    ];
    
    
    public function __construct($uri, $method = 'GET')
    {
        $this->requestUri = $uri;
        $this->requestMethod = $method;
    }
    
    /**
     * Factory method construct Router from global vars.
     * @return Router
     */
    public static function fromGlobals()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        return new static($uri, $_SERVER['REQUEST_METHOD']);
    }

    /**
     * Current processed URI.
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri; // ?: '/';
    }
    
    /**
     * Request method.
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }
    
    /**
     * Get Request handler.
     * @return string|callable
     */
    public function getRequestHandler()
    {
        return $this->requestHandler;
    }
    
    /**
     * Set Request handler.
     * @param $handler string|callable
     */
    public function setRequestHandler($handler)
    {
        $this->requestHandler = $handler;
    }
    
    /**
     * Request wildcard params.
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Add route rule.
     *
     * @param string|array $route A URI route string or array
     * @param mixed $handler Any callable or string with controller classname and action method like "ControllerClass@actionMethod"
     */
    public function add($route, $handler = null)
    {
        if ($handler !== null && !is_array($route)) {
            $route = array($route => $handler);
        }
        $this->routes = array_merge($this->routes, $route);

        return $this;
    }


    /**
     * Process requested URI.
     * @return bool
     */
    public function isFound()
    {
        $uri = $this->getRequestUri();
        
        // if URI equals to route
        if (isset($this->routes[$uri])) {
            $this->requestHandler = $this->routes[$uri];
            return true;
        }
        
        $find    = array_keys($this->placeholders);
        $replace = array_values($this->placeholders);

        foreach ($this->routes as $route => $handler) {
            // Replace wildcards by regex
            if (strpos($route, ':') !== false) {
                $route = str_replace($find, $replace, $route);
            }

            // Route rule matched
            if (preg_match('#^' . $route . '$#', $uri, $matches)) {
                $this->requestHandler = $handler;
                $this->params = array_slice($matches, 1);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Execute Request Handler.
     *
     * @param string|callable $handler
     * @param array $params
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function executeHandler($handler = null, $params = array())
    {
        if ($handler === null) {
            throw new \InvalidArgumentException(
                'Request handler not setted out. Please check '.__CLASS__.'::isFound() first'
            );
        }
        
        // execute action in callable
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        // execute action in controllers
        if (strpos($handler, '@')) {
            $ca = explode('@', $handler);
            $controllerName = $ca[0];
            $action = $ca[1];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
            } else {
                throw new \RuntimeException("Controller class '{$controllerName}' not found");
            }
            if (!method_exists($controller, $action)) {
                throw new \RuntimeException("Method '{$controllerName}::{$action}' not found");
            }
            
            return call_user_func_array(array($controller, $action), $params);
        }
    }
}
