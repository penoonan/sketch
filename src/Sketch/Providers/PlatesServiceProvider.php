<?php

namespace Sketch\Providers;

use Sketch\Application;
use Sketch\ServiceProviderInterface;

class PlatesServiceProvider implements ServiceProviderInterface {

    public function register(Application $app)
    {
        $engine = new League\Plates\Engine( $app['template.dir'] );

        $engine->loadExtension(new \League\Plates\Extension\URI( $app['request'] ));

        if ( $app->offsetExists('template.asset_dir')) {
            $engine->loadExtension(new \League\Plates\Extension\Asset( $app['asset_dir'], true));
        }

        $engine->loadExtension($app->make('Sketch\TemplateHelpers'));

        $view_folders = glob( $app['template.dir'] . '/*' , GLOB_ONLYDIR );

        foreach ($view_folders as $folder) {
            $engine->addFolder(basename($folder), $folder);
        }

        $app['template'] = $engine->makeTemplate();
    }

} 