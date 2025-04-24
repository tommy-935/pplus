<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Admin menu class
 */
class WOOFC_Admin_Menus {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * Add admin meni
	 */
	public function admin_menu() {
		add_menu_page(
			esc_html__( 'Woo Flying Cart', 'woo-flying-cart' ),
			esc_html__( 'Woo Flying Cart', 'woo-flying-cart' ),
			'manage_options',
			'woocommerce-flying-cart',
			array( $this, 'admin_setting_page' ),
			'dashicons-cart',
			58
		);
		add_submenu_page(
			'woocommerce-flying-cart',
			esc_html__( 'Plugin Support', 'woo-flying-cart' ),
			esc_html__( 'Plugin Support', 'woo-flying-cart') ,
			'manage_options',
			'woocommerce-flying-cart_plugin-support',
			array( $this, 'admin_plugin_support_page' )
		);
	}

	/**
	 * Admin setting main page
	 */
	public function admin_setting_page() {
		// Get current tab
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

		require_once WOOFC_PLUGIN_PATH . 'includes/admin/views/html-admin-settings.php';
	}

	public function admin_plugin_support_page() {
		require_once WOOFC_PLUGIN_PATH . 'includes/admin/views/html-admin-plugin-support-page.php';
	}

}

$woofc_admin_menus = new WOOFC_Admin_Menus;
