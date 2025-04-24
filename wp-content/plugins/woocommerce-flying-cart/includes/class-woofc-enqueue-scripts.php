<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WOOFC_Enqueue_Scripts class responsable to load all the scripts and styles.
 */
class WOOFC_Enqueue_Scripts {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) , 200);
	}

	/**
	 * Load all the required frontend scripts
	 * @return void
	 * @since 1.0
	 */
	public function enqueue_scripts() {

		$display_type               = get_option( 'woofc_display_type' );
		$primary_background_color   = get_option( 'woofc_primary_background_color' );
		$primary_text_color         = get_option( 'woofc_primary_text_color' );
		$counter_background_color   = get_option( 'woofc_counter_background_color' );
		$counter_text_color         = get_option( 'woofc_counter_text_color' );
		$cart_trigger_location      = get_option( 'woofc_cart_trigger_location' );
		$cart_trigger_offset_x      = get_option( 'woofc_cart_trigger_offset_x' );
		$cart_trigger_offset_y      = get_option( 'woofc_cart_trigger_offset_y' );

		// Plugin frontend scripts
		wp_enqueue_style( 'woofc-style', WOOFC_PLUGIN_URL . 'assets/css/woofc-public-style.css', array(), WOOFC_PLUGIN_VER );
		wp_enqueue_script( 'woofc-script', WOOFC_PLUGIN_URL . 'assets/js/woofc-public-script.js', array(), WOOFC_PLUGIN_VER, true );
		wp_localize_script( 'woofc-script', 'woofcObj', array(
			'ajax_url'               => admin_url( 'admin-ajax.php' ),
			'wc_ajax_url'            => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'add_to_cart_animation'  => esc_html( get_option( 'woofc_add_to_cart_animation' ) ),
			'display_type'           => esc_html( $display_type ),
			'open_after_add_to_cart' => esc_html( get_option( 'woofc_cart_open_after_add_to_cart' ) ),
			'is_ajax_cart_loader'    => 'yes' === get_option( 'woofc_ajax_cart_loader_status' ) ? true : false,
			'hide_cart_when_empty'   => 'yes' === get_option( 'woofc_hide_cart_when_empty' ) ? true : false,
			'coupon_appled_text'     => esc_textarea( get_option( 'woofc_cart_coupon_applied_text' ) ),
			'coupon_removed_text'    => esc_textarea( get_option( 'woofc_cart_coupon_removed_text' ) ),
		) );

		// Public inline style
		$css = '';

		$css .= ':root {
			--woofc-notice-success-bg-color: ' . esc_html( get_option( 'woofc_notice_success_bg_color' ) ) . ';
			--woofc-notice-success-text-color: ' . esc_html( get_option( 'woofc_notice_success_text_color' ) ) . ';
			--woofc-notice-error-bg-color: ' . esc_html( get_option( 'woofc_notice_error_bg_color' ) ) . ';
			--woofc-notice-error-text-color: ' . esc_html( get_option( 'woofc_notice_error_text_color' ) ) . ';
		}';

		$css .= '.woofc .woofc-primary-bg-color {
			background-color: ' . esc_html( $primary_background_color ) . ' !important;
		}';
		$css .= '.woofc-primary-text-color {
			color: ' . esc_html( $primary_text_color ) . ' !important;
		}';
		$css .= '.woofc .woofc-cart-trigger__counter {
			background-color: ' . esc_html( $counter_background_color ) . ';
			color: ' . esc_html( $counter_text_color ) . ';
		}';

		if ( 'bl' === $cart_trigger_location ) {
			$css .= '.woofc {
				left: ' . intval( $cart_trigger_offset_x ) . 'px;
				bottom: ' . intval( $cart_trigger_offset_y ) . 'px;
			}
			.woofc-cart {
				left: 12px;
				bottom: 12px;
			}
			.woofc-cart-trigger__counter {
				right: -15px;
			}';
		} else {
			$css .= '.woofc {
				right: ' . intval( $cart_trigger_offset_x ) . 'px;
				bottom: ' . intval( $cart_trigger_offset_y ) . 'px;
			}
			.woofc-cart {
				right: 12px;
				bottom: 12px;
			}
			.woofc-cart-trigger__counter {
				left: -15px;
			}';
		}

		$css .= '#woofc-coupon-form input[type="submit"] {
			background-color: ' . esc_html( $primary_background_color ) . ' !important;
			color: ' . esc_html( $primary_text_color ) . ';
			border: 1px solid ' . esc_html( $primary_background_color ) . ' !important;
		}';

		if ( 'yes' === get_option( 'woofc_cart_hide_product_images' ) ) {
			$css .= '.woofc-product__img-wrapper {
				width: 24px;
				height: auto;
			}
			.woofc-product__close {
				top: 12px;
			}';
		}

		$css .= wp_kses_post( get_option( 'woofc_custom_css' ) );

		wp_add_inline_style( 'woofc-style', $css );
	}


} // end of class WOOFC_Enqueue_Scripts

$woofc_enqueue_scripts = new WOOFC_Enqueue_Scripts;
