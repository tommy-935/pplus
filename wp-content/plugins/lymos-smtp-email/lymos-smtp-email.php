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
	const LYMOS_SMTP_PAY_VERSION = '1.0.2';
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
		add_action('wp_ajax_ajaxSaveSmtp', [$this->lymos_smtp_admin_obj, 'ajaxSaveSmtp']);
		add_action('wp_ajax_ajaxLseList', [$this->lymos_smtp_admin_obj, 'getList']);
		add_action('wp_ajax_ajaxSaveMessage', [$this->lymos_smtp_adminlib_obj, 'saveMessage']);

		register_activation_hook(__FILE__, [$this, 'activate']);
		if(is_admin()){
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		}
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
		wp_register_style( 'lse-admin-style', plugins_url( 'admin/assets/css/main' . self::SUFFIX . '.css', LYMOS_SMTP_PATH ), [], self::LYMOS_SMTP_PAY_VERSION );
        wp_enqueue_style( 'lse-admin-style' );
		wp_enqueue_script( 'lse-admin-js', plugins_url( 'admin/assets/js/main' . self::SUFFIX . '.js', LYMOS_SMTP_PATH), [], self::LYMOS_SMTP_PAY_VERSION, true );
	}

	public function activate(){
		
		$this->_createTable();
	}

	private function _createTable(){
        global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'lymoswp_email_record` ' . '
(
	`id` int(11) not null auto_increment,
	`email` varchar(120) not null default "",
	`subject` varchar(1000) not null default "",
	`body` text default null,
	`added_by` int(11) not null default 0,
	`added_date` datetime default null,
	primary key(`id`),
	index email_index (`email`)
	
)ENGINE=InnoDB CHARSET=utf8mb4 collate=utf8mb4_unicode_ci;';
		$wpdb->query($sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery
		
	}
}
new lymosSmtpEmail;