<?php

/**
 * Insert a new address
 *
 * @param  array  $args
 *
 * @return int|WP_Error
 */
function wd_ac_insert_keys($args = [])
{
    global $wpdb;

    if (empty($args['api_key'])) {
        return new \WP_Error('no-api-key', __('You must provide a api key.', 'wppool-store-order'));
    }

    $defaults = [
        'domainname'       => '',
        'api_key'    => '',
        'created_by' => get_current_user_id(),
        'created_at' => current_time('mysql'),
    ];

    $data = wp_parse_args($args, $defaults);

    if (isset($data['id'])) {

        $id = $data['id'];
        unset($data['id']);

        $updated = $wpdb->update(
            $wpdb->prefix . 'wppool_apikeys',
            $data,
            ['id' => $id],
            [
                '%s',
                '%s',
                '%d',
                '%s'
            ],
            ['%d']
        );

        wd_ac_apikeys_purge_cache($id);

        return $updated;
    } else {

        $inserted = $wpdb->insert(
            $wpdb->prefix . 'wppool_apikeys',
            $data,
            [
                '%s',
                '%s',
                '%d',
                '%s'
            ]
        );

        if (!$inserted) {
            return new \WP_Error('failed-to-insert', __('Failed to insert data', 'wppool-store-order'));
        }

        wd_ac_apikeys_purge_cache();

        return $wpdb->insert_id;
    }
}



/**
 * Fetch a single contact from the DB
 *
 * @param  int $id
 *
 * @return object
 */
function wd_ac_get_apikey($domainname)
{
    global $wpdb;
    $address = wp_cache_get('api-' . $domainname, 'apikey');
    if (false === $address) {
        $address = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wppool_apikeys WHERE domainname = %s", $domainname)
        );

        wp_cache_set('api-' . $domainname, $address, 'apikey');
    }
    return $address;
}

/**
 * Delete an address
 *
 * @param  int $id
 *
 * @return int|boolean
 */
function wd_ac_delete_apikey($domainname)
{
    global $wpdb;

    wd_ac_apikeys_purge_cache($domainname);

    return $wpdb->delete(
        $wpdb->prefix . 'wppool_apikeys',
        ['domainname' => $domainname],
        ['%s']
    );
}

/**
 * Purge the cache for books
 *
 * @param  int $book_id
 *
 * @return void
 */
function wd_ac_apikeys_purge_cache($domainname = null)
{
    $group = 'apikey';

    if ($domainname) {
        wp_cache_delete('api-' . $domainname, $group);
    }

    wp_cache_delete('count', $group);
    wp_cache_set('last_changed', microtime(), $group);
}



function insert_metadata_by_post_id($id, $array) {
    $result=[];
    foreach($array as $key=>$value){
        if($key=="meta_data"){
            foreach($value as $metaval)
            {
                $result[ $metaval['key']]=$metaval['value'];
                if ( ! add_post_meta( $id, $metaval['key'], $metaval['value'], true ) ) { 
                    update_post_meta ( $id,  $metaval['key'], $metaval['value'] );
                 }
            }
        }else if($key=="billing" || $key=="date_created" ){

            foreach($value as $customekey=>$customerval)
            {
                $result[ "wppool_customer_".$customekey]=$customerval;
                if ( ! add_post_meta( $id, "wppool_customer_".$customekey, $customerval, true ) ) { 
                    update_post_meta ( $id,  "wppool_customer_".$customekey, $customerval );
                 }
            }

        }else{
            if(is_array($value)){
                $value=json_encode($value);
            }
            $result["wppool_".$key]=$value;
            if ( ! add_post_meta( $id, "wppool_".$key, $value, true ) ) { 
                update_post_meta ( $id,  "wppool_".$key, $value );
             }
        }
        
        
    }
    return $result;
  
  }