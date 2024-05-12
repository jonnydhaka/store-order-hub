<?php

namespace Wppool\Orderhub\Admin;

/**
 * The Menu handler class
 */
class Menu
{

    public $addressbook;

    /**
     * Initialize the class
     */
    function __construct($addressbook)
    {
        $this->addressbook = $addressbook;

        add_action('admin_menu', [$this, 'admin_menu']);
    }

    /**
     * Register admin menu
     *
     * @return void
     */
    public function admin_menu()
    {
        $parent_slug = 'wppool-store-order-hub';
        $capability = 'manage_options';
        add_menu_page(__('Wppool Store Order Hub', 'wppool-store-order'), 'Wppool Store Order Hub', $capability, $parent_slug, [$this->addressbook, 'plugin_page']);
        $hook = add_submenu_page($parent_slug, __('Orders', 'wppool-store-order'), __('Orders', 'wppool-store-order'), $capability, "wppool-store-order-list", [$this->addressbook, 'plugin_lists_page']); 
        add_action('admin_head-' . $hook, [$this, 'enqueue_assets']);
    }



    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function enqueue_assets()
    {
        wp_enqueue_style('orderhub-admin-style');
        wp_enqueue_script('orderhub-admin-script');
    }
}
