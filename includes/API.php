<?php

namespace Wppool\Orderhub;

/**
 * API Class
 */
class API
{

    /**
     * Initialize the class
     */
    function __construct()
    {

        add_action('rest_api_init', [$this, 'register_api']);
        // add_action('admin_init', [$this, 'register_api']);
    }

    /**
     * Register the API
     *
     * @return void
     */
    public function register_api()
    {
        $addressbook = new API\Addressbook();
        $addressbook->register_routes();
    }
}
