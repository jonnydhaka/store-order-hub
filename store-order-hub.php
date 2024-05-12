<?php

/**
 * Plugin Name: Wppool Store order Hub
 * Description: Store order Hub plugin
 * Plugin URI: https://tanvirhasan.co
 * Author: Tanvir hasan
 * Author URI: https://tanvirhasan.co
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */
final class Wppool_Orderhub
{

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0';

    /**
     * Class construcotr
     */
    private function __construct()
    {
        $this->define_constants();

        register_activation_hook(__FILE__, [$this, 'activate']);

        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initializes a singleton instance
     *
     * @return \Wppool_Orderhub
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('WD_ORDERHUB_VERSION', self::version);
        define('WD_ORDERHUB_FILE', __FILE__);
        define('WD_ORDERHUB_PATH', __DIR__);
        define('WD_ORDERHUB_URL', plugins_url('', WD_ORDERHUB_FILE));
        define('WD_ORDERHUB_ASSETS', WD_ORDERHUB_URL . '/assets');
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin()
    {

        new Wppool\Orderhub\Assets();

        if (defined('DOING_AJAX') && DOING_AJAX) {
            new Wppool\Orderhub\Ajax();
        }

        if (is_admin()) {
            new Wppool\Orderhub\Admin();
        } 
        new Wppool\Orderhub\Post();
        new Wppool\Orderhub\API();
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate()
    {
        $installer = new Wppool\Orderhub\Installer();
        $installer->run();
    }
}

/**
 * Initializes the main plugin
 *
 * @return \Wppool_Orderhub
 */
function wppool_orderhub()
{
    return Wppool_Orderhub::init();
}

// kick-off the plugin
wppool_orderhub();
