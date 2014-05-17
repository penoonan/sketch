<?php

namespace Sketch;

use Illuminate\Container\Container;
use League\Plates\Template;
use Symfony\Component\HttpFoundation\Request;

class ControllerDispatcher {

    /**
     * @var \Illuminate\Container\Container
     */
    private $app;

    /**
     * If you choose to namespace your controllers,
     * pass that namespace in as a parameter to this class
     * in app.php
     * @var string
     */
    private $controller_namespace;

    protected $args = array();

    /**
     * @param Container $app
     * @param null $controller_namespace
     */
    public function __construct(Container $app, $controller_namespace = null)
    {
        if ($controller_namespace) {
            $this->controller_namespace = trim($controller_namespace, '\\');
        }
        $this->app = $app;
    }

    public function dispatch($controller, array $args = array())
    {
        if (is_callable($controller)) {
            return call_user_func_array($controller, $args);
        }

        list($class, $method) = explode('@', $controller);
        $class = ucwords($class) . 'Controller';
        return call_user_func_array(array($this->controller($class), $method), $args);
    }

    private function controller($class)
    {
        $controller_class = $this->app->make($this->controller_namespace . '\\' . $class);
        $controller_class->setTemplate($this->app['template']);
        $controller_class->setRequest($this->app['request']);
        return $controller_class;
    }

}