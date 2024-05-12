<?php

namespace Wppool\Orderhub;

/**
 * Installer class
 */
class Installer
{

    /**
     * Run the installer
     *
     * @return void
     */
    public function run()
    {
        $this->add_version();
        $this->create_tables();
    }

    /**
     * Add time and version on DB
     */
    public function add_version()
    {
        $installed = get_option('wd_orderhub_installed');

        if (!$installed) {
            update_option('wd_orderhub_installed', time());
        }

        update_option('wd_orderhub_version', WD_ORDERHUB_VERSION);
    }

    /**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wppool_apikeys` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `domainname` varchar(100) NOT NULL DEFAULT '',
          `api_key` varchar(255) DEFAULT NULL,
          `created_by` bigint(20) unsigned NOT NULL,
          `created_at` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) $charset_collate";

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta($schema);
    }
}
