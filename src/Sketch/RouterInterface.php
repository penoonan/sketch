<?php

namespace Sketch;

interface RouterInterface {
    public function resolve();
    public function post($params, $controller);
    public function get($params, $controller);
    public function any($params, $controller);
    public function register($method, $params, $controller);
} 