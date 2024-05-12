<?php

namespace Wppool\Orderhub;

/**
 * API Class
 */
class Post
{

    /**
     * Initialize the class
     */
    function __construct()
    {
        add_action('init', [$this, 'register_custom_post']);
    }

    /**
     * Register the API
     *
     * @return void
     */
    public function register_custom_post()
    {
        $addressbook = new Post\Createpost();
        $addressbook->create_custom_post();
    }
}
