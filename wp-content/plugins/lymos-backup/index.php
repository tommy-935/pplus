<?php
/**
 * Plugin Name: wp backup
 * Plugin URI: /
 * Author: /
 * Author URI: /
 * Description: wp backup
 * Version: 1.0.0
 * License: GNU Public License
 */
class lybpClass{

	public $wol_obj = null;
	public $front_obj = null;
	public $admin_obj = null;
	const SUFFIX = '';
	const LYBP_VERSION = '1.0.0';

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		
		$this->_initPath();
		$this->_requireFile();
		$this->_addHooks();
		$this->_initScript();
	}

	private function _addHooks(){
		add_action('admin_menu', array($this->admin_obj, 'registerMenu' ), 99);
		add_action('wp_ajax_ajaxLybpBackupDb', [$this->admin_obj, 'ajaxLybpBackupDb']);
		add_action('wp_ajax_ajaxLybpBackupFile', [$this->admin_obj, 'ajaxLybpBackupFile']);
		register_activation_hook(__FILE__, [$this, 'activate']);
	}

	public function activate(){
		$this->_createTable();
	}

	private function _createTable(){
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'lymos_backup_list` ' . '
(
	`id` int(11) not null auto_increment,
	`filename` varchar(60) not null default "",
	`email` varchar(255) not null default "",
	`type` tinyint(1) not null default 0 comment "0.file 1.database",
	`added_by` int(11) not null default 0,
	`added_date` datetime default null,
	primary key(`id`)
)ENGINE=InnoDB CHARSET=utf8mb4 collate=utf8mb4_unicode_ci;';
		$wpdb->query($sql);
	}


	private function _initPath(){
		define('LYBP_DIR', dirname(__FILE__));
		define('LYBP_PATH', __FILE__);
		define('LYBP_URL', plugins_url('', __FILE__));
	}

	private function _initScript(){
		if(is_admin()){
			add_action('admin_enqueue_scripts', [$this, 'adminScript']);
		}else{
			add_action('wp_enqueue_scripts', [$this, 'frontScript']);
		}
	}

	public function frontScript(){
		// wp_register_style( 'lybp-front-style', plugins_url( 'assets/css/wolFront' . self::SUFFIX . '.css', LYBP_PATH ), [], self::LYBP_VERSION );
        // wp_enqueue_style( 'lybp-front-style' );
		// wp_enqueue_script( 'lybp-front-script', plugins_url( 'assets/js/wolFront' . self::SUFFIX . '.js', LYBP_PATH), [], self::LYBP_VERSION, true );

	}

	private function _requireFile(){
		if(is_admin()){
			$this->_requireAdminFile();
    	}else{
			$this->_requireFrontFile();
    	}
	}

	private function _requireAdminFile(){
    	require_once LYBP_DIR . '/lib/lybpAdmin.php';
    	$this->admin_obj = new lybpAdmin;
    }

	private function _requireFrontFile(){
		/*
		require_once LYBP_DIR . '/lib/lybpFront.php';
    	$this->front_obj = new lybpFront;
		*/
	}

	public function adminScript(){
		wp_register_style( 'lybp-admin-style', plugins_url( 'assets/css/lybpAdmin' . self::SUFFIX . '.css', LYBP_PATH ), [], self::LYBP_VERSION );
        wp_enqueue_style( 'lybp-admin-style' );
		wp_enqueue_script( 'lybp-admin-js', plugins_url( 'assets/js/lybpAdmin' . self::SUFFIX . '.js', LYBP_PATH), [], self::LYBP_VERSION, true );
	}
}
new lybpClass;