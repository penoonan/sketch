<?php

use Sketch\Dispatcher;
use Symfony\Component\HttpFoundation\Request;
use Mockery as m;

class FooController{}

class DispatcherTest extends PHPUnit_Framework_TestCase{

    protected $dispatcher;

    protected $app;
    protected $template;
    protected $controller;
    protected $request;

    public function setUp()
    {
        ini_set('display_errors', -1); error_reporting(E_ALL);
        $this->app = m::mock('Illuminate\Container\Container');
        $this->template = m::mock('League\Plates\Template');
        $this->controller = m::mock('FooController');
        $this->request = m::mock('Symfony\Component\HttpFoundation\Request');
        $this->dispatcher = new Dispatcher($this->app);
    }

    public function test_it_can_dispatch_a_request()
    {
        $this->app->shouldReceive('make')->with('\FooController')->once()->andReturn($this->controller);
        $this->app->shouldReceive('offsetGet')->with('template')->once()->andReturn($this->template);
        $this->app->shouldReceive('offsetGet')->with('request')->once()->andReturn($this->request);
        $this->controller->shouldReceive('setTemplate')->with($this->template)->once();
        $this->controller->shouldReceive('setRequest')->with($this->request)->once();
        $this->controller->shouldReceive('foo')->once()->andReturn('foo');

        $result = $this->dispatcher->dispatch('foo@foo');

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
        $request = Request::create('abc.com', 'GET');
        $callback = function() use($request) {
            return $request->getMethod();
        };

        $result = $this->dispatcher->dispatch($callback);

        $this->assertSame($result, 'GET');
    }

} 