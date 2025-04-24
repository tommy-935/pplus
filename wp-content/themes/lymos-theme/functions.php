<?php

if(! defined('ABSPATH')){
	exit;
}
define('LYMOS_THEME_DIR', dirname(__FILE__));
class lymosTheme{

	const VERSION = '1.0';
	public $widget_class = null;
	const SUFFIX = '';

	public function __construct(){
		$this->_requrieFiles();
		$this->_init();
	}

	private function _init(){
		define('THEME_URI', get_template_directory_uri());
		$this->_initHooks();
		
	}

	private function _initHooks(){
		if(is_admin()){

		}else{
			add_action('wp_enqueue_scripts', [$this, 'linkFrontCss']);
			add_action('wp_enqueue_scripts', [$this, 'linkFrontJs']);
			add_action( 'wp_head', array( $this, 'metaViewport' ), 1 );
		}
	}

	public function metaViewport() {

		// Meta viewport.
		$viewport = '<meta name="viewport" content="width=device-width, initial-scale=1">';

		// Apply filters for child theme tweaking.
		echo apply_filters( 'los_meta_viewport', $viewport ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	public function linkFrontCss(){
		
		wp_enqueue_style('los-slick-css', THEME_URI . '/assets/lib/slick/slick' . self::SUFFIX . '.css', false, self::VERSION);
		wp_enqueue_style('los-slick-theme-css', THEME_URI . '/assets/lib/slick/slick-theme' . self::SUFFIX . '.css', false, self::VERSION);
		wp_enqueue_style('los-main-css', THEME_URI . '/assets/css/los' . self::SUFFIX . '.css', false, self::VERSION);
	}

	public function linkFrontJs(){
		wp_enqueue_script('los-slick-js', THEME_URI . '/assets/lib/slick/slick' . self::SUFFIX . '.js', ['jquery'], self::VERSION);
		wp_enqueue_script('los-main-js', THEME_URI . '/assets/js/los' . self::SUFFIX . '.js', ['jquery'], self::VERSION);
	}

	private function _requrieFiles(){
		$this->_requrieCommonFiles();
		$this->_requrieAdminFiles();
		$this->_requrieFrontFiles();
		
	}

	private function _isWoocommerceActive() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}
		return $is_active;
	}

	private function _requrieCommonFiles(){
		require_once LYMOS_THEME_DIR . '/lib/widgets/widgets.class.php';
		require_once LYMOS_THEME_DIR . '/lib/menu/menu.class.php';
		require_once LYMOS_THEME_DIR . '/lib/customize/customize.class.php';
		$this->_initCommonClass();
	}

	private function _requrieAdminFiles(){
		if(! is_admin()){
			return ;
		}
		
		$this->_initAdminClass();
	}

	private function _requrieFrontFiles(){
		if(is_admin()){
			return ;
		}
		require_once LYMOS_THEME_DIR . '/lib/header/header.class.php';
		require_once LYMOS_THEME_DIR . '/lib/footer/footer.class.php';
		require_once LYMOS_THEME_DIR . '/lib/content/content.class.php';
		require_once LYMOS_THEME_DIR . '/lib/page/page.class.php';

		if($this->_isWoocommerceActive()){
			require_once LYMOS_THEME_DIR . '/lib/woocommerce/product.class.php';
		}
		$this->_initFrontClass();
	}

	private function _initFrontClass(){
		new lymosHeader;
		new lymosFooter($this->widget_class);
		new lymosContent;
		new lymosPage;
		if($this->_isWoocommerceActive()){
			new lymosProduct;
		}
	}

	private function _initAdminClass(){
		
	}

	private function _initCommonClass(){
		$this->widget_class = new lymosWidgets;
		new lymosMenu;
		new lymosCustomize;
	}
}
new lymosTheme;