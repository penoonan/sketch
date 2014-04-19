<?php

use Mockery as m;


class TaxonomyTest extends PHPUnit_Framework_TestCase {

    public function test_it_can_be_instantiated()
    {
        $tax = new FooTaxonomy($this->getWpWrapper());
        $this->assertInstanceOf('Sketch\Taxonomy\BaseTaxonomy', $tax);
    }

    /**
     * @expectedException Sketch\Taxonomy\SketchTaxonomyMissingTaxonomyException
     */
    public function test_it_throws_exception_if_no_taxonomy_name_given()
    {
        $tax = new FooTaxonomy($this->getWpWrapper());
        $tax->add();
    }

    /**
     * @expectedException Sketch\Taxonomy\SketchTaxonomyInvalidArgumentException
     */
    public function test_it_throws_exception_if_invalid_arg_is_given()
    {
        $tax = new BadArgTaxonomy($this->getWpWrapper());
        $tax->add();
    }

    /**
     * @expectedException Sketch\Taxonomy\SketchTaxonomyInvalidLabelException
     */
    public function test_it_throws_exception_if_invalid_label_is_given()
    {
        $tax = new BadLabelTaxonomy($this->getWpWrapper());
        $tax->add();
    }

    /**
     * @expectedException Sketch\Taxonomy\SketchTaxonomyInvalidRewriteException
     */
    public function test_it_throws_exception_if_invalid_rewrite_is_given()
    {
        $tax = new BadRewriteTaxonomy($this->getWpWrapper());
        $tax->add();
    }

    /**
     * @expectedException Sketch\Taxonomy\SketchTaxonomyInvalidCapabilityException
     */
    public function test_it_throws_exception_if_invalid_capability_is_given()
    {
        $tax = new BadCapTaxonomy($this->getWpWrapper());
        $tax->add();
    }

    /**
     * @expectedException Sketch\Taxonomy\SketchTaxonomyInvalidObjectTypeException
     */
    public function test_it_throws_exception_if_invalid_object_type_given()
    {
        $tax = new FooTaxonomy($this->getWpWrapper());
        $tax->setObjectType(array());
    }

    public function test_it_can_add_metabox()
    {
        $wp = $this->getWpWrapper();
        $tax = new FineTaxonomy($wp);
        $wp->shouldReceive('register_taxonomy')->with('fine', null, array('meta_box_cb' => array($tax, 'metaboxCallback')));
        $metabox = m::mock('Sketch\Metabox\MetaboxInterface');
        $metabox->shouldReceive('dispatch')->with('foo', 'bar');

        $tax->setMetabox($metabox);
        $tax->add();
        $tax->metaboxCallback('foo', 'bar');
    }

    public function test_metabox_callback_defaults_to_wp_if_no_metabox_present_non_hierarchical()
    {
        $wp = $this->getWpWrapper();
        $wp->shouldReceive('post_tags_meta_box')->with('foo', 'bar');
        $tax = new FineTaxonomy($wp);
        $tax->metaboxCallback('foo', 'bar');
    }

    public function test_metabox_callback_defaults_to_wp_if_no_metabox_hierarchical()
    {
        $wp = $this->getWpWrapper();
        $wp->shouldReceive('post_categories_meta_box')->with('foo', 'bar');
    }

    protected function getWpWrapper()
    {
        $wp = m::mock('Sketch\Wp\WpApiWrapper');
        $wp->shouldReceive('add_action')->once();
        return $wp;
    }

}

class FineTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy{
    protected $taxonomy = "fine";
}
class HierarchicalTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy{
    protected $taxonomy = "hierarchical";
    protected $args = array('hierarchical' => true);
}
class FooTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy {}
class BadArgTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy {
    protected $taxonomy = "bar";
    protected $args = array('foo' => 'bar');
}
class BadLabelTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy {
    protected $taxonomy = "bar";
    protected $labels = array('foo' => 'bar');
}
class BadRewriteTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy {
    protected $taxonomy = "bar";
    protected $rewrite = array('foo' => 'bar');
}
class BadCapTaxonomy extends \Sketch\Taxonomy\BaseTaxonomy {
    protected $taxonomy = "bar";
    protected $capabilities = array('foo' => 'bar');
}