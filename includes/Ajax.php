<?php

namespace Wppool\Orderhub;

/**
 * Ajax handler class
 */
class Ajax
{

    /**
     * Class constructor
     */
    function __construct()
    {
        add_action('wp_ajax_wppool-delete-post-by-id', [$this, 'delete_post_by_id']);
    }

    /**
     * Handle contact deletion
     *
     * @return void
     */
    public function delete_post_by_id()
    {


        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'wppool-so-admin-nonce')) {
            wp_send_json_error([
                'message' => __('Nonce verification failed!', 'wppool-store-order')
            ]);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('No permission!', 'wppool-store-order')
            ]);
        }

        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        if($id){
            wp_delete_post( $id, true); 
        }
        
        wp_send_json_success();
    }
}
