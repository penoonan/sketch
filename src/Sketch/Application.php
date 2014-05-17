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

    /**
     * Register service providers on the application.
     * The "register" method of a service provider is a great
     * place to put your calls to $app->bind() and such
     *
     * @param ServiceProviderInterface $provider
     * @param array $options
     */
    public function register(ServiceProviderInterface $provider, array $options = array())
    {
        $this->providers[] = $provider;

        foreach ($options as $k => $v) {
            $this[$k] = $v;
        }

        $provider->register($this);
    }

}