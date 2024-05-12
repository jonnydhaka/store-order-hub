<?php

namespace Wppool\Orderhub;

/**
 * The admin class
 */
class Admin {

    /**
     * Initialize the class
     */
    function __construct() {
        $addressbook = new Admin\Addressbook();
        new Admin\Menu( $addressbook );
    }
}
