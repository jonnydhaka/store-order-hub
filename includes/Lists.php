<?php

namespace Wppool\Orderhub;

use Wppool\Orderhub\Traits\Get_Value;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// ref::
//ref:: http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
//http://codingbin.com/display-custom-table-data-wordpress-admin/
//http://wpengineer.com/2426/wp_list_table-a-step-by-step-guide/
//https://webkul.com/blog/create-admin-tables-using-wp_list_table-class/

// WP_List_Table is not loaded automatically so we need to load it in our application
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Create a new table class that will extend the WP_List_Table
 */
class Lists extends \WP_List_Table
{

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    public $page_status;

    public function __construct()
    {

        parent::__construct(array(

            'singular' => 'Orders', //singular name of the listed records

            'plural' => 'Orders', //plural name of the listed records

            'ajax' => false,

        ));
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $this->process_bulk_action();
        $this->_column_headers = $this->get_column_info();
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;
        $this->page_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '2';

        // only ncessary because we have sample data
        $args = array(
            'post_type' => 'Orders',
            'post_status' => 'publish',
            'offset' => $offset,
            'posts_per_page' => $per_page,
            'paged' => $current_page 
        );

        if (isset($_REQUEST['orderby']) && isset($_REQUEST['order'])) {
            $args['orderby'] = $_REQUEST['orderby'];
            $args['order'] = $_REQUEST['order'];
        }

        $this->items = $this->table_data($args);

        $argsall = array(
            'all' => 'all',
        );
        $count_data = wp_count_posts("orders")->publish;
      

        $this->set_pagination_args(array(
            'total_items' => $count_data,
            'per_page' => $per_page,
        ));

        $this->_column_headers = array($columns, $hidden, $sortable);
        /** Process bulk action */
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            //'id_envato_licence_info'          => 'ID',
            'cb' => '<input type="checkbox" />',
            'orders_id' => 'Orders Id',
            'customer_name' => 'Customer Name',
            'status' => 'Status',
            'email' => 'Email',
            'note' => 'Order Note',
            'order_date' => 'Order Date',
            'shipping_date' => 'Shipping Date',

        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'orders_id' => array('orders_id', true),
            'customer_name' => array('customer_name', true),
            'status' => array('status', true),
            'email' => array('email', true),
            'order_date' => array('order_date', true),
            'shipping_date' => array('shipping_date', true),
        );
        return $sortable_columns;
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data($args = array())
    {
        
        $data = array();

        global $wpdb;

        $table_name = $wpdb->prefix . "posts";

        $defaults = array(
            'posts_per_page' => 20,
            'offset' => 0,
            'orderby' => 'id',
            'order' => 'ASC',
        );

        $args = wp_parse_args($args, $defaults);

        // check if a search was performed.
        $search = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';
        /* If the value is not NULL, do a search for it. */
        if ($search != null) {
            $search = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : false;
            $meta_query = [];
            $meta_query['relation'] = 'OR';
            $metadatas=['wppool_customer_first_name','wppool_status','wppool_customer_email'];
            foreach ( $metadatas as $metadata ) {
                $meta_query[] = [
                    'key'     => $metadata,
                    'value'   => $search,
                    'compare' => 'LIKE',
                ];
            }
            $args[ 'meta_query']= $meta_query;
        }
       
    
        $loop = new \WP_Query( $args ); 
        $count = 0; 
            
        while ( $loop->have_posts() ) : $loop->the_post(); 
        $orderdate=get_post_meta(get_the_ID(), 'wppool_customer_date',true );
        $data[$count]['post_id']=get_the_ID();

        $data[$count]['orders_id']=get_post_meta(get_the_ID(), 'wppool_id',true );
        $data[$count]['customer_name']=get_post_meta(get_the_ID(), 'wppool_customer_first_name',true );
        $data[$count]['status']=get_post_meta(get_the_ID(), 'wppool_status',true );
        $data[$count]['email']=get_post_meta(get_the_ID(), 'wppool_customer_email',true );
        $data[$count]['note']=get_post_meta(get_the_ID(), 'wppool_notes',true );
        $data[$count]['order_date']=($orderdate)? date('d-m-Y',strtotime($orderdate)):'';
        $data[$count]['shipping_date']= ($orderdate)? date('d-m-Y',strtotime($orderdate. ' + 20 days')):'';
            $count ++; 

        endwhile;
        wp_reset_postdata(); 
        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        //print_r($item);
        switch ($column_name) {
            case 'orders_id':
                return $item['orders_id'];
            case 'customer_name':
                return $item['customer_name'];
            case 'status':
                return $item['status'];
            case 'email':
                return $item['email'];
            case 'note':
                return $item['note'];
            case 'order_date':
                return $item['order_date'];
            case 'shipping_date':
                return $item['shipping_date'];
            default:
                return isset( $item[$column_name] ) ? $item[$column_name] : '';
                //return print_r($item, true);
        }
    }

    public function column_licence_type($item)
    {

        $licence_type_name = Envato_Licence_Valicator::licenceTypeName($item->license_type);
        $value = $licence_type_name;
        return $value;
    }


    function column_orders_id($item)
    {
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&targetpost=%s">' . __('Edit', 'supporthost-admin-table') . '</a>', $_REQUEST['page'], 'edit', $item['post_id']),
       
               'delete' => sprintf('<a href="#" class="submitforapi" data-id="%s">%s</a>', $item['post_id'], __('Delete', 'wppool-store-order'))
            );
        return sprintf('%1$s %2$s', $item['orders_id'], $this->row_actions($actions));
    }

    public function column_purchase_key($item)
    {

        $licence_type_name = Envato_Licence_Valicator::licenceTypeName($item->license_type);
        $value = $item->purchase_key;
        $actions = '';
        return $value;
    }

    public function search_box($text, $input_id)
    { ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <input type="hidden" id="<?php echo $input_id ?>" name="post_type" value="Orders" />
            <input type="hidden" id="<?php echo $input_id ?>" name="page" value="wppool-store-order-list" />
            <?php submit_button($text, 'button', false, false, array('id' => 'search-submit')); ?>
        </p>
<?php }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['post_id']
        );
    }

    public function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }

    public function process_bulk_action()
    {
        
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
            $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $action = 'bulk-' . $this->_args['plural'];
            if (!wp_verify_nonce($nonce, $action)) {
                wp_die('Nope! Security check failed!');
            }
            $action = $this->current_action();
            switch ($action) {
                case 'delete':
                    if ('delete' === $this->current_action()) {
                        $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
                        foreach($ids as $id){
                            wp_delete_post( $id, true); 
                        }
                    }
                    echo("<script>location.href = '".esc_url_raw(add_query_arg(null, null))."'</script>");
                    //wp_redirect(esc_url_raw(add_query_arg(null, null)));
                    exit;
                    break;
                case 'edit':
                    wp_die('This is the edit page.');
                    break;

                default:
                    // do nothing or something else
                    return;
                    break;
            }
        }
        return;
    }
}
