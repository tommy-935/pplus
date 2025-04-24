<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'woofc_get_template' ) ) {
	/**
	 * Get plugin templates.
	 *
	 * @since 1.5.0
	 *
	 * @param string $template_name Relative path.
	 * @param array  $args          Arguments.
	 * @return string Template path.
	 */
	function woofc_get_template( $template_name, $args = array() ) {
		extract( $args );

		$template_file = get_stylesheet_directory() . '/woofc-templates/' . $template_name;

		if ( file_exists( $template_file ) ) {
			$template = $template_file;
		} else {
			$template = trailingslashit( WOOFC_PLUGIN_PATH ) . 'templates/' . $template_name;
		}

		require apply_filters( 'woofc_get_template', $template, $template_name, $args );
	}
}

if ( ! function_exists( 'woofc_get_template_html' ) ) {
	/**
	 * Get plugin templates.
	 *
	 * @since 1.5.0
	 *
	 * @param string $template_name Relative path.
	 * @param array  $args          Arguments.
	 * @return string Template HTML.
	 */
	function woofc_get_template_html( $template_name, $args = array() ) {
		ob_start();
		woofc_get_template( $template_name, $args );
		return apply_filters( 'woofc_get_template_html', ob_get_clean(), $template_name, $args );
	}
}

if ( ! function_exists( 'woofc_the_cart_footer_call_to_actions' ) ) {
	/**
	 * Footer call to actions.
	 *
	 * @since 1.1.8
	 *
	 * @return void
	 */
	function woofc_the_cart_footer_call_to_actions() {
		$cart_button     = get_option( 'woofc_cart_button' );
		$checkout_button = get_option( 'woofc_checkout_button' );

		woofc_get_template(
			'call-to-actions.php',
			array(
				'cart_button'     => $cart_button,
				'checkout_button' => $checkout_button,
			)
		);
	}
}

if ( ! function_exists( 'woofc_the_cart_products_loop' ) ) {
	/**
	 * Display the cart products loop.
	 *
	 * @since 1.1.8
	 *
	 * @return void
	 */
	function woofc_the_cart_products_loop() {
		$products = woofc_get_cart_products();

		if ( $products ) {
			woofc_get_template( 'products.php', array( 'products' => $products ) );
		}
	}
}

if ( ! function_exists( 'woofc_the_cart_related_products' ) ) {
	/**
	 * Related products.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	function woofc_the_cart_related_products() {
		$related_products_id = array();
		$products            = woofc_get_cart_products();

		if ( $products && 'yes' === get_option( 'woofc_cart_related_products_status' ) ) {
			foreach ( $products as $cart_item_key => $cart_item ) {
				$product_id       = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				$related_products = wc_get_related_products( $product_id );

				if ( $related_products ) {
					foreach ( $related_products as $related_product ) {
						if ( in_array( $related_product, wp_list_pluck( $products, 'product_id' ) ) ) {
							continue;
						}

						$related_products_id[ $related_product ] = wc_get_product( $related_product );
					}
				}

			}

			if ( ! $related_products_id ) {
				return;
			}

			shuffle( $related_products_id );

			$related_products_id = array_slice( $related_products_id, 0, get_option( 'woofc_cart_related_products_per_row' ) );

			woofc_get_template( 'related-products.php', array( 'related_products' => $related_products_id ) );
		}
	}
}

if ( ! function_exists( 'woofc_the_cart_cross_sells_products' ) ) {
	/**
	 * Cross sells products.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	function woofc_the_cart_cross_sells_products() {
		$cross_sells_products_id = array();
		$products            = woofc_get_cart_products();

		if ( $products && 'yes' === get_option( 'woofc_cart_cross_sells_products_status' ) ) {
			foreach ( $products as $cart_item_key => $cart_item ) {
				$product_id       = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				$product          = wc_get_product( $product_id );
				$cross_sells_products = $product->get_cross_sell_ids( $product_id );

				if ( $cross_sells_products ) {
					foreach ( $cross_sells_products as $cross_sells_product ) {
						if ( in_array( $cross_sells_product, wp_list_pluck( $products, 'product_id' ) ) ) {
							continue;
						}

						$cross_sells_products_id[ $cross_sells_product ] = wc_get_product( $cross_sells_product );
					}
				}

			}

			if ( ! $cross_sells_products_id ) {
				return;
			}

			shuffle( $cross_sells_products_id );

			$cross_sells_products_id = array_slice( $cross_sells_products_id, 0, get_option( 'woofc_cart_cross_sells_products_per_row' ) );

			woofc_get_template( 'cross-sells-products.php', array( 'cross_sells_products' => $cross_sells_products_id ) );
		}
	}
}

if ( ! function_exists( 'woofc_the_cart_coupon_form' ) ) {
	/**
	 * Display the cart coupon form.
	 *
	 * @since 1.1.8
	 *
	 * @return void
	 */
	function woofc_the_cart_coupon_form() {
		if ( woofc_is_cart_empty() ) {
			return;
		}

		if ( wc_coupons_enabled() ) {
			woofc_get_template( 'coupon-form.php', array(
				'coupons' => woofc_get_coupons(),
			) );
		}
	}
}

if ( ! function_exists( 'woofc_the_cart_review' ) ) {
	/**
	 * Dispay the cart review.
	 *
	 * @since 1.1.8
	 *
	 * @return void
	 */
	function woofc_the_cart_review() {
		if ( woofc_is_cart_empty() ) {
			return;
		}

		woofc_get_template( 'cart-review.php' );
	}
}

if ( ! function_exists( 'woofc_the_empty_cart_message' ) ) {
	/**
	 * Display the empty cart message.
	 *
	 * @since 1.1.8
	 *
	 * @return void
	 */
	function woofc_the_empty_cart_message() {
		if ( ! woofc_is_cart_empty() ) {
			return;
		}

		woofc_get_template( 'cart-empty.php' );
	}
}
