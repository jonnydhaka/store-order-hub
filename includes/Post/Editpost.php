<?php

namespace Wppool\Orderhub\Post;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;
use Wppool\Orderhub\JWT as JWT;
use Wppool\Orderhub\Traits\Get_Value;

/**
 * Addressbook Class
 */
class Editpost
{

    use Get_Value;
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
    public function create_edit_page($id)
    {
        if(isset($_POST['orderformsubmit'])){
            if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'order-edit-page' ) ) {

                $status = sanitize_text_field( $_POST['status'] );
                $note = implode( "\n", array_map( 'sanitize_textarea_field', explode( "\n", $_POST['note'] ) ) );
                update_post_meta( $id, 'wppool_notes',$note );
                update_post_meta ( $id, "wppool_status", sanitize_text_field( $_POST['status'] ) );
                $domainname=get_post_meta($id, 'wppool_order_domain_name',true );
                $orders_id=get_post_meta($id, 'wppool_id',true );    
                $getapikey = wd_ac_get_apikey($domainname);
                if (!$getapikey) {
                    //echo "111";
                    return false;
                }
                $order_update_data=array("status"=>$status,"note"=>$note,"orders_id"=>$orders_id );
                $jwt = new JWT();
                $senddata = $jwt->generate_jwt($order_update_data, $getapikey->api_key);
                $url = $domainname.'/wp-json/api/v1/orderupdate';
                $response = wp_remote_post(
                    $url,
                    array(
                        'method'      => 'POST',
                        'timeout'     => 45,
                        'headers'     => array(),
                        'body'        => array(
                            'domainname' => $this->get_domain(),
                            'senddata' => $senddata,
                        ),
        
                    )
                );
               
                // echo "<pre>", print_r($response);
                // exit();

                $redirect_url= add_query_arg( array( 
                    'page' => "wppool-store-order-list", 
                ), admin_url( 'admin.php' ) );
                echo("<script>location.href = '".$redirect_url."'</script>");
                //wp_redirect($redirect_url);
                exit();
            } else {
              die( __( 'Security check', 'wppool-store-order' ) );
            }

        }else{
            $statusarray=array("panding-payment"=>"Pending payment","failed"=>"Failed","processing"=>"Processing",
            "completed"=>"Completed","on-hold"=>"On hold","cancelled"=>"Cancelled","refunded"=>"Refunded");
            $status=get_post_meta($id, 'wppool_status',true );
            $note=get_post_meta($id, 'wppool_notes',true ); 
            $orders_id=get_post_meta($id, 'wppool_id',true );       
            ?>
            <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo sprintf(__('Orders ID %s', 'wppool-store-order' ), $orders_id);?></h1>
                <form method="post">
                    <div class="status-box">
                    <p><strong><?php _e( 'Status', 'wppool-store-order' ) ?> </strong></p>
                        <select name="status" id="cars">
                        <?php
                        foreach($statusarray as $key=>$value){
                            echo '<option ' . ( $status == $key ? 'selected="selected"' : '' ) . 'value="'.$key.'">'.$value.'</option>';
                        }
                        ?>
                    </select>
                    </div>
                    <p><strong><?php _e( 'Note', 'wppool-store-order' ) ?></strong></p>
                    <div class="note-box">
                    <textarea id="note" name="note" rows="4" cols="50"><?php echo $note ?></textarea>
                    </div>
                    <?php wp_nonce_field( "order-edit-page"); ?>
                
                <?php submit_button("Update", 'button', "orderformsubmit", false, array('id' => 'update')); ?>
                    
            </form>
            </div>
    
       <?php

        }
       
        
        }
}
