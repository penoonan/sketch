<?php

namespace Sketch\Providers;

use Sketch\Application;
use Sketch\PlatesTemplateAdapter;
use Sketch\ServiceProviderInterface;

class PlatesServiceProvider implements ServiceProviderInterface {

    public function register(Application $app, array $values)
    {
        $engine = new \League\Plates\Engine( $values['template.dir'] );

        $engine->loadExtension(new \League\Plates\Extension\URI( $app['request'] ));

        if ( isset($values['template.asset_dir'])) {
            $engine->loadExtension(new \League\Plates\Extension\Asset( $values['template.asset_dir'], true));
        }

        $engine->loadExtension($app->make('Sketch\TemplateHelpers'));

        $view_folders = glob( $app['template.dir'] . '/*' , GLOB_ONLYDIR );

        foreach ($view_folders as $folder) {
            $engine->addFolder(basename($folder), $folder);
        }

        $app['template'] = $app->share(function() use ($engine)
        {
            return new PlatesTemplateAdapter($engine->makeTemplate());
        });
    }

} 