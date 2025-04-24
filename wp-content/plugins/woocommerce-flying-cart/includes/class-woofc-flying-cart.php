<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

class WOOFC_Flying_Cart {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'flying_cart_html' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart' ), 10, 1 );
		add_filter( 'woofc_classes', array( $this, 'flying_cart_classes' ), 20 );
	}

	/**
	 * Cart output.
	 */
	public function flying_cart_html() {
		// return if flying cart is disable on current page.
		if ( $this->display_by_page() !== true ) {
			return;
		}
		if ( $this->display_by_device() !== true ) {
			return;
		}
		// Load flying cart view.
		require_once WOOFC_PLUGIN_PATH . 'templates/flying-cart.php';
	}

	/**
	 * Get number of products added into the WooCommerce cart session.
	 *
	 * @return int
	 */
	public function get_products_count() {
		return apply_filters( 'woofc_get_cart_contents_count', WC()->cart->get_cart_contents_count() );
	}

	/**
	 * Run fragments with WooCommerce add to cart.
	 *
	 * @param array $fragments
	 * @return array
	 */
	public function add_to_cart( $fragments ) {
		wc_clear_notices();

		ob_start();
		do_action( 'woocommerce_before_cart' );
		do_action( 'woofc_cart_body' );
		$cart_body = ob_get_clean();

		$fragments['#woofc-cart-body']            = '<div class="woofc-cart__body" id="woofc-cart-body">' . $cart_body . '</div>';
		$fragments['#woofc-cart-total']           = '<span id="woofc-cart-total">' . woofc_get_cart_total() . '</span>';
		$fragments['#woofc-cart-trigger-counter'] = '<span id="woofc-cart-trigger-counter">' . $this->get_products_count() . '</span>';

		return $fragments;
	}

	/**
	 * Check whether cart display on currnt page or not.
	 *
	 * @since 1.0
	 * @return bool
	 */
	public function display_by_page() {

		// Not display woofc on selected page.
		$exclude_pages = get_option( 'woofc_exclude_on_pages', array() );
		if ( count( $exclude_pages ) != 0 ) {
			if ( in_array( get_the_ID(), $exclude_pages) ) {
				return false;
			}
		}
		// display woofc on selected page.
		$include_pages = get_option( 'woofc_include_on_pages', array() );
		if (  count( $include_pages ) !== 0 ) {
			return in_array( get_the_ID(), $include_pages );
		}
		// Display everwhere.
		if ( 'yes' === get_option( 'woofc_display_by_pages_filter' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Display flying cart by device.
	 *
	 * @since 1.1
	 * @return bool
	 */
	public function display_by_device() {
		// Hide flying cart on checkout and cart page.
		if ( is_cart() || is_checkout() ) {
			return false;
		}
		if ( wp_is_mobile() && 'yes' === get_option( 'woofc_display_on_mobile' ) ) {
			return true;
		}
		if ( ! wp_is_mobile() && 'yes' === get_option( 'woofc_display_on_desktop' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Filter flying cart classes.
	 *
	 * @since 1.1.8
	 *
	 * @param array $classes
	 * @return array
	 */
	public function flying_cart_classes( $classes ) {
		$cart_type = get_option( 'woofc_display_type' );
		$dark_mode = get_option( 'woofc_dark_mode_status' );

		if ( $cart_type ) {
			if ( 'slide-right' === $cart_type ) {
				$classes[] = 'woofc-slide-right';
			} else if ( 'slide-left' === $cart_type ) {
				$classes[] = 'woofc-slide-left';
			} else if  ( 'floating-box' === $cart_type ) {
				$classes[] = 'woofc-floating-box';
			}
		}

		if ( 'yes' === $dark_mode ) {
			$classes[] = 'woofc-dark-mode';
		}

		if ( 'yes' === get_option( 'woofc_hide_cart_when_empty' ) && WC()->cart->is_empty() ) {
			$classes[] = 'woofc--hide';
		}

		return $classes;
	}

}

$woofc_flying_cart = new WOOFC_Flying_Cart;
