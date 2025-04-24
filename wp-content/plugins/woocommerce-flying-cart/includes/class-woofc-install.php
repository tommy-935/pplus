<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Plugin install class
 */
class WOOFC_Install {

	/**
	 * Plugin install.
	 */
	public static function install() {
		do_action( 'woofc_before_install' );

		self::register_settings();

		do_action( 'woofc_after_install' );

		// update current version.
		update_option( 'woofc_version', WOOFC_PLUGIN_VER );
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public static function register_settings() {
		// Load admin settings.
		require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-settings.php';

		WOOFC_Settings::add_options();
	}

	/**
	 * Get current version of the plugin.
	 *
	 * @since 1.0
	 * @return float
	 */
	public static function get_current_version() {
		$plugin_data = get_plugin_data( WOOFC_PLUGIN_FILE );
		return $plugin_data['Version'];
	}
}
