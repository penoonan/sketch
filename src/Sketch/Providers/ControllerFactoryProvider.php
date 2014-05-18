<?php

namespace Sketch\Providers;

use Sketch\Application;
use Sketch\ServiceProviderInterface;

class ControllerFactoryProvider implements ServiceProviderInterface {

    /**
     * @param \Sketch\Application $app
     * @param array $values
     * @return mixed
     */
    public function register(Application $app, array $values)
    {
        $app['controller_factory'] = $app->share(function() use ($app) {
            return $app->make('Sketch\ControllerFactory');
        });
    }


} 