<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Admin no WooCommerce class
 */
class WOOFC_Admin_No_WooCommerce {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		add_menu_page(
			esc_html__( 'Woo Flying Cart', 'woo-flying-cart' ),
			esc_html__( 'Woo Flying Cart', 'woo-flying-cart' ),
			'manage_options',
			'woofc-flying-cart',
			array( $this, 'admin_no_woocommerce_page' ),
			'dashicons-cart'
		);
	}

	/**
	 * Admin no WooCommerce main page.
	 */
	public function admin_no_woocommerce_page() {
		require_once WOOFC_PLUGIN_PATH . 'includes/admin/views/html-no-woocommerce.php';
	}

}

$woofc_admin_no_woocommerce = new WOOFC_Admin_No_WooCommerce;
