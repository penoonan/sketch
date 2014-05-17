<?php

namespace Sketch;

interface TemplateInterface {

    /**
     * @param string $template
     * @param array $data
     * @return mixed
     */
    public function render($template, array $data);
} 