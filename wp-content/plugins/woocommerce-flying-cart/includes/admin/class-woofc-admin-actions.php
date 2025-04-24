<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Admin actions class
 */
class WOOFC_Admin_Actions {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'reset_settings' ) );
	}

	/**
	 * Reset settings to default.
	 */
	public function reset_settings() {

		if ( ! isset( $_GET['woofc_reset_settings'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		WOOFC_Settings::reset_options();

		delete_option( 'woofc_admin_notice_follow_us' );
		wp_safe_redirect( wp_get_referer() );
	}

}

$woofc_admin_actions = new WOOFC_Admin_Actions;
