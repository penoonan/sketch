<?php

use Mockery as m;
use Sketch\QueryStringRouter as Router;
use Symfony\Component\HttpFoundation\Request as Request;

class QueryStringRouterTest extends PHPUnit_Framework_TestCase {

    protected $dispatcher;
    protected $request;
    protected $router;

    public function setUp()
    {
        $this->request = m::mock('Symfony\Component\HttpFoundation\Request');
        $this->dispatcher = m::mock('Sketch\Dispatcher');
        $this->router = new Router($this->dispatcher, $this->request);
    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }

    public function test_it_can_register_a_route()
    {
        $this->router->register('GET', array('foo' => 'bar'), 'baz@buz');
        $this->assertEquals($this->router->routes, array($this->getStandardRoute()));
    }

    public function test_it_can_register_a_route_based_on_a_query_string()
    {
        $this->router->register('GET', 'foo=bar', 'baz@buz');
        $this->assertEquals($this->router->routes, array($this->getStandardRoute()));
    }

    public function test_it_can_register_query_string_route_with_multiple_params()
    {
        $this->router->get('foo=bar&baz=buz', 'baz@buz');
        $this->assertEquals($this->router->routes, array(
            array(
                'method' => 'GET',
                'params' => array('foo' => 'bar', 'baz' => 'buz'),
                'controller' => 'baz@buz'
        )));
    }

    public function test_it_can_register_a_get_route()
    {
        $this->router->get(array('foo' => 'bar'), 'baz@buz');
        $this->assertEquals($this->router->routes, array($this->getStandardRoute()));
    }

    public function test_it_can_register_a_post_route()
    {
        $this->router->post(array('foo' => 'bar'), 'baz@buz');
        $this->assertEquals($this->router->routes, array($this->getStandardRoute('POST')));
    }

    public function test_it_can_register_both_get_and_post_with_any()
    {
        $this->router->any(array('foo' => 'bar'), 'baz@buz');
        $this->assertEquals($this->router->routes, array($this->getStandardRoute(), $this->getStandardRoute('POST')));

    }

    public function test_it_resolves_a_simple_route()
    {
        $request = $this->makeAFakeRequest('foo=bar', 'GET');
        $router = new Router($this->dispatcher, $request);
        $router->get(array('foo' => 'bar'), 'baz@buz');

        $this->dispatcher->shouldReceive('dispatch')->with($request, 'baz@buz')->once()->andReturn('success');
        $result = $router->resolve();
        $this->assertSame($result, 'success');
    }

    public function test_it_resolves_route_with_int()
    {
        $request = $this->makeAFakeRequest('foo=1', 'GET');
        $router = new Router($this->dispatcher, $request);
        $router->get(array('foo' => '{int}'), 'baz@buz');

        $this->dispatcher->shouldReceive('dispatch')->with($request, 'baz@buz')->once()->andReturn('success');
        $result = $router->resolve();
        $this->assertSame($result, 'success');
    }

    public function test_it_resolves_query_string_route_with_int()
    {
        $request = $this->makeAFakeRequest('foo=1', 'GET');
        $router = new Router($this->dispatcher, $request);
        $router->get('foo={int}', 'baz@buz');

        $this->dispatcher->shouldReceive('dispatch')->with($request, 'baz@buz')->once()->andReturn('success');
        $result = $router->resolve();
        $this->assertSame($result, 'success');
    }

    public function test_it_resolves_to_correct_method_when_multiple_are_present()
    {
        $request = $this->makeAFakeRequest('foo=bar', 'GET');
        $router = new Router($this->dispatcher, $request);
        $router->get(array('foo' => 'bar'), 'baz@get');
        $router->post(array('foo' => 'bar'), 'baz@post');

        $this->dispatcher->shouldReceive('dispatch')->with($request, 'baz@get')->once()->andReturn('success');
        $result = $router->resolve();
        $this->assertSame($result, 'success');
    }

    public function test_it_skips_too_specific_route()
    {
        $request = $this->makeAFakeRequest('foo=bar', 'GET');
        $router = new Router($this->dispatcher, $request);
        $router->get(array('foo' => 'bar', 'baz' => 'buz'), 'baz@most_specific');
        $router->get(array('foo' => 'bar'), 'baz@least_specific');

        $this->dispatcher->shouldReceive('dispatch')->with($request, 'baz@least_specific')->once()->andReturn('success');
        $result = $router->resolve();
        $this->assertSame($result, 'success');
    }

    public function test_it_selects_most_specific_route()
    {
        $request = $this->makeAFakeRequest('foo=bar&baz=buz', 'GET');
        $router = new Router($this->dispatcher, $request);
        $router->get(array('foo' => 'bar', 'baz' => 'buz'), 'baz@most_specific');
        $router->get(array('foo' => 'bar'), 'baz@least_specific');

        $this->dispatcher->shouldReceive('dispatch')->with($request, 'baz@most_specific')->once()->andReturn('success');
        $result = $router->resolve();
        $this->assertSame($result, 'success');
    }

    protected function getStandardRoute($method = 'GET')
    {
        return array(
            'method' => $method,
            'params' => array('foo' => 'bar'),
            'controller' => 'baz@buz'
        );
    }

    protected function makeAFakeRequest($query_string, $method)
    {
        return Request::create('abc.com?' . $query_string, $method);
    }

} 