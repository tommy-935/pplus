<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WOOFC_Admin_Enqueue_Scripts {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 200 );
	}

	/**
	 * Load scripts and styles on only plugin screens.
	 *
	 * @param string $hook
	 */
	public function enqueue_scripts( $hook ) {
		if ( $this->is_admin_screen( $hook ) !== true ) {
			return;
		}

		// Select2
		wp_enqueue_style( 'select2', WOOFC_PLUGIN_URL . 'assets/libraries/select2/select2.min.css', array(), '4.0.10' );
		wp_enqueue_script( 'select2', WOOFC_PLUGIN_URL . 'assets/libraries/select2/select2.min.js', array(), '4.0.10', true );

		// Load plugin scripts
		wp_enqueue_style( 'woofc-admin-style', WOOFC_PLUGIN_URL . 'assets/css/woofc-admin-style.css', array(), WOOFC_PLUGIN_VER );
		wp_enqueue_script( 'woofc-admin-script', WOOFC_PLUGIN_URL . 'assets/js/woofc-admin-script.js', array(), WOOFC_PLUGIN_VER, true );
		wp_localize_script( 'woofc-admin-script', 'woofcAdminObj', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		) );

	}

	/**
	 * Check whether current screen is plugin admin screen or not.
	 *
	 * @param string $hook
	 * @return boolean Return true if current screen is plugin admin screen.
	 */
	private function is_admin_screen( $hook ) {
		if ( 'toplevel_page_woocommerce-flying-cart' === $hook
		|| 'woo-flying-cart_page_woocommerce-flying-cart_plugin-support' === $hook ) {
			return true;
		}

		return false;
	}

}

$woofc_admin_enqueue_scripts = new WOOFC_Admin_Enqueue_Scripts;
