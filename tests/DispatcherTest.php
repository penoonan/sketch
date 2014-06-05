<?php

use Sketch\ControllerDispatcher;
use Mockery as m;

class DispatcherTest extends PHPUnit_Framework_TestCase{

    protected $dispatcher;
    protected $controller_factory;
    protected $template;
    protected $controller;
    protected $request;

    public function setUp()
    {
        $this->controller_factory = m::mock('Sketch\ControllerFactoryInterface');
        $this->controller = m::mock('FooController');
        $this->dispatcher = new ControllerDispatcher($this->controller_factory);
    }

    public function test_it_can_dispatch_a_request()
    {
        $this->controller_factory->shouldReceive('make')->once()->with('\FooController')->andReturn($this->controller);
        $this->controller->shouldReceive('foo')->once()->andReturn('foo');

        $result = $this->dispatcher->dispatch('FooController@foo');

        $this->assertSame($result, 'foo');
    }

    public function test_it_can_dispatch_a_callback()
    {
        $callback = function() {
            return 'foo';
        };

        $result = $this->dispatcher->dispatch($callback);

        $this->assertSame($result, 'foo');
    }

    public function test_it_can_dispatch_a_callback_using_use()
    {
        $foo = 'bar';
        $callback = function() use($foo) {
            return $foo;
        };

        $result = $this->dispatcher->dispatch($callback);

        $this->assertSame($result, 'bar');
    }

} 