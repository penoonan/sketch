<?php

namespace Sketch\CustomPostType;
use Sketch\Metabox\MetaboxInterface as Metabox;

interface CustomPostTypeInterface {
    public function register();
    public function addMetabox(Metabox $metabox);
    public function metaboxCallback($post);
} 