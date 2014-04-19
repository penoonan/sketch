<?php

namespace Sketch\Taxonomy;

use Sketch\Metabox\MetaboxInterface;
use Sketch\Wp\WpApiWrapper;

class SketchTaxonomyInvalidObjectTypeException extends \InvalidArgumentException {}
class SketchTaxonomyMissingTaxonomyException extends \InvalidArgumentException {}
class SketchTaxonomyInvalidArgumentException extends \InvalidArgumentException {}
class SketchTaxonomyInvalidLabelException extends \InvalidArgumentException {}
class SketchTaxonomyInvalidRewriteException extends \InvalidArgumentException {}
class SketchTaxonomyInvalidCapabilityException extends \InvalidArgumentException {}

class BaseTaxonomy implements TaxonomyInterface {

    protected
        $taxonomy = null,
        $object_type = null,
        $args = array(),
        $labels = array(),
        $rewrite = array(),
        $capabilities = array()
    ;
        
    private
        $arg_keys = array(
            'label',
            'public',
            'show_ui',
            'show_in_nav_menus',
            'show_tagcloud',
            'meta_box_cb',
            'show_admin_column',
            'hierarchical',
            'update_count_callback',
            'query_var',
            'sort',
        ),
        $label_keys = array(
            'name',
            'singular_name',
            'menu_name',
            'all_items',
            'edit_item',
            'view_item',
            'update_item',
            'add_new_item',
            'new_item_name',
            'parent_item',
            'parent_item_colon',
            'search_items',
            'popular_items',
            'separate_items_with_commas',
            'add_or_remove_items',
            'choose_from_most_used',
            'not_found'
        ),
        $rewrite_keys = array(
            'slug',
            'with_front',
            'hierarchical',
            'ep_mask',
        ),
        $capability_keys = array(
            'manage_terms',
            'edit_terms',
            'delete_terms',
            'assign_terms',
        )
    ;

    /**
     * @var \Sketch\Wp\WpApiWrapper
     */
    private $wp;

    /**
     * @var \Sketch\Metabox\MetaboxInterface|bool
     */
    private $metabox = false;

    public function __construct(WpApiWrapper $wp)
    {
        $this->wp = $wp;
        $this->wp->add_action('init', array($this, 'add'));
    }

    public function getName()
    {
        return $this->taxonomy;
    }

    public function add()
    {
        $this->validate();
        $this->wp->register_taxonomy($this->taxonomy, $this->object_type, $this->getArgs());
    }

    public function addObjectType($object_type)
    {
        if (!$object_type || !is_string($object_type)) {
            Throw new SketchTaxonomyInvalidObjectTypeException('Invalid $object_type ('.print_r($object_type, true).') passed to taxonomy ' . get_class($this) . '. Object type must be a string or array.');
        }

        $object_type = strtolower(str_replace(' ', '_', $object_type));

        if (is_array($this->object_type)) {
            array_push($this->object_type, $object_type);
        } elseif (is_string($this->object_type)) {
            $this->object_type = array($this->object_type, $object_type);
        } else {
            $this->object_type = $object_type;
        }
    }

    public function metaboxCallback($post, $box)
    {
        if ($this->metabox) {
            $this->metabox->dispatch($post, $box);
        } elseif (isset($this->args['hierarchical']) && $this->args['hierarchical']) {
            $this->wp->post_categories_meta_box($post, $box);
        } else {
            $this->wp->post_tags_meta_box($post, $box);
        }
    }

    public function setMetabox(MetaboxInterface $metabox)
    {
        $this->args['meta_box_cb'] = array($this, 'metaboxCallback');
        $this->metabox = $metabox;
        return $this;
    }

    public function updateCountCallback() {}

    private function getArgs()
    {
        $args = array();
        foreach ($this->args as $k => $v) {
            if (!in_array($k, $this->arg_keys)) {
                Throw new SketchTaxonomyInvalidArgumentException('Specified argument key "'.$k . ' => ' . $v .'" in taxonomy ' . get_class($this) . ' is not valid. Valid argument keys are: ' . print_r($this->arg_keys, true) . '.');
            }
            if ($k === 'update_count_callback' && !$v === '_update_generic_term_count') {
                $args[$k] = array($this, 'updateCountCallback');
            } else {
                $args[$k] = $v;
            }
        }
        if ($labels = $this->getLabels()) {
            $args['labels'] = $labels;
        }
        if ($rewrite = $this->getRewrite()) {
            $args['rewrite'] = $rewrite;
        }
        if ($capabilities = $this->getCapabilities()) {
            $args['capabilities'] = $capabilities;
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
                Throw new SketchTaxonomyInvalidLabelException('Specified label key "' . $k . '" in taxonomy ' . get_class($this) . ' is not valid. Valid label keys are: ' . print_r($this->label_keys, true) .'.');
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
                Throw new SketchTaxonomyInvalidRewriteException('Specified rewrite key "' . $k . '" in taxonomy ' . get_class($this) . ' is not valid. Valid rewrite keys are: ' . print_r($this->label_keys, true) .'.');
            }
        }
        return count($vals) > 0 ? $vals : null;
    }

    private function getCapabilities()
    {
        $vals = array();
        foreach ($this->capabilities as $k => $v) {
            if (in_array($k, $this->capability_keys)) {
                $vals[$k] = $v;
            } else {
                Throw new SketchTaxonomyInvalidCapabilityException('Specified capability key "' . $k . '" in taxonomy ' . get_class($this) . ' is not valid. Valid capability keys are: ' . print_r($this->label_keys, true) .'.');
            }
        }
        return count($vals) > 0 ? $vals : null;
    }


    protected function validate()
    {
        if (!$this->taxonomy) {
            Throw new SketchTaxonomyMissingTaxonomyException('No $taxonomy name as been given for taxonomy '.get_class($this).'.');
        }
    }
} 