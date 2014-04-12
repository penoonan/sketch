<?php

namespace Sketch;

abstract class WpSubmenuAbstract extends WpBaseMenuAbstract {

    public
        $parent_slug = 'menu_slug',
        $page_title = 'Submenu',
        $menu_title = "Submenu Title",
        $capability = "edit_themes",
        $menu_slug  = "submenu_slug";

    public function addMenu()
    {
        $this->wp->add_submenu_page(
          $this->parent_slug,
          $this->page_title,
          $this->menu_title,
          $this->capability,
          $this->menu_slug,
          array($this->router, 'resolve')
        );
    }

} 