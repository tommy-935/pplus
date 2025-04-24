<?php
class lymosProduct{

	public function __construct(){
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('los_product_gallery', [$this, 'genGallery']);
		add_action('los_product_thumbs', [$this, 'genThumbs']);
	}

	public function genGallery(){
		get_template_part('templates/woocommerce/gallery');
	}

	public function genThumbs(){
		get_template_part('templates/woocommerce/thumbs');
	}
}