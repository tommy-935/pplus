<?php

if ( ! function_exists( 'woofc_get_cart_total' ) ) {
	/**
	 * Get WooCommerce cart total.
	 *
	 * @since 1.1.8
	 *
	 * @return string WooCommerce formated price with currency symbol.
	 */
	function woofc_get_cart_total() {
		return apply_filters( 'woofc_get_cart_total', WC()->cart->get_total() );
	}
}

if ( ! function_exists( 'woofc_get_cart_products' ) ) {
	/**
	 * Get WooCommerce cart products.
	 *
	 * @since 1.1.8
	 *
	 * @return array Array of cart items
	 */
	function woofc_get_cart_products() {
		return WC()->cart->get_cart();
	}
}


if ( ! function_exists( 'woofc_is_cart_empty' ) ) {
	/**
	 * Checks if the cart is empty.
	 *
	 * @since 1.1.8
	 *
	 * @return bool
	 */
	function woofc_is_cart_empty() {
		return WC()->cart->is_empty();
	}
}

if ( ! function_exists( 'woofc_woocommerce_quantity_input' ) ) {

	/**
	 * Output the quantity input.
	 *
	 * @since 1.2.1
	 *
	 * @param  array           $args    Args for the input.
	 * @param  WC_Product|null $product WooCommerce product object.
	 * @param  boolean         $echo    Whether to return or echo|string.
	 */
	function woofc_woocommerce_quantity_input( $args = array(), $product = null, $echo = true ) {
		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		$defaults = array(
			'input_name'  => 'quantity',
			'input_value' => '1',
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', -1, $product ),
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
			'step'        => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
			'pattern'     => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
			'inputmode'   => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
		);

		$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

		// Apply sanity to min/max args - min cannot be lower than 0.
		$args['min_value'] = max( $args['min_value'], 0 );
		$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

		// Max cannot be lower than min if defined.
		if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
			$args['max_value'] = $args['min_value'];
		}

		ob_start();

		if ( $args['max_value'] && $args['min_value'] === $args['max_value'] ) {
			?>
			<div class="hidden">
				<input type="hidden" class="qty" name="<?php echo esc_attr( $args['input_name'] ); ?>" value="<?php echo esc_attr( $args['min_value'] ); ?>" />
			</div>
			<?php
		} else {
			?>
			<div>
				<input
					type="number"
					step="<?php echo esc_attr( $args['step'] ); ?>"
					min="<?php echo esc_attr( $args['min_value'] ); ?>"
					max="<?php echo esc_attr( 0 < $args['max_value'] ? $args['max_value'] : '' ); ?>"
					name="<?php echo esc_attr( $args['input_name'] ); ?>"
					value="<?php echo esc_attr( $args['input_value'] ); ?>"
					size="4"
					inputmode="<?php echo esc_attr( $args['inputmode'] ); ?>" />
			</div>
			<?php
		}

		if ( $echo ) {
			echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return ob_get_clean();
		}
	}
}

/**
 * Get availabile WooCommerce coupons WC_Coupon object.
 *
 * @since 1.6.0
 *
 * @return array Array of WC_Coupon.
 */
function woofc_get_coupons() {
	$coupon_posts = get_posts( array(
		'posts_per_page'   => -1,
		'orderby'          => 'name',
		'order'            => 'asc',
		'post_type'        => 'shop_coupon',
		'post_status'      => 'publish',
	) );

	$coupons = array(); // Initializing

	foreach( $coupon_posts as $coupon_post) {
		$coupons[] = new WC_Coupon( $coupon_post->post_name );
	}

	return $coupons;
}

/**
 * Check whether coupon code is valid or not.
 *
 * @since 1.6.0
 *
 * @param string $coupon_code Coupon code.
 * @return bool True if valid, false otherwise.
 */
function woofc_is_coupon_valid( $coupon_code ) {
	$coupon    = new WC_Coupon( $coupon_code );
	$discounts = new WC_Discounts( WC()->cart );
	$response  = $discounts->is_coupon_valid( $coupon );

	return is_wp_error( $response ) ? false : true;
}
