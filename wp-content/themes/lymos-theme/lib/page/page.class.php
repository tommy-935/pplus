<?php
class lymosPage{

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('los_page', [$this, 'genPageHeader']);
		add_action('los_page', [$this, 'genPageContent']);
	}

	public function genPageHeader(){
		get_template_part('templates/page/header');
	}

	public function genPageContent(){
		get_template_part('templates/page/content');
	}
}