<?php
namespace Lymos\Lwc\Main;

use Lymos\Lwc\Main\Admin;
use Lymos\Lwc\Main\Front;

class Core {

    public $admin_obj = null;
    public $front_obj = null;
    public $db = null;
    const SUFFIX = '';
	const LWC_VERSION = '1.0.1';

    public function __construct(){
        $this->_init();
    }

    private function _init(){
		global $wpdb;
		$this->db = $wpdb;

		$this->_addHooks();
		$this->_initScript();
	}

    private function _addHooks(){
		
        $this->admin_obj = new Admin($this);
        $this->front_obj = new Front($this);
		
		/*
		add_action('wp_ajax_ajaxLybpBackupDb', [$this->admin_obj, 'ajaxLybpBackupDb']);
		add_action('wp_ajax_ajaxLybpBackupFile', [$this->admin_obj, 'ajaxLybpBackupFile']);
		*/
	
	}

    private function _initScript(){
		if(is_admin()){
			add_action('admin_enqueue_scripts', [$this, 'adminScript']);
		}else{
			add_action('wp_enqueue_scripts', [$this, 'frontScript']);
		}
	}


    public function adminScript(){
		wp_register_style( 'lwc-admin-style', plugins_url( 'assets/css/lwcAdmin' . self::SUFFIX . '.css', LWC_PATH ), [], self::LWC_VERSION );
        wp_enqueue_style( 'lwc-admin-style' );
		wp_enqueue_script( 'lwc-admin-js', plugins_url( 'assets/js/lwcAdmin' . self::SUFFIX . '.js', LWC_PATH), [], self::LWC_VERSION, true );
	}

    public function frontScript(){
		// wp_register_style( 'lwc-front-style', plugins_url( 'assets/css/wolFront' . self::SUFFIX . '.css', LWC_PATH ), [], self::LWC_VERSION );
        // wp_enqueue_style( 'lwc-front-style' );
		// wp_enqueue_script( 'lwc-front-script', plugins_url( 'assets/js/wolFront' . self::SUFFIX . '.js', LWC_PATH), [], self::LWC_VERSION, true );

	}


}