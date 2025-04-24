<?php
/*
*  Plugin Name: Demo Plugin Composer
*  Plugin URI: http://demo.com
*  Description: Download any plugin from your wordpress admin panel's plugins page by just one click!
*  Version: 1.0.0
*  Author: lymos
*  Author URI: https://profiles.wordpress.org/lymos/
*  Text Domain: demo-plugin
*  Requires at least: 4.8
*  Tested up to: 5.9.3
*  Requires PHP: 5.6
*  Developer: First build need to delete vendor and run php composer install, and development
*/
use Lymos\Lwc\Main\Core;
if (! defined( 'ABSPATH' ) ) {
    exit;
}
define('LWC_DIR', dirname(__FILE__));
define('LWC_PATH', __FILE__);
define('LWC_URL', plugins_url('', __FILE__));
define('LWC_BASE', plugin_basename(LWC_PATH));
require_once LWC_DIR . '/vendor/autoload.php';

add_action( 'plugins_loaded', 'lwcPluginLoaded' );

function lwcPluginLoaded(){
    static $instance;
	if ( is_null( $instance ) ) {

		$instance = new Core;
        /**
         * demo plugin loaded.
         *
         * Fires when demo plugin was fully loaded and instantiated.
         *
         */
        // do_action( 'some_action_plugin_loaded' );
	}
	return $instance;
}

class lymosLwcInit{
    public function __construct()
    {
        register_activation_hook(LWC_PATH, [$this, 'activate']);
		// register_activation_hook(LWC_PATH, [$this, 'addSchedule']);
        register_deactivation_hook(LWC_PATH, [$this, 'deactivation']);
		// add_filter('cron_schedules', [$this, 'addCustomSchedules']);
    }

    /**
	 * http://www.example.com/wp-cron.php?doing_wp_cron 手动执行调式
	 */
	public function addSchedule(){
		$time = time();

		// wp_schedule_event($time, 'hourly', 'dospider_action');
		wp_schedule_event($time, 'mycron', 'dospider_action');

	}

	public function addCustomSchedules($schedules){
        $time = get_option('asm_schedules_time');
		// $time = 360; // debug
		if(! $time){
			$time = 120 * 6;
		}
		$schedules['mycron'] = array(
			'interval' => $time * 60, 
			'display' => __( $time . ' mins per' )
		);

		return $schedules;
    }

    public function deactivation(){
		// wp_clear_scheduled_hook('dospider_action');
	}

    public function activate(){
		
		// $this->_createTable();
	}

	private function _createTable(){
        global $wpdb;
		$sql = 'CREATE TABLE `' . $wpdb->prefix . 'por_callback` ' . '
(
	`id` int(11) not null auto_increment,
	`session_key` varchar(60) not null default "",
	`data` text default null,
	`status` tinyint(1) not null default 0 comment "0.default 1.paid",
	`local_order_id` int(11) not null default 0,
	`remote_order_id` int(11) not null default 0,
	`remote_order_status` varchar(60) not null default "",
	`added_by` int(11) not null default 0,
	`added_date` datetime default null,
	primary key(`id`)
	
)ENGINE=InnoDB CHARSET=utf8mb4 collate=utf8mb4_unicode_ci;';
		$wpdb->query($sql);
		
	}
}
new lymosLwcInit;
