<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

final class WOOFC_Init {

	public function init() {

		// Before init action.
		do_action( 'woofc_before_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();
		$this->init_hooks();

		// After init action.
		do_action( 'woofc_after_init' );

	}

	public function init_hooks() {
		// Plugin page setting link on "Install plugin page"
		add_filter( 'plugin_action_links_'.plugin_basename( WOOFC_PLUGIN_FILE ), array( $this, 'plugin_page_settings_link' ) );

		// Load files after the WooCommerce init
		add_action( 'woocommerce_init', array( $this, 'includes' ), 20 );

		add_action( 'init', array( $this, 'upgrader_process_complete' ), 10, 2 );
	}

	/**
	 * Includes plugin files.
	 */
	public function includes() {
		// Core
		require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-settings.php';

		// Deprecated

		// Include files
		require_once WOOFC_PLUGIN_PATH . 'includes/woofc-cart-functions.php';
		require_once WOOFC_PLUGIN_PATH . 'includes/woofc-template-functions.php';
		require_once WOOFC_PLUGIN_PATH . 'includes/woofc-template-hooks.php';
		require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-ajax.php';
		require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-enqueue-scripts.php';
		require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-flying-cart.php';

		// Debug class
		require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-debug.php';

		if ( is_admin() ) {
			// Includes admin functions

			// Includes admin class
			require_once WOOFC_PLUGIN_PATH . 'includes/admin/class-woofc-admin-notices.php';
			require_once WOOFC_PLUGIN_PATH . 'includes/admin/class-woofc-admin-actions.php';
			require_once WOOFC_PLUGIN_PATH . 'includes/admin/class-woofc-admin-menu.php';
			require_once WOOFC_PLUGIN_PATH . 'includes/admin/class-woofc-admin-fields.php';
			require_once WOOFC_PLUGIN_PATH . 'includes/admin/class-woofc-admin-enqueue-scripts.php';
			require_once WOOFC_PLUGIN_PATH . 'includes/admin/class-woofc-admin-plugin-updater.php';

		}
	}

	/**
	 * Load Localisation files.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woo-flying-cart', false, plugin_basename( dirname( WOOFC_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Adding a Settings link to plugin
	 * @since 1.3
	 */
	public function plugin_page_settings_link( $links ) {
		$links[] = '<a href="' .
			admin_url( 'admin.php?page=woocommerce-flying-cart' ) .
			'">' . esc_html__( 'Settings', 'woo-flying-cart' ) . '</a>';
		return $links;
	}

	/**
	 * This function runs when plugin update.
	 */
	public function upgrader_process_complete() {
		if ( WOOFC_PLUGIN_VER != get_option( 'woofc_version' ) ) {
			// Run installation.
			woofc_plugin_install();
			// update current version.
			update_option( 'woofc_version', WOOFC_PLUGIN_VER );
		}
	}

}
