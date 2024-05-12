<?php

namespace Wppool\Orderhub;

/**
 * Assets handlers class
 */
class Assets
{

    /**
     * Class constructor
     */
    function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_assets']);
    }

    /**
     * All available scripts
     *
     * @return array
     */
    public function get_scripts()
    {
        return [
            
            'orderhub-admin-script' => [
                'src'     => WD_ORDERHUB_ASSETS . '/js/admin.js',
                'version' => filemtime(WD_ORDERHUB_PATH . '/assets/js/admin.js'),
                'deps'    => ['jquery', 'wp-util']
            ],
        ];
    }

    /**
     * All available styles
     *
     * @return array
     */
    public function get_styles()
    {
        return [
           
            'orderhub-enquiry-style' => [
                'src'     => WD_ORDERHUB_ASSETS . '/css/enquiry.css',
                'version' => filemtime(WD_ORDERHUB_PATH . '/assets/css/enquiry.css')
            ],
        ];
    }

    /**
     * Register scripts and styles
     *
     * @return void
     */
    public function register_assets()
    {
        $scripts = $this->get_scripts();
        $styles  = $this->get_styles();

        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;

            wp_register_script($handle, $script['src'], $deps, $script['version'], true);
        }

        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;

            wp_register_style($handle, $style['src'], $deps, $style['version']);
        }

        wp_localize_script('orderhub-admin-script', 'WppoolStoreOrder', [
            'nonce' => wp_create_nonce('wppool-so-admin-nonce'),
            'confirm' => __('Are you sure?', 'wppool-store-order'),
            'error' => __('Something went wrong', 'wppool-store-order'),
        ]);
    }
}
