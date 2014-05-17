<?php

namespace Sketch;

use Symfony\Component\HttpFoundation\Request;

class QueryStringRouter implements RouterInterface {

    public $routes = array();

    /**
     * @var ControllerDispatcher
     */
    protected $dispatcher;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function __construct(ControllerDispatcher $dispatcher, Request $request)
    {
        $this->dispatcher = $dispatcher;
        $this->request = $request;
    }

    public function register($method, $params, $controller, $script = 'admin')
    {
        if (is_string($params)) {
            parse_str(ltrim($params, '?'), $param_array);
        } else {
            $param_array = $params;
        }

        array_push($this->routes, array(
            'method' => $method,
            'params' => $param_array,
            'controller' => $controller,
            'script' => $script
          )
        );
    }

    public function get($params, $controller, $script = 'admin')
    {
        $this->register('GET', $params, $controller, $script);
    }

    public function post($params, $controller, $script = 'admin')
    {
        $this->register('POST', $params, $controller, $script);
    }

    public function any($params, $controller, $script = 'admin')
    {
        $this->register('GET', $params, $controller, $script);
        $this->register('POST', $params, $controller, $script);
    }

    public function resolve()
    {
        $params = $this->request->query->all();
        $method = $this->request->getMethod();
        $script = $this->request->server->get('SCRIPT_NAME');

        foreach ($this->routes as $route) {
            if($method === strtoupper($route['method']) && $script === $this->formatScript($route['script'])) {
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

    protected function formatScript($script)
    {
        if (FALSE === strpos($script, '/wp-admin/')) {
            $script = '/wp-admin/' . $script;
        }
        if (FALSE === strpos($script, '.php')) {
            $script .= '.php';
        }
        return strtolower($script);
    }

} 