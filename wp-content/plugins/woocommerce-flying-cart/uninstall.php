<?php
/**
 * Runs on Uninstall.
 */

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if accessed directly
}

// Delete all plugin settings, tables, etc.
if ( 'yes' === get_option( 'woofc_delete_all' ) ) :
	global $wpdb;

	// Load install class
	require_once 'includes/class-woofc-install.php';

	// Delete plugin options
	foreach ( WOOFC_Install::default_options() as $name => $value ) {
		if ( get_option( $name ) ) {
			delete_option( $name );
		}
	}

	// Admin notices
	delete_option( 'woofc_admin_notice_follow_us' );

endif;
