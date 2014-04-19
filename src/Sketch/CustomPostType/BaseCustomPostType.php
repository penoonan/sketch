<?php

namespace Sketch\CustomPostType;

use Sketch\Wp\WpApiWrapper;
use Sketch\Metabox\MetaboxInterface as Metabox;
use Sketch\Taxonomy\TaxonomyInterface as Taxonomy;

class SketchPostTypeInvalidArgumentException extends \InvalidArgumentException{}
class SketchPostTypeInvalidLabelKeyException extends \InvalidArgumentException{}
class SketchPostTypeInvalidRewriteKeyException extends \InvalidArgumentException{}

abstract class BaseCustomPostType implements CustomPostTypeInterface {

    protected
        $post_type = "custom_post_type",
        $args = array(),
        $labels = array(),
        $rewrite = array()
    ;
    
    private
        $arg_keys = array(
            'description',
            'public',
            'exclude_from_search',
            'publicly_queryable',
            'show_ui',
            'show_in_nav_menus',
            'show_in_menu',
            'show_in_admin_bar',
            'menu_position',
            'menu_icon',
            'capability_type',
            'capabilities',
            'map_meta_cap',
            'heirarchical',
            'supports',
            'register_meta_box_cb',
            'taxonomies',
            'has_archive',
            'permalink_epmask',
            'query_var',
            'can_export',
            'label',
            'labels',
            'rewrite'
        ),

        $label_keys = array(
            'name',
            'singular_name',
            'menu_name',
            'name_admin_bar',
            'all_items',
            'add_new',
            'add_new_item',
            'edit_item',
            'new_item',
            'view_item',
            'search_items',
            'not_found',
            'not_found_in_trash',
            'parent_item_colon'
        ),

        $rewrite_keys = array(
            'slug',
            'with_front',
            'feeds',
            'pages',
            'ep_mask'
        )
    ;
    /**
     * @var
     */
    private $wp;

    protected $metaboxes = array();

    public function __construct(WpApiWrapper $wp)
    {
        $this->wp = $wp;
        $this->wp->add_action('init', array($this, 'register'));
    }

    public function register()
    {
        $this->wp->register_post_type($this->post_type, $this->getArgs());
    }

    public function addTaxonomy(Taxonomy $taxonomy)
    {
        if (isset($this->args['taxonomies'])) {
            array_push($this->args['taxonomies'], $taxonomy->getName());
        } else {
            $this->args['taxonomies'] = array($taxonomy->getName());
        }
        $taxonomy->addObjectType($this->post_type);
        return $this;
    }

    public function addMetabox(Metabox $metabox)
    {
        $metabox->setPostType($this->post_type);
        array_push($this->metaboxes, $metabox);
        $this->args['register_meta_box_cb'] = array($this, 'metaboxCallback');

        return $this;
    }

    public function metaboxCallback($post)
    {
        foreach ($this->metaboxes as $metabox) {
            $metabox->add($post);
        }
    }

    private function getArgs()
    {
        $args = array();

        foreach ($this->args as $k => $v) {
            if (!in_array($k, $this->arg_keys)) {
                Throw new SketchPostTypeInvalidArgumentException('Specified argument key "'.$k . ' => ' . $v .'" specified in custom post type ' . get_class($this) . ' is not valid. Valid argument keys are: ' . print_r($this->arg_keys) . '.');
            } else {
                $args[$k] = $v;
            }
        }
        if ($labels = $this->getLabels()) {
            $args['labels'] = $labels;
        }
        if ($rewrite =  $this->getRewrite()) {
            $args['rewrite'] = $rewrite;
        }
        return count($args) > 0 ? $args : null;
    }

    private function getLabels()
    {
        $vals = array();
        foreach ($this->labels as $k => $v) {
            if (in_array($k, $this->label_keys)) {
                $vals[$k] = $v;
            } else {
                Throw new SketchPostTypeInvalidLabelKeyException('Specified label key "' . $k . '" in post type ' . get_class($this) . ' is not valid. Valid label keys are: ' . print_r($this->label_keys) .'.');
            }
        }
        return count($vals > 0) ? $vals : null;
    }

    private function getRewrite()
    {
        $vals = array();
        foreach ($this->rewrite as $k => $v) {
            if (in_array($k, $this->rewrite_keys)) {
                $vals[$k] = $v;
            } else {
                Throw new SketchPostTypeInvalidRewriteKeyException('Specified rewrite key "' . $k . '" in post type ' . get_class($this) . ' is not valid. Valid rewrite keys are: ' . print_r($this->label_keys) .'.');
            }
        }
        return count($vals) > 0 ? $vals : null;
    }



} 