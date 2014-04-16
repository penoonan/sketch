<?php

namespace Sketch\CustomPostType;

use Sketch\Wp\WpApiWrapper;
use Sketch\Metabox\MetaboxInterface as Metabox;

abstract class BaseCustomPostType implements CustomPostTypeInterface {

    protected
        $post_type = "custom_post_type",

        //args
        $description,
        $public,
        $exclude_from_search,
        $publicly_queryable,
        $show_ui,
        $show_in_nav_menus,
        $show_in_menu,
        $show_in_admin_bar,
        $menu_position,
        $menu_icon,
        $capability_type,
        $capabilities,
        $map_meta_cap,
        $heirarchical,
        $supports,
        $register_meta_box_cb,
        $taxonomies,
        $has_archive,
        $permalink_epmask,
        $query_var,
        $can_export,
        $label,
        
        //Labels
        $labels,
        $name,
        $singular_name,
        $menu_name,
        $name_admin_bar,
        $all_items,
        $add_new,
        $add_new_item,
        $edit_item,
        $new_item,
        $view_item,
        $search_items,
        $not_found,
        $not_found_in_trash,
        $parent_item_colon,

        //Rewrite
        $slug,
        $with_front,
        $feeds,
        $pages,
        $ep_mask
    ;
    
    private
        $arg_vals = array(
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
        ),

        $label_vals = array(
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

        $rewrite_vals = array(
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

    public function addMetabox(Metabox $metabox)
    {
        $metabox->setPostType($this->post_type);
        array_push($this->metaboxes, $metabox);
        $this->register_meta_box_cb = array($this, 'metaboxCallback');

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
        foreach ($this->arg_vals as $arg) {
            if ($this->{$arg}) {
                $args[$arg] = $this->{$arg};
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
        $labels = array();
        foreach ($this->label_vals as $label) {
            if ($this->{$label}) {
                $labels[$label] = $this->{$label};
            }
        }
        return count($labels > 0) ? $labels : null;
    }

    private function getRewrite()
    {
        $vals = array();
        foreach ($this->rewrite_vals as $val) {
            if ($this->{$val}) {
                $vals[$val] = $this->{$val};
            }
        }
        return count($vals) > 0 ? $vals : null;
    }

} 