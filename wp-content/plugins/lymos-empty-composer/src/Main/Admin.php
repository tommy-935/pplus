<?php
namespace Lymos\Lwc\Main;

use Lymos\Lwc\View\Setting;

class Admin{

    public $obj = null;

    public function __construct($obj)
    {
        $this->obj = $obj;
        $this->_init();
    }

    public function _init(){
        $this->_addMenu();
        $this->_addHooks();
    }

    public function _addHooks(){

    }

    private function _addMenu(){
        add_action('admin_menu', array($this, 'registerMenu'), 99);
    }

    public function registerMenu() {
    	
		add_menu_page(__( 'Demo Plugin Composer', 'lwc' ), __( 'Demo Plugin Composer', 'lwc' ), 'edit_posts', 'lwc_setting', [$this, 'setting']);
		
	}

    public function setting(){
        $view = new Setting();
        $view->show();
    }
}