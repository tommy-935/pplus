<?php
class lymosTopbar{

	public function __construct(){
		$this->_init();
	}

	private function _init(){

	}

	private function _addHooks(){
		add_action('lymos_topbar', [$this, '_genTopbar']);
	}

	private function _genTopbar(){
		require_once 'template/topbar.php';
	}
}