<?php

namespace Sketch;

interface ControllerFactoryInterface {

    /**
     * @param string $class
     * @return Object
     */
    public function make($class);

} 