<?php

use Sketch\WpSubmenuAbstract;
use Mockery as m;

class BazMenu extends WpSubmenuAbstract {}
class BuzMenu extends WpSubmenuAbstract { protected function addActions() { $this->wp->foo('bar'); }}

class SubmenuTest extends PHPUnit_Framework_TestCase {

    protected $menu;
    protected $menu_with_actions;
    protected $wp;
    protected $router;

    public function setUp()
    {
//        ini_set('display_errors', -1); error_reporting(E_ALL);
        $this->wp = m::mock('Sketch\WpApiWrapper');
        $this->router = m::mock('Sketch\QueryStringRouter');
        $this->wp->shouldReceive('add_action')->once();
        $this->menu = new BazMenu($this->wp, $this->router);
    }

    public function test_it_can_be_constructed()
    {
        $this->assertInstanceOf('Sketch\WpBaseMenuAbstract', $this->menu);
    }

    public function test_it_can_add_menu()
    {
        $this->wp->shouldReceive('add_submenu_page')->once();
        $this->menu->addMenu();
    }

    public function test_it_runs_add_actions()
    {
        $this->wp->shouldReceive('add_action')->once();
        $this->wp->shouldReceive('foo')->with('bar')->once();
        $this->menu_with_actions = new BuzMenu($this->wp, $this->router);
    }

} 