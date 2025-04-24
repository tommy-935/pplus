<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WOOFC_Migration {

	public function __construct() {
		add_action( 'woofc_before_install', array( $this, 'migration_version_1_1_5' ), 1 );
	}

	/**
	 * Migration version 1.1.5
	 *
	 * @return void
	 */
	public function migration_version_1_1_5() {
		if ( get_option( 'woofc_cart_button_two' ) ) {
			$checkout_page_id = get_option( 'woofc_cart_button_two' );

			// Add checkout button option
			add_option( 'woofc_checkout_button', $checkout_page_id );

			// Delete old cart and checkout button options.
			delete_option( 'woofc_cart_button_one' );
			delete_option( 'woofc_cart_button_two' );
		}
	}
}

new WOOFC_Migration;
