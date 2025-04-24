<?php
class lymosContent{

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('los_loop_post', [$this, 'genPostHeader']);
		add_action('los_loop_post', [$this, 'genPostContent']);
	}

	public function genPostHeader(){
		get_template_part('templates/content/header');
	}

	public function genPostContent(){
		get_template_part('templates/content/content');
	}
}