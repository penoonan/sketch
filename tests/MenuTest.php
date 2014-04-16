<?php

use Sketch\Menu\WpMenuAbstract;
use Mockery as m;

class FooMenu extends WpMenuAbstract {}
class BarMenu extends WpMenuAbstract { protected function addActions() { $this->wp->foo('bar'); }}

class MenuTest extends PHPUnit_Framework_TestCase {

    protected $menu;
    protected $menu_with_actions;
    protected $wp;
    protected $router;

    public function setUp()
    {
        $this->wp = m::mock('Sketch\Wp\WpApiWrapper');
        $this->router = m::mock('Sketch\QueryStringRouter');
        $this->wp->shouldReceive('add_action')->once();
        $this->menu = new FooMenu($this->wp, $this->router);
    }

    public function test_it_can_be_constructed()
    {
        $this->assertInstanceOf('Sketch\Menu\WpBaseMenuAbstract', $this->menu);
    }

    public function test_it_can_add_menu()
    {
        $this->wp->shouldReceive('add_menu_page')->with('Menu', 'Menu Title', 'edit_themes', 'menu_slug', array($this->router, 'resolve'), '', null);
        $this->menu->addMenu();
    }

    public function test_it_runs_add_actions()
    {
        $this->wp->shouldReceive('add_action')->once();
        $this->wp->shouldReceive('foo')->with('bar')->once();
        $this->menu_with_actions = new BarMenu($this->wp, $this->router);
    }

} 