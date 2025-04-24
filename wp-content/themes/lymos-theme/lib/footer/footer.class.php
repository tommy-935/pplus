<?php
class lymosFooter{

	public $widget_class = null;

	public function __construct($widget_class = null){
		$this->widget_class = $widget_class;
		$this->_init();
	}

	private function _init(){
		$this->_addHooks();
	}

	private function _addHooks(){
		add_action('lymos_footer', [$this, 'genFooter']);
	}

	public function genFooter(){
		require_once LYMOS_THEME_DIR . '/templates/footer/footer.php';
	}
}