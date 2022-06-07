<?php

/**
 * Test API to Wordpress
 *
 * @wordpress-plugin
 * Plugin Name: Test API to Wordpress
 * Description: Test Plugin.
 * Author: Andrei
 * Author URI:
 * Version: 1.0.0
 * Text Domain: test
 */

if (!defined('ABSPATH')) {
    exit;
}
add_action('plugins_loaded', 'test_plugin_init');

function test_plugin_init()
{
    if (!class_exists('WP_Test_Plugin')) :
        class WP_Test_Plugin
        {
            /**
             * @var Singleton The reference the *Singleton* instance of this class
             */
            private static $instance;

            /** Returns the *Singleton* instance of this class.
             *
             * @return Singleton The *Singleton* instance.
             */
            public static function get_instance()
            {
                if (null === self::$instance) {
                    self::$instance = new self();
                }
                return self::$instance;
            }

            private function clone()
            {
            }

            private function __wakeup()
            {
            }

            /**
             * Protected constructor to prevent creating a new instance of the
             * *Singleton* via the new operator from outside of this class.
             */
            private function __construct()
            {
                add_action('admin_init', array($this, 'install'));

                register_activation_hook(__FILE__, [$this, 'activate']);
                register_deactivation_hook(__FILE__, [$this, 'deactivate']);
                register_uninstall_hook(__FILE__, [$this, 'uninstall']);

                $this->init();
            }

            /**
             * Init the plugin after plugins_loaded so environment variables are set.
             *
             * @since 1.0.0
             */
            public function init()
            {
                require_once(dirname(__FILE__) . '/includes/class-test-plugin.php');
                $testPlugin = new Test_Plugin();
                $testPlugin->init();
            }

            public function install()
            {
                if (!is_plugin_active(plugin_basename(__FILE__))) {
                    return;
                }

                $this->install_plugin();
            }

            public function install_plugin()
            {
                global $wpdb;
                $table_name = $wpdb->get_blog_prefix() . 'test_users';
                $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                $sql = "CREATE TABLE {$table_name} (
            id int(11) unsigned NOT NULL auto_increment,
            name varchar(255) NOT NULL default '',
            PRIMARY KEY  (id)
        ) {$charset_collate};";

                dbDelta($sql);

                $table_name = $wpdb->get_blog_prefix() . 'items';
                $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
                $sql = "CREATE TABLE {$table_name} (
            id int(11) unsigned NOT NULL auto_increment,
            name varchar(255) NOT NULL default '',
            value int(11) DEFAULT NULL,
            PRIMARY KEY  (id)
        ) {$charset_collate};";

                dbDelta($sql);

                $table_name = $wpdb->get_blog_prefix() . 'user_items';
                $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
                $sql = "CREATE TABLE {$table_name} (
            id int(11) unsigned NOT NULL auto_increment,
            id_user int(11) DEFAULT NULL,
            id_item int(11) DEFAULT NULL,
            PRIMARY KEY  (id)
        ) {$charset_collate};";

                dbDelta($sql);

                $table_name = $wpdb->get_blog_prefix() . 'bit';
                $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
                $sql = "CREATE TABLE {$table_name} (
            id int(11) unsigned NOT NULL auto_increment,
            creator_id int(11) DEFAULT NULL,
            consumer_id int(11) DEFAULT NULL,
            status varchar(10) NOT NULL default '',
            creator_items varchar(255) NOT NULL default '',
            consumer_items varchar(255) NOT NULL default '',
            PRIMARY KEY  (id)
        ) {$charset_collate};";

                dbDelta($sql);
                add_option('test_plugin_activate', (get_option('test_plugin_activate') === 'false' ? '0' : '1'));
            }

            public function uninstall_plugin()
            {
                global $wpdb;
                $table_name = $wpdb->get_blog_prefix() . 'trade_users';
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

                $table_name = $wpdb->get_blog_prefix() . 'trade_items';
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

                $table_name = $wpdb->get_blog_prefix() . 'trade_user_items';
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

                $table_name = $wpdb->get_blog_prefix() . 'trade_bid';
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
            }

            public function activate()
            {
                update_option('test_plugin_activate', '1');
            }

            public function deactivate()
            {
                update_option('test_plugin_activate', '0');
            }

            public function uninstall()
            {
                global $wpdb;
                $table_name = $wpdb->get_blog_prefix() . 'trade_users';
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

                $table_name = $wpdb->get_blog_prefix() . 'trade_items';
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

                $table_name = $wpdb->get_blog_prefix() . 'trade_user_items';
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

                $table_name = $wpdb->get_blog_prefix() . 'trade_bid';
                $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
            }


        }


        WP_Test_Plugin::get_instance();
    endif;
}
