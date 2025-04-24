<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WOOFC_AJAX extends WC_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Add custom ajax events here
	 */
	public static function add_ajax_events() {
		// woocommerce_EVENT => nopriv
		$ajax_events = array(
			'woofc_add_to_cart'             => true,
			'woofc_remove_product'          => true,
			'woofc_update_product_quantity' => true,
			'woofc_remove_coupon'           => true,
			'woofc_apply_coupon'            => true,
			'woofc_update_shipping_method'  => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				// WC AJAX can be used for frontend ajax requests
				add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * AJAX added to cart.
	 *
	 * @since 1.1.9
	 */
	public static function woofc_add_to_cart() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['action'] ) || 'woofc_add_to_cart' !== $_POST['action'] || ! isset( $_POST['add-to-cart'] ) ) {
			die();
		}

		// Get woocommerce error notice.
		$error = wc_get_notices( 'error' );
		$html  = '';

		if ( $error ) {
			// Print notice.
			ob_start();

			foreach ( $error as $value ) {
				wc_print_notice( $value, 'error' );
			}

			$js_data = array(
				'error' => ob_get_clean(),
			);

			wc_clear_notices(); // Clear other notice.
			wp_send_json( $js_data );
		} else {
			// Trigger action for added to cart in ajax.
			do_action( 'woocommerce_ajax_added_to_cart', intval( $_POST['add-to-cart'] ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals, WordPress.Security.NonceVerification.Missing

			wc_clear_notices(); // Clear other notice.
			WC_AJAX::get_refreshed_fragments();
		}

		die();
	}

	public static function woofc_update_product_quantity() {
		$cart_item_key  = sanitize_text_field( $_POST['cart_item_key'] );
		$quantity       = intval( $_POST['quantity'] );

		if( $cart_item_key ) {
			WC()->cart->set_quantity( $cart_item_key, $quantity );

			// Response
			die( json_encode( parent::get_refreshed_fragments() ) );
		}

	}

	/**
	 * Removes item from the cart then returns a new fragment
	 */
	public static function woofc_remove_product() {
		$cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );

		if( $cart_item_key ) {
			WC()->cart->remove_cart_item( $cart_item_key );
			// Response
			die( json_encode( parent::get_refreshed_fragments() ) );
		}
	}

	/**
	 * Removes coupon from the cart then returns a new fragment
	 */
	public static function woofc_remove_coupon() {
		$coupon_code = sanitize_text_field( $_POST['coupon_code'] );

		WC()->cart->remove_coupon( $coupon_code );
		WC()->cart->calculate_totals();

		die( json_encode( parent::get_refreshed_fragments() ) );
	}

	/**
	 * Apply coupon from the cart then returns a new fragment
	 */
	public static function woofc_apply_coupon() {
		$coupon_code = sanitize_text_field( $_POST['coupon_code'] );
		// Get the coupon
		$the_coupon  = new WC_Coupon( $coupon_code );

		// Check it can be used with cart
		if ( ! $the_coupon->is_valid() ) {
			die( json_encode( array( 'coupon_error' => $the_coupon->get_error_message() ) ) );
		} else {
			WC()->cart->add_discount( $coupon_code );
			WC()->cart->calculate_totals();
			die( json_encode( parent::get_refreshed_fragments() ) );
		}
	}

	/**
	 * AJAX update shipping method on cart page.
	 */
	public static function woofc_update_shipping_method() {
		if ( ! isset( $_POST['shipping_method'] ) ) {
			return;
		}

		$shipping_method = sanitize_text_field( $_POST['shipping_method'] );

		WC()->session->set('chosen_shipping_methods', array( $shipping_method ) );
		WC()->cart->calculate_totals();
		die( json_encode( parent::get_refreshed_fragments() ) );
	}


}

WOOFC_AJAX::init();
