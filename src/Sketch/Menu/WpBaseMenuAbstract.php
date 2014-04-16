<?php

namespace Sketch\Menu;

use ArcWp\ArcWpApiWrapper as WP;
use Symfony\Component\HttpFoundation\Request;

abstract class WpBaseMenuAbstract {

    /**
     * @var WP
     */
    protected $wp;

    /**
     * @var RouterInterface
     */
    protected $router;


   public
        $page_title,
        $menu_title,
        $capability,
        $menu_slug,
        $icon_url = '',
        $position = null,
        $parent_slug;

    public function __construct(WpApiWrapper $wp, RouterInterface $router)
    {
        $this->wp = $wp;
        $this->router = $router;

        $this->wp->add_action('admin_menu', array($this, 'addMenu'));
        $this->addActions();
    }

    /*
     * This is where you would register any actions related
     * to the menu, enqueueing javascript or css, etc
     * Use "\Sketch\UriTrait" in any menu for easier access to public assets
     */
    protected function addActions(){}

}