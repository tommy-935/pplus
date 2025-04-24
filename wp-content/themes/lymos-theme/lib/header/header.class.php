<?php
class lymosHeader{

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('lymos_header', [$this, 'genHeader']);
		add_action('los-header-right', [$this, 'headerCart'], 1);
		add_action('los-header-right', [$this, 'headerAccount'], 2);
		add_action('los-header-right', [$this, 'headerMobileMenu'], 3);
	}

	public function genHeader(){
		// require_once 'template/header.php';
	}

	public function headerCart(){
		get_template_part('templates/header/header-cart');
	}

	public function headerMobileMenu(){
		get_template_part('templates/header/header-mobile-menu');
	}

	public function headerAccount(){
		//require_once 'template/header-cart.php';
	}
}