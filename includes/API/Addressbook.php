<?php

namespace Wppool\Orderhub\API;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;
use Wppool\Orderhub\JWT as JWT;

/**
 * Addressbook Class
 */
class Addressbook extends WP_REST_Controller
{

    /**
     * Initialize the class
     */
    function __construct()
    {
        $this->namespace = 'api/v1';
        $this->rest_base = 'getapi';
        $this->rest_upload = 'apiupload';
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @return void
     */
    public function register_routes()
    {

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                // [
                //     'methods'             => WP_REST_Server::READABLE,
                //     'callback'            => [$this, 'create_api_key'],
                //     'permission_callback' => "__return_true",
                // ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_api_key'],
                    'permission_callback' => "__return_true",
                ],
            ]
        );


        register_rest_route(
            $this->namespace,
            '/' . $this->rest_upload,
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_api_upload'],
                    'permission_callback' => "__return_true",
                ],
            ]
        );
    }


    /**
     * Creates one item from the collection.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|WP_REST_Response
     */
    public function create_api_key($request)
    {
        if (isset($request['buttonaction']) && $request['buttonaction'] == "remove") {
            wd_ac_delete_apikey($request['domainname']);
            $data = ['domainname' => $request['domainname'], 'remove' => true];
        } else {
            $key = md5($request['domainname'] . microtime() . rand());
            $getapikeybydomain = wd_ac_get_apikey($request['domainname']);
            if ($getapikeybydomain) {
                $data = ['domainname' => $getapikeybydomain->domainname, 'api_key' => $getapikeybydomain->api_key];
            } else {
                $data = ['domainname' => $request['domainname'], 'api_key' => $key];
                $updated = wd_ac_insert_keys($data);
            }
        }

        return rest_ensure_response($data);
    }



    /**
     * Creates one item from the collection.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|WP_REST_Response
     */
    public function create_api_upload($request)
    {
        $apikey = wd_ac_get_apikey($request['domainname']);
        $jwt = new JWT();
        $senddata = $jwt->validate_jwt($request, $apikey->api_key);
        if ($senddata) {
            global $user_ID;
            if(!empty($senddata['woodata']['wppool_post_id'])){
                $post_id =$senddata['woodata']['wppool_post_id'];
                if(!empty($senddata['woodata']['wppool_notes'])){
                    update_post_meta ( $post_id, "wppool_notes", $senddata['woodata']['wppool_notes']);
                }
                if(!empty($senddata['woodata']['status'])){
                    update_post_meta ( $post_id, "wppool_status", $senddata['woodata']['status']);
                }
                $return = array("postid" => $post_id, "status" => "Updated");
            }else{
                $new_post = array(
                    'post_title' => $senddata['domain'] . "-Order-" . $senddata['woodata']['id'],
                    'post_content' => json_encode($senddata),
                    'post_status' => 'publish',
                    'post_date' => date('Y-m-d H:i:s'),
                    'post_author' => $user_ID,
                    'post_type' => 'Orders',
                    'post_category' => array(0)
                );
                $post_id = wp_insert_post($new_post);
                if ($post_id) {
                    add_post_meta($post_id, $senddata['domain'] . "-Order-" . $senddata['woodata']['id'], json_encode($senddata), true);
                    $return = array("postid" => $post_id, "status" => "success");
                    $metaval=insert_metadata_by_post_id($post_id,$senddata['woodata']);
                    if ( ! add_post_meta( $post_id, "wppool_order_domain_name", $senddata['domain'], true ) ) { 
                        update_post_meta ( $post_id, "wppool_order_domain_name", $senddata['domain'] );
                     }
                    //print_r($metaval);
                } else {
                    $return = array("status" => "failed");
                }
            }
        } else {
            $return = array("Massage" => "Sig Failed", "status" => "failed");
        }
        return rest_ensure_response($return);
    }

}
