<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Admin notice class
 */
class WOOFC_Admin_Notices {

	/**
	 * Admin screen
	 *
	 * @var string
	 */
	public $screen = false;

	public function __construct() {
		add_action( 'current_screen', array( $this, 'get_current_screen' ) );

		add_action( 'admin_notices', array( $this, 'follow_us_notice' ), 50 );
		add_action( 'admin_notices', array( $this, 'wc_add_to_cart_redirection_message' ), 50 );
		add_action( 'wp_ajax_woofc_admin_noice_hide_follow_us', array( $this, 'admin_noice_hide_follow_us' ) );
		add_action( 'wp_ajax_nopriv_woofc_admin_noice_hide_follow_us', array( $this, 'admin_noice_hide_follow_us' ) );
	}

	public function follow_us_notice() {
		if ( 'toplevel_page_woocommerce-flying-cart' !== $this->screen ) {
			return;
		}
		if ( 'no' === get_option( 'woofc_admin_notice_follow_us' ) ) {
			return;
		}

		$class      = 'notice notice-info is-dismissible woofc-hide-follow-us';
		$message    = __( 'Follow us on Envato and get the lastest updates. <a href="https://codecanyon.net/user/wecreativez/follow">Follow us now</a>', 'woo-flying-cart' );
		printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
	}

	public function admin_noice_hide_follow_us() {
		if ( ! is_admin() ) {
			return;
		}

		update_option( 'woofc_admin_notice_follow_us', 'no' );
		wp_die();
	}

	/**
	 * Add to cart redirection message.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function wc_add_to_cart_redirection_message() {
		if ( 'yes' !== get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			return;
		}

		$class    = 'notice notice-error';
		$message  = '<p>' . __( '<strong>WooCommerce Flying Cart</strong> is activate but not working properly. Please disable the <code>Add to cart redirection</code> from WooCommerce settings.', 'woo-flying-cart' ) . '</p>';
		$message .= '<p>' . __( 'Disable the <code>Add to cart redirection</code>, go to the <strong>WooCommerce > Settings > Products (tab) > Add to cart behaviour > Redirect to the cart page after successful addition</strong> and disable the option and save the changes.', 'woo-flying-cart' ) . '</p>';

		printf( '<div class="%s">%s</div>', esc_attr( $class ), wp_kses_post( $message ) );
	}

	public function get_current_screen() {
		$currentScreen = get_current_screen();

		if( $currentScreen ) {
			$this->screen = $currentScreen->id;
		}
	}

}

$woofc_admin_notices = new WOOFC_Admin_Notices;
