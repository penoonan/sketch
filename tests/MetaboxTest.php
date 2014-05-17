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
        $this->dispatcher = m::mock('Sketch\ControllerDispatcher');
        $this->wp = $this->get_a_wrapper();
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
        $wp = $this->get_a_wrapper();
        $bar_metabox = new BarMetabox($wp, $this->dispatcher);
        $bar_metabox->add();
    }

    public function test_it_can_dispatch_request()
    {
        $wp = $this->get_a_wrapper();
        $metabox = new BazMetabox($wp, $this->dispatcher);
        $this->dispatcher->shouldReceive('dispatch')->with('foo@bar', array('post', 'metabox'))->once();
        $metabox->dispatch('post', 'metabox');
    }

    public function test_it_can_dispatch_with_callback_args()
    {
        $wp = $this->get_a_wrapper();
        $metabox = new BuzMetabox($wp, $this->dispatcher);
        $this->dispatcher->shouldReceive('dispatch')->with('foo@bar', array('post', 'metabox', array('foo' => 'bar', 'baz' => 'buz')))->once();
        $metabox->dispatch('post', 'metabox');
    }

    /**
     * @expectedException \Sketch\Metabox\SketchInvalidPostTypeException
     */
    public function test_call_to_add_throw_exception_if_post_type_not_set()
    {
        $wp = $this->get_a_wrapper();
        $metabox = new BuzMetabox($wp, $this->dispatcher);
        $metabox->setPostType(null);
    }

    protected function get_a_wrapper()
    {
        return m::mock('Sketch\Wp\WpApiWrapper');
    }

} 