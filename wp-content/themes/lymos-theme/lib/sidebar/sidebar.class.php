<?php
class lymosSidebar{

	public function __construct(){
		$this->_init();
	}

	private function _init(){

	}

	private function _addHooks(){
		add_action('lymos_sidebar', [$this, '_genSidebar']);
	}

	private function _genSidebar(){
		require_once 'template/sidebar.php';
	}
}