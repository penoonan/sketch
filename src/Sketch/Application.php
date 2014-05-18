<?php

namespace Sketch;

use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Request;

class Application extends Container {

    public function __construct(array $values = array())
    {
        $app = $this;

        $app->instance('request', Request::createFromGlobals());
        $app->instance('Sketch\Application', $app);

        $app->bind('Sketch\ControllerDispatcher', $app->share(function() use ($app) {
            return new ControllerDispatcher(
                $app['controller_factory'],
                $app['controller_namespace']
            );
        }));

        $app['controller_namespace'] = null;

        $app['router'] = $app->share(function() use ($app) {
              return new QueryStringRouter (
                $app->make('Sketch\ControllerDispatcher'),
                $app['request']
              );
          });

        $app->bind('Sketch\RouterInterface', function() use($app) {return $app['router'];});

        foreach ($values as $k => $v) {
            $app[$k] = $v;
        }
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
        $provider->register($this, $options);
    }

}