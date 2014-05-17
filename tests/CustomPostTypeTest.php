<?php
use Sketch\CustomPostType\BaseCustomPostType;
use Mockery as m;

class CustomPostTypeTest extends PHPUnit_Framework_TestCase {

    protected $post_type;

    protected $metabox;

    protected $taxonomy;

    protected $wp;

    public function setUp()
    {
        $this->wp = m::mock('Sketch\Wp\WpApiWrapper');
        $this->wp->shouldReceive('add_action')->once();
        $this->post_type = new CustomPostType($this->wp);
    }

    public function test_it_can_register_default_args()
    {
        $this->wp->shouldReceive('register_post_type')->with('custom_post_type', null)->once();
        $this->post_type->register();
    }

    public function test_it_can_register_custom_args()
    {
        $wp = m::mock('Sketch\Wp\WpApiWrapper');
        $wp->shouldReceive('add_action')->once();
        $wp->shouldReceive('register_post_type')->with('foo', array('description' => 'foo', 'supports' => 'foo', 'labels' => array('name' => 'foo'), 'rewrite' => array('slug' => 'foo')))->once();
        $post_type = new FooPostType($wp);
        $post_type->register();
    }

    public function test_it_can_add_a_metabox()
    {
        $this->metabox = m::mock('Sketch\Metabox\MetaboxInterface');
        $this->metabox->shouldReceive('setPostType')->with('custom_post_type')->once();
        $this->wp->shouldReceive('register_post_type')->with('custom_post_type', array('register_meta_box_cb' => array($this->post_type, 'metaboxCallback')));
        $this->metabox->shouldReceive('add')->with('post')->once();
        $this->post_type->addMetabox($this->metabox);
        $this->post_type->register();
        $this->post_type->metaboxCallback('post');
    }

    public function test_it_can_add_a_taxonomy()
    {
        $taxonomy = m::mock('\Sketch\Taxonomy\TaxonomyInterface');
        $taxonomy->shouldReceive('getName')->once()->andReturn('foo');
        $taxonomy->shouldReceive('addObjectType')->once();
        $this->wp->shouldReceive('register_post_type')->with('custom_post_type', array('taxonomies' => array('foo')));
        $this->post_type->addTaxonomy($taxonomy);
        $this->post_type->register();
    }

    public function test_it_can_add_multiple_taxonomies()
    {
        $taxonomy = m::mock('\Sketch\Taxonomy\TaxonomyInterface');
        $taxonomy->shouldReceive('getName')->once()->andReturn('foo');
        $taxonomy->shouldReceive('addObjectType')->once();
        $taxonomy2 = m::mock('\Sketch\Taxonomy\TaxonomyInterface');
        $taxonomy2->shouldReceive('getName')->once()->andReturn('bar');
        $taxonomy2->shouldReceive('addObjectType')->once();
        $this->wp->shouldReceive('register_post_type')->with('custom_post_type', array('taxonomies' => array('foo', 'bar')));
        $this->post_type
            ->addTaxonomy($taxonomy)
            ->addTaxonomy($taxonomy2)
            ->register();
    }
}

class CustomPostType extends BaseCustomPostType {}
class FooPostType extends BaseCustomPostType{
    protected
      $post_type = 'foo',
      $args = array(
      'description' => 'foo',
      'supports' => 'foo',
    ),
      $labels = array(
      'name' => 'foo'
    ),
      $rewrite = array(
      'slug' => 'foo'
    );
}