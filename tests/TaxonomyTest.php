<?php

use Mockery as m;

class FooTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy {}

class TaxonomyTest extends PHPUnit_Framework_TestCase {

    public function SetUp()
    {
        $this->wp = $this->getWpWrapper();
    }

    protected function getWpWrapper()
    {
        $wp = m::mock('Sketch\Wp\WpApiWrapper');
        $wp->shouldReceive('add_action')->once();
        return $wp;
    }

    /**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testBasicExample()
	{
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
	}
}
