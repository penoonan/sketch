<?php

namespace Sketch;

class ControllerFactory implements ControllerFactoryInterface {

    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Instantiate the controller class, set its request and template
     * properties, and return it to the dispatcher, where the controller method
     * will be called with any given parameters.
     *
     * @see \Sketch\ControllerDispatcher::dispatch();
     * @param string $class
     * @throws \InvalidArgumentException
     * @return Object
     */
    public function make($class)
    {
        $resolved = $this->app->make($class);

        if (!$resolved instanceof BaseController) {
            Throw new \InvalidArgumentException('By default, controllers must extend \Sketch\BaseController - it looks like "'. get_class($resolved) .'" does not. To use a different type of controller, you must register a custom controller factory class. See the docs or feel free to ask for help doing this.');
        }

        $resolved->setTemplate($this->app['template']);
        $resolved->setRequest($this->app['request']);

        return $resolved;
    }


} 