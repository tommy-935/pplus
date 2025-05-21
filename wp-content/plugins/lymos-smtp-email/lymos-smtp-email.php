<?php
/** 
 * Plugin Name: Lymos Smtp Email
 * Plugin URI:: https://lymoswp.fly.dev/
 * Author: lymoswp
 * Author URI: https://lymoswp.fly.dev/
 * Description: Send Emails, Like order email and other emails. Record the email logs.
 * Version: 1.0.2
 * License: GPLv2
 */
namespace lymosSmtpEmail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class lymosSmtpEmail{

    const SUFFIX = '';
    const LYMOS_SMTP_VERSION = '1.0.2';
	const TABLE_RECORD = 'lymoswp_email_record';
	public $lymos_smtp_adminlib_obj;
	public $lymos_smtp_admin_obj;
	public $plugin_file;
	
    public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->plugin_file = __FILE__;
		$this->_initPath();
		$this->_requireFile();
		$this->_addHooks();
		$this->_initScript();
	}

	private function _addHooks(){

		add_action('plugins_loaded', 'my_plugin_check_upgrade');

		add_action('wp_ajax_ajaxSaveSmtp', [$this->lymos_smtp_admin_obj, 'ajaxSaveSmtp']);
		add_action('wp_ajax_ajaxLseList', [$this->lymos_smtp_admin_obj, 'getList']);
		add_action('wp_ajax_ajaxSaveMessage', [$this->lymos_smtp_adminlib_obj, 'saveMessage']);
		add_action('wp_ajax_lymos_smtp_resend', [$this->lymos_smtp_adminlib_obj, 'resend']);
		add_action('wp_ajax_lymos_smtp_opened', [$this->lymos_smtp_adminlib_obj, 'opened']);
		add_action('wp_ajax_nopriv_lymos_smtp_opened', [$this->lymos_smtp_adminlib_obj, 'opened']);


		register_activation_hook(__FILE__, [$this, 'activate']);
		register_activation_hook(__FILE__, [$this, 'addSchedule']);
		if(is_admin()){
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		}
	}

	public function addSchedule(){
		$time = time();
		wp_schedule_event($time, 'hourly', [$this, 'autoSendFailedEmail']);
	}

	public function autoSendFailedEmail(){
		$this->lymos_smtp_adminlib_obj->autoSendFailedEmail();
	}

	private function _initPath(){
		define('LYMOS_SMTP_DIR', dirname(__FILE__));
		define('LYMOS_SMTP_PATH', __FILE__);
		define('LYMOS_SMTP_URL', plugins_url('', __FILE__));
		define('LYMOS_SMTP_NONCE', 'lymos-smtp-email');
	}

	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( $this->plugin_file ) === $file ) {
			$settings_link = '<a href="options-general.php?page=lymos_email_settings">' . __( 'Settings', 'lymos-smtp-email' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	private function _initScript(){
		if(is_admin()){
			$this->_adminScript();
		}else{
			$this->_frontScript();
		}
	}

	private function _frontScript(){
		//wp_register_style( 'lse-style', plugins_url( 'assets/css/Style' . self::SUFFIX . '.css', LYMOS_SMTP_PATH ), [], self::LYMOS_SMTP_VERSION );
        //wp_enqueue_style( 'lse-style' );
		//wp_enqueue_script( 'lse-front-js', plugins_url( 'assets/js/Front' . self::SUFFIX . '.js', LYMOS_SMTP_PATH), [], self::LYMOS_SMTP_VERSION, true );

	}

	private function _requireFile(){
		require_once LYMOS_SMTP_DIR . '/admin/lib/lseAdmin.php';
		$this->lymos_smtp_adminlib_obj = new \lymosSmtpEmail\admin\lib\lseAdmin;
		require_once LYMOS_SMTP_DIR . '/admin/lib/hooks.php';
		new \lymosSmtpEmail\admin\lib\Hooks;

		if(is_admin()){
            $this->_requireAdminFile();
    	}else{
			$this->_requireFrontFile();
    	}

	}

    private function _requireAdminFile(){
		require_once LYMOS_SMTP_DIR . '/admin/lib/adminView.php';
		$this->lymos_smtp_admin_obj = new \lymosSmtpEmail\admin\lib\adminView;
	}

	private function _requireFrontFile(){
		
	}

	private function _adminScript(){
		wp_register_style( 'lse-admin-style', plugins_url( 'admin/assets/css/main' . self::SUFFIX . '.css', LYMOS_SMTP_PATH ), [], self::LYMOS_SMTP_VERSION );
        wp_enqueue_style( 'lse-admin-style' );
		wp_enqueue_script( 'lse-admin-js', plugins_url( 'admin/assets/js/main' . self::SUFFIX . '.js', LYMOS_SMTP_PATH), [], self::LYMOS_SMTP_VERSION, true );
	}

	public function activate(){
		$this->_createTable();
		add_option('lymos_smtp_plugin_version', self::LYMOS_SMTP_VERSION);
	}

	public function plugin_check_upgrade() {
		$saved_version = get_option('lymos_smtp_plugin_version');
	
		if ($saved_version !== self::LYMOS_SMTP_VERSION) {
			$this->plugin_upgrade($saved_version);
			update_option('lymos_smtp_plugin_version', self::LYMOS_SMTP_VERSION);
		}
	}

	public function plugin_upgrade($old_version) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_RECORD;
	
		$column1 = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'status'");
		if (empty($column1)) {
			$wpdb->query('alter table ' . $table_name . ' add column `status` varchar(12) not null default "Success" comment "Wait, Success, Failed"');
		}

		$column2 = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'resend_times'");
		if (empty($column2)) {
			$wpdb->query('alter table ' . $table_name . ' add column `resend_times` tinyint(1) not null default 0 comment "resend times"');
		}

		$column3 = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'error_message'");
		if (empty($column3)) {
			$wpdb->query('alter table ' . $table_name . ' add column `error_message` text default null');
		}

		$column4 = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'key'");
		if (empty($column4)) {
			$wpdb->query('alter table ' . $table_name . ' add column `key` varchar(120) not null default ""');
		}

		$column5 = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'opened'");
		if (empty($column5)) {
			$wpdb->query('alter table ' . $table_name . ' add column `opened` varchar(12) not null default "No" comment "No, Yes"');
		}

		// if (version_compare($old_version, '1.0.5', '<')) { ... }
	}

	private function _createTable(){
        global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . self::TABLE_RECORD . '` ' . '
(
	`id` int(11) not null auto_increment,
	`key` varchar(120) not null default "",
	`email` varchar(120) not null default "",
	`subject` varchar(1000) not null default "",
	`body` text default null,
	`status` varchar(12) not null default "Success" comment "Wait, Success, Failed",
	`opened` varchar(12) not null default "No" comment "No, Yes",
	`resend_times` tinyint(1) not null default 0 comment "resend times",
	`error_message` text default null,
	`added_by` int(11) not null default 0,
	`added_date` datetime default null,
	primary key(`id`),
	index email_index (`email`),
	unique index key_unique (`key`)
	
)ENGINE=InnoDB CHARSET=utf8mb4 collate=utf8mb4_unicode_ci;';
		$wpdb->query($sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery
		
	}
}
new lymosSmtpEmail;