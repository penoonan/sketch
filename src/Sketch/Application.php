<?php

namespace Sketch;

use Illuminate\Container\Container;

class Application extends Container {

    /**
     * All registered service providers
     *
     * @var array
     */
    protected $providers = array();

    public function __construct()
    {
        $this->instance('request', Request::createFromGlobals());
        $this->instance('Illuminate\Container\Container', $this);

        $this['router'] = $this->share(function() use ($this) {
            return new \Sketch\QueryStringRouter (
                $this->make('Sketch\Dispatcher'),
                $this['request']
            );
        });
    }

    public function register(ServiceProviderInterface $provider)
    {

    }
}