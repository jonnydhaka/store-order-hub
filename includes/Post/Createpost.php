<?php

namespace Wppool\Orderhub\Post;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;

/**
 * Addressbook Class
 */
class Createpost
{

    /**
     * Initialize the class
     */
    function __construct()
    {
    }

    /**
     * Create Custom Post.
     *
     * @return 
     */
    public function create_custom_post()
    {

        $supports = array(
            'title', // post title
            'editor', // post content
            'author', // post author
            'thumbnail', // featured images
            'excerpt', // post excerpt
            'custom-fields', // custom fields
            'comments', // post comments
            'revisions', // post revisions
            'post-formats', // post formats
        );
        $labels = array(
            'name' => _x('Orders', 'plural'),
            'singular_name' => _x('Orders', 'singular'),
            'menu_name' => _x('Orders', 'admin menu'),
            'name_admin_bar' => _x(')Orders', 'admin bar'),
            'add_new' => _x('Add New', 'add new'),
            'add_new_item' => __('Add New Orders'),
            'new_item' => __('New Orders'),
            'edit_item' => __('Edit Orders'),
            'view_item' => __('View Orders'),
            'all_items' => __('All Orders'),
            'search_items' => __('Search Orders'),
            'not_found' => __('No Orders found.'),
        );
        $args = array(

            'labels' => $labels,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'rewrite' => array('slug' => 'orders'),
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 21,
        );

        register_post_type('Orders', $args);
    }
}
