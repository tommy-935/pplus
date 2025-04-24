<?php
class lymosMenu{

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('init', [$this, 'registerLocation']);
	}

	public function registerLocation(){
		$locations = array(
			'primary'  => __( 'Desktop Menu', 'lymostheme' ),
			'mobile'   => __( 'Mobile Menu', 'lymostheme' ),
		);
		register_nav_menus( $locations );
	}
}