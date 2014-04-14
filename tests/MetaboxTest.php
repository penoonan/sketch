<?php

use Sketch\Metabox\BaseMetabox;
use Mockery as m;

class FooMetabox extends BaseMetabox {}

class BarMetabox extends BaseMetabox {
    protected
        $callback_controller = 'foobar';
}

class MetaboxTest extends PHPUnit_Framework_TestCase {

    protected
        $metabox, //the SUT
        $wp,
        $dispatcher;

    public function setUp()
    {
        $this->dispatcher = m::mock('Sketch\Dispatcher');
        $this->wp = m::mock('Sketch\WpApiWrapper');
        $this->wp->shouldReceive('add_action')->once();
        $this->metabox = new FooMetabox($this->wp, $this->dispatcher);
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
    public function test_it_throw_exception_if_controller_incorrectly_formatted()
    {
        $wp = m::mock('Sketch\WpApiWrapper');
        $wp->shouldReceive('add_action')->once();
        $bar_metabox = new BarMetabox($wp, $this->dispatcher);
        $bar_metabox->add();
    }

} 