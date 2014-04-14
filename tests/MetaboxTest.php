<?php

use Sketch\Metabox\BaseMetabox;
use Mockery as m;

class FooMetabox extends BaseMetabox {}
class BarMetabox extends BaseMetabox { protected $callback_controller = 'foobar'; }
class BazMetabox extends BaseMetabox { protected $callback_controller = 'foo@bar';}
class BuzMetabox extends BaseMetabox { protected $callback_controller = 'foo@bar', $callback_args = array('foo' => 'bar', 'baz' => 'buz'); }

class MetaboxTest extends PHPUnit_Framework_TestCase {

    protected
        $metabox, //the SUT
        $wp,
        $dispatcher;

    public function setUp()
    {
        $this->dispatcher = m::mock('Sketch\Dispatcher');
        $this->wp = m::mock('Sketch\WpApiWrapper');
        $this->metabox = new FooMetabox($this->wp, $this->dispatcher);
    }

    public function test_it_can_manually_add_action()
    {
        $this->wp->shouldReceive('add_action')->with('add_meta_boxes', array($this->metabox, 'add'))->once();
        $this->metabox->manuallyAddAction();
    }

    /**
     * @expectedException \Sketch\Metabox\SketchMetaboxMissingControllerException
     */
    public function test_it_throws_exception_if_controller_not_specificied()
    {
        $this->metabox->add();
    }

    /**
     * @expectedException \Sketch\Metabox\SketchMetaboxInvalidControllerException
     */
    public function test_it_throws_exception_if_controller_incorrectly_formatted()
    {
        $wp = m::mock('Sketch\WpApiWrapper');
        $bar_metabox = new BarMetabox($wp, $this->dispatcher);
        $bar_metabox->add();
    }

    public function test_it_can_dispatch_request()
    {
        $wp = m::mock('Sketch\WpApiWrapper');
        $metabox = new BazMetabox($wp, $this->dispatcher);
        $this->dispatcher->shouldReceive('dispatch')->with('foo@bar', array('post', 'metabox'))->once();
        $metabox->dispatch('post', 'metabox');
    }

    public function test_it_can_dispatch_with_callback_args()
    {
        $wp = m::mock('Sketch\WpApiWrapper');
        $metabox = new BuzMetabox($wp, $this->dispatcher);
        $this->dispatcher->shouldReceive('dispatch')->with('foo@bar', array('post', 'metabox', array('foo' => 'bar', 'baz' => 'buz')))->once();
        $metabox->dispatch('post', 'metabox');
    }

} 