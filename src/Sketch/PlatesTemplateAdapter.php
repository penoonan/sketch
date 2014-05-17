<?php

namespace Sketch;

use League\Plates\Template;

class PlatesTemplateAdapter implements TemplateInterface {

    /**
     * @var \League\Plates\Template
     */
    protected $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param string $template
     * @param array $data
     * @return mixed
     */
    public function render($template, array $data = array())
    {
        return $this->template->render($template, $data);
    }

} 