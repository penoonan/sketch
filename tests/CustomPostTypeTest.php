<?php
use Sketch\CustomPostType\BaseCustomPostType;
use Mockery as m;

class CustomPostType extends BaseCustomPostType {}
class FooPostType extends BaseCustomPostType{
    protected
        $post_type = 'foo',
        $description = 'foo',
        $supports = 'foo',
        $name = 'foo',
        $slug = 'foo';
}

class CustomPostTypeTest extends PHPUnit_Framework_TestCase {

    protected $post_type;

    protected $metabox;

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
        $this->metabox = m::mock('Sketch\Metabox\CustomPostMetaboxInterface');
        $this->metabox->shouldReceive('setPostType')->with('custom_post_type')->once();
        $this->wp->shouldReceive('register_post_type')->with('custom_post_type', array('register_meta_box_cb' => array($this->post_type, 'metaboxCallback')));
        $this->metabox->shouldReceive('add')->with('post')->once();
        $this->post_type->addMetabox($this->metabox);
        $this->post_type->register();
        $this->post_type->metaboxCallback('post');
    }
}