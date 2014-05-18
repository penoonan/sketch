<?php

namespace Sketch;

use Sketch\Application;
use League\Plates\Template;
use Symfony\Component\HttpFoundation\Request;

class ControllerDispatcher {

    /**
     * @var ControllerFactoryInterface
     */
    private $controller_factory;

    /**
     * If you choose to namespace your controllers,
     * pass that namespace in as a parameter to this class
     * in app.php
     * @var string
     */
    private $controller_namespace;

    /**
     * @param ControllerFactoryInterface $controller_factory
     * @param null $controller_namespace
     */
    public function __construct(ControllerFactoryInterface $controller_factory, $controller_namespace = null)
    {
        if ($controller_namespace) {
            $this->controller_namespace = trim($controller_namespace, '\\');
        }
        $this->controller_factory = $controller_factory;
    }

    public function dispatch($controller, array $args = array())
    {
        if (is_callable($controller)) {
            return call_user_func_array($controller, $args);
        }

        list($class, $method) = explode('@', $controller);
        $resolved_controller = $this->controller_factory->make($class);

        return call_user_func_array(array($resolved_controller, $method), $args);
    }

}