<?php

namespace Sketch\Wp;

class WpQueryFactory {

  public function make(array $args)
  {
      return new \WP_Query($args);
  }

} 