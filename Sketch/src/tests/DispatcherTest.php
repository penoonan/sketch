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

    public function setUp()
    {
        ini_set('display_errors', -1); error_reporting(E_ALL);
        $this->app = m::mock('Illuminate\Container\Container');
        $this->template = m::mock('League\Plates\Template');
        $this->controller = m::mock('FooController');
        $this->dispatcher = new Dispatcher($this->app, $this->template);
    }

    public function test_it_can_dispatch_a_request()
    {
        $request = Request::create('abc.com', 'GET');
        $this->app->shouldReceive('make')->with('\FooController')->once()->andReturn($this->controller);
        $this->controller->shouldReceive('setTemplate')->with($this->template)->once();
        $this->controller->shouldReceive('setRequest')->with($request)->once();
        $this->controller->shouldReceive('foo')->once()->andReturn('foo');

        $result = $this->dispatcher->dispatch($request, 'foo@foo');

        $this->assertSame($result, 'foo');
    }

} 