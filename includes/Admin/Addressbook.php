<?php

namespace Wppool\Orderhub\Admin;

use Wppool\Orderhub\Traits\Get_Value;
use Wppool\Orderhub\Lists as Lists;
use Wppool\Orderhub\Post\Editpost as Editpost;




/**
 * Addressbook Handler class
 */
class Addressbook
{

    use Get_Value;


    public function plugin_page()
    {
        echo $this->get_domain();

    }

    public function plugin_lists_page()
    {
        if(isset($_GET["targetpost"])){
            $editorderdata = new Editpost();
            $editorderdata->create_edit_page($_GET["targetpost"]);

        }else{
            $lists = new Lists();
            $lists->prepare_items();
            ?><div class="wrap">
                <h1 class="wp-heading-inline"> <?php _e('Orders Details', 'wppool-store-order');?></h1>
                <?php $lists->views();?>
                <form method="post">
                <?php
                    $lists->search_box('Search', 'search');
                    $lists->display();
                ?>
                </form><?php
        }
        
    }
}
