<?php

namespace Sketch;

use Symfony\Component\HttpFoundation\Request;

class QueryStringRouter implements RouterInterface {

    public $routes = array();
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function __construct(Dispatcher $dispatcher, Request $request)
    {
        $this->dispatcher = $dispatcher;
        $this->request = $request;
    }

    public function register($method, $params, $controller)
    {
        if (is_string($params)) {
            parse_str(ltrim($params, '?'), $param_array);
        } else {
            $param_array = $params;
        }

        array_push($this->routes, array(
            'method' => $method,
            'params' => $param_array,
            'controller' => $controller)
        );
    }

    public function get($params, $controller)
    {
        $this->register('GET', $params, $controller);
    }

    public function post($params, $controller)
    {
        $this->register('POST', $params, $controller);
    }

    public function any($params, $controller)
    {
        $this->register('GET', $params, $controller);
        $this->register('POST', $params, $controller);
    }

    public function resolve()
    {
        $params = $this->request->query->all();
        $method = $this->request->getMethod();

        foreach ($this->routes as $route) {
            if($method === strtoupper($route['method'])) {
                $i = 0;
                foreach($route['params'] as $k => $v) {
                    if (!$this->matches($params, $k, $v)) {
                        break;
                    }
                    $i++;
                    if ($i === count($route['params'])){
                        return $this->dispatch($route['controller']);
                    }
                }
            }
        }
        return false;
    }

    protected function dispatch($controller)
    {
        return $this->dispatcher->dispatch($controller);
    }
    
    protected function matches($request_params, $route_param_name, $route_param_value)
    {
        if (!isset($request_params[$route_param_name])) return false;

        $request_value = $request_params[$route_param_name];

        if ($route_param_value === '{int}' && ctype_digit($request_value)) return true;

        return $request_value === $route_param_value;
    }

    protected function convertParamStringToArray($params)
    {
        $params = ltrim($params, '?');
        $param_array = explode('&', ltrim($params, '?'));

    }

} 