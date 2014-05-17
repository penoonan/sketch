<?php

namespace Sketch;

interface ServiceProviderInterface {

    /**
     * @param \Sketch\Application $app
     * @param array $values
     * @return mixed
     */
    public function register(Application $app, array $values);

}