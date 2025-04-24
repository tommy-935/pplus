<?php

/**
 * Register settings.
 *
 * @author  WeCreativez
 * @package WooCommerce Flying Cart
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-settings-api.php';

/**
 * Class WOOFC_Settings.
 *
 * @since 2.0.0
 */
class WOOFC_Settings extends WOOFC_Settings_API {

	/**
	 * Class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		parent::init();

		add_filter( 'woofc_register_settings', array( $this, 'register_plugin_settings' ) );
	}

	/**
	 * Register plugin settings.
	 *
	 * @since 2.0.0
	 *
	 * @param array $settings Settings array.
	 */
	public function register_plugin_settings( $settings ) {

		// Appearance settings.
		$settings[] = array(
			'id'     => 'woofc_appearance_settings',
			'title'  => __( 'Appearance Settings', 'woo-flying-cart' ),
			'fields' => array(
				array(
					'id'      => 'woofc_display_type',
					'type'    => 'select',
					'title'   => __( 'Display Type', 'woo-flying-cart' ),
					'desc'    => __( 'Select the layout accoring to your need.', 'woo-flying-cart' ),
					'default' => 'floating-box',
					'options' => array(
						'floating-box' => __( 'Floating Box', 'woo-flying-cart' ),
						'slide-left'   => __( 'Slide ( Left )', 'woo-flying-cart' ),
						'slide-right'  => __( 'Slide ( Right )', 'woo-flying-cart' ),
					),
				),
				array(
					'id'      => 'woofc_primary_background_color',
					'type'    => 'color',
					'title'   => __( 'Primary Background Color', 'woo-flying-cart' ),
					'desc'    => array(
						sprintf( __( 'Default %s', 'woo-flying-cart' ), '<code>#24303F</code>'),
						__( 'Select the primary background color.', 'woo-flying-cart' ),
					),
					'classes' => 'woofc-colorpicker',
					'default' => '#24303F',
				),
				array(
					'id'      => 'woofc_primary_text_color',
					'type'    => 'color',
					'title'   => __( 'Primary Text Color', 'woo-flying-cart' ),
					'desc'    => array(
						__( 'Select the primary text color.', 'woo-flying-cart' ),
						sprintf( __( 'Default %s', 'woo-flying-cart' ), '<code>#ffffff</code>' ),
					),
					'classes' => 'woofc-colorpicker',
					'default' => '#ffffff',
				),
				array(
					'id'      => 'woofc_counter_background_color',
					'type'    => 'color',
					'title'   => __( 'Counter Background Color', 'woo-flying-cart' ),
					'desc'    => array(
						sprintf( __( 'Default %s', 'woo-flying-cart' ), '<code>#ff1744</code>'),
						__( 'Select the counter background color.', 'woo-flying-cart' ),
					),
					'classes' => 'woofc-colorpicker',
					'default' => '#ff1744',
				),
				array(
					'id'      => 'woofc_counter_text_color',
					'type'    => 'color',
					'title'   => __( 'Counter Text Color', 'woo-flying-cart' ),
					'desc'    => array(
						__( 'Select the counter text color.', 'woo-flying-cart' ),
						sprintf( __( 'Default %s', 'woo-flying-cart' ), '<code>#ffffff</code>' ),
					),
					'classes' => 'woofc-colorpicker',
					'default' => '#ffffff',
				),
				array(
					'id'      => 'woofc_dark_mode_status',
					'type'    => 'checkbox',
					'title'   => __( 'Dark Mode', 'woo-flying-cart' ),
					'desc'    => __( 'Enable/ Disable', 'woo-flying-cart' ) . ' ( ' . __( 'Enable/ Disable the dark mode for flying cart.', 'woo-flying-cart' ) . ' )',
					'default' => 'no',
				),
				array(
					'id'      => 'woofc_ajax_cart_loader_status',
					'type'    => 'checkbox',
					'title'   => __( 'AJAX Cart Loader', 'woo-flying-cart' ),
					'desc'    => __( 'Enable/ Disable', 'woo-flying-cart' ) . ' ( ' . __( 'Enable, if your AJAX request taking time to response.', 'woo-flying-cart' ) . ' )',
					'default' => 'no',
				),
				array(
					'id'      => 'woofc_add_to_cart_animation',
					'type'    => 'select',
					'title'   => __( 'Add To Cart Animation', 'woo-flying-cart' ),
					'desc'    => __( 'Select add to cart animation.', 'woo-flying-cart' ),
					'default' => 'tada',
					'options' => array(
						'bounce'      => __( 'Bounce', 'woo-flying-cart' ),
						'flash'       => __( 'Flash', 'woo-flying-cart' ),
						'rubber-band' => __( 'Rubber Band', 'woo-flying-cart' ),
						'shake'       => __( 'Shake', 'woo-flying-cart' ),
						'swing'       => __( 'Swing', 'woo-flying-cart' ),
						'tada'        => __( 'Tada', 'woo-flying-cart' ),
						'jello'       => __( 'Jello', 'woo-flying-cart' ),
					),
				),
				array(
					'id'       => 'woofc_cart_icon_selector',
					'type'     => 'woofc_cart_icon_selector',
					'title'    => __( 'Cart Icon', 'woo-flying-cart' ),
					'desc'     => __( 'Select cart icon.', 'woo-flying-cart' ),
					'callback' => function() {
						?>
						<table>
							<tr>
								<td>
									<div class="woofc-admin-cart-icons">
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-2.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-2.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-3.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-3.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-4.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-4.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-5.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-5.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-6.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-6.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-7.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-7.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-8.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-8.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-9.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-9.svg' ); ?>" alt="//">
										</div>
										<div data-woofc-cart-icon-url="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-10.svg' ); ?>">
											<img src="<?php echo esc_url( WOOFC_PLUGIN_URL . 'assets/images/cart-10.svg' ); ?>" alt="//">
										</div>
									</div>
								</td>
							</tr>
						</table>
						<?php
					},
					'register' => false,
				),
				array(
					'id'      => 'woofc_cart_icon',
					'type'    => 'file',
					'desc'    => __( 'Or you can select cart image. Only jpg, png allowed.', 'woo-flying-cart' ),
					'default' => WOOFC_PLUGIN_URL . 'assets/images/cart.svg',
				),
			),
		);

		// Notice settings.
		$settings[] = array(
			'id'     => 'woofc_notice_settings',
			'title'  => __( 'Notice Settings', 'woo-flying-cart' ),
			'page'   => 'woofc_appearance_settings',
			'fields' => array(
				array(
					'id'       => 'woofc_notice_success_settings',
					'type'     => 'woofc_custom',
					'title'    => __( 'Success Notice', 'woo-flying-cart' ),
					'callback' => function() {
						?>
						<div>
							<p>
								<input type="text" class="woofc-colorpicker" name="woofc_notice_success_bg_color" value="<?php echo self::get_option( 'woofc_notice_success_bg_color' ); ?>">
								<span style="color: #646970;"><?php esc_html_e( 'Success notice background color.', 'woo-flying-cart' ); ?></span>
							</p>
							<p>
								<input type="text" class="woofc-colorpicker" name="woofc_notice_success_text_color" value="<?php echo self::get_option( 'woofc_notice_success_text_color' ); ?>">
								<span style="color: #646970;"><?php esc_html_e( 'Success notice text color.', 'woo-flying-cart' ); ?></span>
							</p>
						</div>
						<?php
					},
					'register' => array(
						array(
							'id'      => 'woofc_notice_success_bg_color',
							'type'    => 'color',
							'default' => '#00c853',
						),
						array(
							'id'      => 'woofc_notice_success_text_color',
							'type'    => 'color',
							'default' => '#000000',
						),
					),
				),
				array(
					'id'       => 'woofc_notice_error_settings',
					'type'     => 'woofc_custom',
					'title'    => __( 'Error Notice', 'woo-flying-cart' ),
					'callback' => function() {
						?>
						<div>
							<p>
								<input type="text" class="woofc-colorpicker" name="woofc_notice_error_bg_color" value="<?php echo self::get_option( 'woofc_notice_error_bg_color' ); ?>">
								<span style="color: #646970;"><?php esc_html_e( 'Error notice background color.', 'woo-flying-cart' ); ?></span>
							</p>
							<p>
								<input type="text" class="woofc-colorpicker" name="woofc_notice_error_text_color" value="<?php echo self::get_option( 'woofc_notice_error_text_color' ); ?>">
								<span style="color: #646970;"><?php esc_html_e( 'Error notice text color.', 'woo-flying-cart' ); ?></span>
							</p>
						</div>
						<?php
					},
					'register' => array(
						array(
							'id'      => 'woofc_notice_error_bg_color',
							'type'    => 'color',
							'default' => '#d50000',
						),
						array(
							'id'      => 'woofc_notice_error_text_color',
							'type'    => 'color',
							'default' => '#ffffff',
						),
					),
				),
			),
		);

		// Basic Settings.
		$settings[] = array(
			'id'     => 'woofc_basic_settings',
			'title'  => __( 'Basic Settings', 'woo-flying-cart' ),
			'fields' => array(
				array(
					'id'         => 'woofc_cart_trigger_offset_x',
					'type'       => 'number',
					'title'      => __( 'Cart Trigger X-axis Offset', 'woo-flying-cart' ),
					'desc'       => array(
						__( 'Enter the value of x-axis ( horizontal ) spacing.', 'woo-flying-cart' ),
						__( 'In px ( pixels ) only. Default 12px.', 'woo-flying-cart' ),
					) ,
					'default'    => '12',
					'classes'    => 'small-text',
					'attributes' => array(
						'step'  => '1',
						'min'   => '0',
						'max'   => '200',
					),
				),
				array(
					'id'         => 'woofc_cart_trigger_offset_y',
					'type'       => 'number',
					'title'      => __( 'Cart Trigger Y-axis Offset', 'woo-flying-cart' ),
					'desc'       => array(
						__( 'Enter the value of x-axis ( horizontal ) spacing.', 'woo-flying-cart' ),
						__( 'In px ( pixels ) only. Default 12px.', 'woo-flying-cart' ),
					),
					'default'    => '12',
					'classes'    => 'small-text',
					'attributes' => array(
						'step'  => '1',
						'min'   => '0',
						'max'   => '200',
					),
				),
				array(
					'id'      => 'woofc_cart_trigger_location',
					'type'    => 'select',
					'title'   => __( 'Cart Trigger Location', 'woo-flying-cart' ),
					'desc_'   => __( 'Select the cart trigger or button location.', 'woo-flying-cart' ),
					'default' => 'br',
					'options' => array(
						'br' => __( 'Bottom Right', 'woo-flying-cart' ),
						'bl' => __( 'Bottom Left', 'woo-flying-cart' ),
					),
				),
				array(
					'id'      => 'woofc_hide_cart_when_empty',
					'type'    => 'select',
					'title'   => __( 'Hide Cart When Empty', 'woo-flying-cart' ),
					'desc'    => __( 'Select yes if you want to hide flying cart and cart icon when the cart is empty.', 'woo-flying-cart' ),
					'default' => 'no',
					'options' => array(
						'yes' => __( 'Yes', 'woo-flying-cart' ),
						'no'  => __( 'No', 'woo-flying-cart' ),
					),
				),
				array(
					'id'        => 'woofc_display_on_mobile',
					'type'      => 'checkbox',
					'title'     => __( 'Display On Mobile', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'Enable this option, if you want to display the flying cart on mobile & tablet.', 'woo-flying-cart' ),
					'default'   => 'yes',
				),
				array(
					'id'        => 'woofc_display_on_desktop',
					'type'      => 'checkbox',
					'title'     => __( 'Display On Desktop', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'Enable this option, if you want to display the flying cart on desktop & laptop.', 'woo-flying-cart' ),
					'default'   => 'yes',
				),
				array(
					'id'        => 'woofc_display_by_pages_filter',
					'type'      => 'checkbox',
					'title'     => __( 'Display By Pages', 'woo-flying-cart' ),
					'desc'      => __( 'Everywhere', 'woo-flying-cart' ),
					'tooltip'   => __( 'Enable this option, if you want to display the flying cart on all pages.', 'woo-flying-cart' ),
					'default'   => 'yes',
				),
				array(
					'id'       => 'woofc_include_on_pages',
					'type'     => 'select',
					'desc'     => __( 'Display floating cart only on selected pages.', 'woo-flying-cart' ),
					'default'  => array(),
					'classes'  => 'regular-text woofc-select2',
					'multiple' => true,
					'options'  => array(
						'wp_query' => array(
							'post_type'      => 'page',
							'posts_per_page' => -1,
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					),
				),
				array(
					'id'       => 'woofc_exclude_on_pages',
					'type'     => 'select',
					'desc'     => __( 'Hide floating cart only on selected pages.', 'woo-flying-cart' ),
					'default'  => array(),
					'classes'  => 'regular-text woofc-select2',
					'multiple' => true,
					'options'  => array(
						'wp_query' => array(
							'post_type'      => 'page',
							'posts_per_page' => -1,
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					),
				),
				array(
					'id'      => 'woofc_custom_css',
					'type'    => 'textarea',
					'title'   => __( 'Custom CSS', 'woo-flying-cart' ),
					'desc'    => __( 'Enter your custom CSS.', 'woo-flying-cart' ),
					'default' => '',
					'style'   => array(
						'height'     => '200px',
						'background' => '#263238',
						'color'      => '#fff',
						'font-size'  => '13px',
						'width'      => '600px',
						'max-width'  => '98%',
					)
				),
				array(
					'id'       => 'wscc_developemnt_settings_link',
					'title'    => __( 'Developer Settings', 'woo-flying-cart' ),
					'type'     => 'WOOFC_link',
					'link'     => admin_url( 'admin.php?page=woocommerce-flying-cart&tab=developer' ),
					'desc'     => __( 'Please do not make changes here without our permission.', 'woo-flying-cart' ),
					'value'    => __( 'Goto developer settings', 'woo-flying-cart' ),
					'callback' => array( $this, 'field_link' ),
				),
			),
		);

		// Flying Cart Settings.
		$settings[] = array(
			'id'     => 'woofc_flying_cart_settings',
			'title'  => __( 'Flying Cart Settings', 'woo-flying-cart' ),
			'fields' => array(
				array(
					'id'      => 'woofc_cart_title_text',
					'type'    => 'text',
					'title'   => __( 'Cart Title', 'woo-flying-cart' ),
					'desc'    => __( 'Enter your cart title.', 'woo-flying-cart' ),
					'default' => 'YOUR CART',
				),
				array(
					'id'      => 'woofc_cart_empty_cart_text',
					'type'    => 'wp_editor',
					'title'   => __( 'Empty Cart Message', 'woo-flying-cart' ),
					'desc'    => __( 'Enter your empty cart text.', 'woo-flying-cart' ),
					'default' => 'Your cart is currently empty.',
				),
				array(
					'id'      => 'woofc_cart_open_after_add_to_cart',
					'type'    => 'checkbox',
					'title'   => __( 'Open Cart After Adding products', 'woo-flying-cart' ),
					'desc'    => __( 'Open cart when product added to cart.', 'woo-flying-cart' ),
					'default' => 'no',
				),
				array(
					'id'      => 'woofc_cart_remove_product_links',
					'type'    => 'checkbox',
					'title'   => __( 'Remove Product Links', 'woo-flying-cart' ),
					'desc'    => __( 'Remove product links in flying cart.', 'woo-flying-cart' ),
					'default' => 'no',
				),
				array(
					'id'      => 'woofc_cart_hide_product_images',
					'type'    => 'checkbox',
					'title'   => __( 'Hide Product Images', 'woo-flying-cart' ),
					'desc'    => __( 'Hide product images in flying cart.', 'woo-flying-cart' ),
					'default' => 'no',
				),
				array(
					'id'      => 'woofc_cart_hide_total_price',
					'type'    => 'checkbox',
					'title'   => __( 'Hide Cart Total Price', 'woo-flying-cart' ),
					'desc'    => __( 'Enable to hide cart total price.', 'woo-flying-cart' ),
					'default' => 'no',
				),
				array(
					'id'      => 'woofc_cart_button',
					'type'    => 'select',
					'title'   => __( 'Cart Button', 'woo-flying-cart' ),
					'desc'    => array(
						__( 'Select cart page.', 'woo-flying-cart' ),
						wp_kses_post( sprintf( __( 'Default <code>%s</code>', 'woo-flying-cart' ), get_the_title( get_option( 'woocommerce_cart_page_id' ) ) ) ),
					),
					'default' => get_option( 'woocommerce_cart_page_id' ),
					'classes' => 'regular-text woofc-select2',
					'options' => array(
						''         => __( 'None', 'woo-flying-cart' ),
						'wp_query' => array(
							'post_type'      => 'page',
							'posts_per_page' => -1,
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					),
				),
				array(
					'id'      => 'woofc_cart_button_text',
					'type'    => 'text',
					'title'   => __( 'Cart Button Text', 'woo-flying-cart' ),
					'desc'    => __( 'Enter cart button text.', 'woo-flying-cart' ),
					'default' => get_the_title( get_option( 'woocommerce_cart_page_id' ) ),
				),
				array(
					'id'      => 'woofc_checkout_button',
					'type'    => 'select',
					'title'   => __( 'Checkout Button', 'woo-flying-cart' ),
					'desc'    => array(
						__( 'Select checkout page.', 'woo-flying-cart' ),
						wp_kses_post( sprintf( __( 'Default <code>%s</code>', 'woo-flying-cart' ), get_the_title( get_option( 'woocommerce_checkout_page_id' ) ) ) ),
					),
					'default' => get_option( 'woocommerce_checkout_page_id' ),
					'classes' => 'regular-text woofc-select2',
					'options' => array(
						''         => __( 'None', 'woo-flying-cart' ),
						'wp_query' => array(
							'post_type'      => 'page',
							'posts_per_page' => -1,
							'orderby'        => 'title',
							'order'          => 'ASC',
						)
					),
				),
				array(
					'id'      => 'woofc_checkout_button_text',
					'type'    => 'text',
					'title'   => __( 'Checkout Button Text', 'woo-flying-cart' ),
					'desc'    => __( 'Enter checkout button text.', 'woo-flying-cart' ),
					'default' => get_the_title( get_option( 'woocommerce_checkout_page_id' ) ),
				),
			),
		);

		// Related Products Settings.
		$settings[] = array(
			'id'     => 'woofc_related_products_settings',
			'title'  => __( 'Related Products', 'woo-flying-cart' ),
			'page'   => 'woofc_flying_cart_settings',
			'fields' => array(
				array(
					'id'        => 'woofc_cart_related_products_status',
					'type'      => 'checkbox',
					'title'     => __( 'Related Products', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'You can enable or disable the related products on Flying Cart.', 'woo-flying-cart' ),
					'default'   => 'yes',
				),
				array(
					'id'         => 'woofc_cart_related_products_per_row',
					'type'       => 'number',
					'title'      => __( 'Numbers of Related Products', 'woo-flying-cart' ),
					'desc'       => __( 'How many related products should be shown?', 'woo-flying-cart' ),
					'default'    => '3',
					'attributes' => array(
						'min'  => 1,
						'max'  => 4,
					),
				),
				array(
					'id'      => 'woofc_cart_related_products_text',
					'type'    => 'text',
					'title'   => __( 'Related Products Text', 'woo-flying-cart' ),
					'desc'    => __( 'Enter the related products text.', 'woo-flying-cart' ),
					'default' => 'You may also like',
				),
			),
		);

		// Cross-sells Products Settings.
		$settings[] = array(
			'id'     => 'woofc_cross_sells_products_settings',
			'title'  => __( 'Cross-sells Products', 'woo-flying-cart' ),
			'page'   => 'woofc_flying_cart_settings',
			'fields' => array(
				array(
					'id'        => 'woofc_cart_cross_sells_products_status',
					'type'      => 'checkbox',
					'title'     => __( 'Cross-sells Products', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'You can enable or disable the cross-sells products on Flying Cart.', 'woo-flying-cart' ),
					'default'   => 'yes',
				),
				array(
					'id'         => 'woofc_cart_cross_sells_products_per_row',
					'type'       => 'number',
					'title'      => __( 'Numbers of Cross-sells Products', 'woo-flying-cart' ),
					'desc'       => __( 'How many cross-sells products should be shown?', 'woo-flying-cart' ),
					'default'    => '3',
					'attributes' => array(
						'min'  => 1,
						'max'  => 4,
					),
				),
				array(
					'id'      => 'woofc_cart_cross_sells_products_text',
					'type'    => 'text',
					'title'   => __( 'Cross-sells Products Text', 'woo-flying-cart' ),
					'desc'    => __( 'Enter the cross-sells products text.', 'woo-flying-cart' ),
					'default' => 'Recommended products',
				),
			),
		);

		// Coupon Settings.
		$settings[] = array(
			'id'     => 'woofc_coupon_settings',
			'title'  => __( 'Coupon Settings', 'woo-flying-cart' ),
			'page'   => 'woofc_flying_cart_settings',
			'fields' => array(
				array(
					'id'        => 'woofc_cart_coupons_status',
					'type'      => 'checkbox',
					'title'     => __( 'Show Available Coupons', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'You can enable or disable available coupons on flying cart.', 'woo-flying-cart' ),
					'default'   => 'yes',
				),
				array(
					'id'        => 'woofc_cart_coupons_expiry_date_status',
					'type'      => 'checkbox',
					'title'     => __( 'Show Expiry Date', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'You can enable or disable expiry date on the coupons.', 'woo-flying-cart' ),
					'default'   => 'yes',
				),
				array(
					'id'      => 'woofc_cart_coupons_expiry_date_text',
					'type'    => 'text',
					'title'   => __( 'Expiry Date Text', 'woo-flying-cart' ),
					'desc'    => sprintf( __( 'Use %s to show coupon expiry date.', 'woo-flying-cart' ), '<code>{{expiry_date}}</code>' ),
					'default' => 'Valid till {{expiry_date}}',
				),
			),
		);

		// Developer Settings.
		$settings[] = array(
			'id'     => 'woofc_developer_settings',
			'title'  => __( 'Developer Settings', 'woo-flying-cart' ),
			'fields' => array(
				array(
					'id'        => 'woofc_debug_status',
					'type'      => 'checkbox',
					'title'     => __( 'Debug', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'By enabling debug option you allow us to view plugin settings, server environment, installed plugins.', 'woo-flying-cart' ),
					'default'   => 'no',
				),

				array(
					'id'         => 'woofc_reset_settings_temp',
					'type'       => 'WOOFC_link',
					'title'      => __( 'Reset Settings', 'woo-flying-cart' ),
					'link'       => '?woofc_reset_settings=1',
					'desc'       => __( 'Reset all settings to default.', 'woo-flying-cart' ),
					'value'      => __( 'Reset', 'woo-flying-cart' ),
					'callback'   => array( $this, 'field_link' ),
					'classes'    => 'button button-secondary',
					'attributes' => array(
						'onClick' => 'return confirm( "' . __( 'Are you sure?', 'woo-flying-cart' ) . '" )',
					),
					'register'   => false,
				),
				array(
					'id'        => 'woofc_delete_all',
					'type'      => 'checkbox',
					'title'     => __( 'Delete Plugin Setting', 'woo-flying-cart' ),
					'desc'      => __( 'Enable/ Disable', 'woo-flying-cart' ),
					'tooltip'   => __( 'If you want to delete plugin settings stored in database then enable this option and then click on the save changes button. Now delete the plugin from your plugins page.', 'woo-flying-cart' ),
					'default'   => 'no',
				),
			),
		);

		return $settings;
	}

	/**
	 * Render link fields.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $args Field arguments.
	 * @return void
	 */
	public function field_link( $args ) {
		$classes     = isset( $args['classes'] ) ? $args['classes'] : '';
		$custom_attr = parent::custom_field_attributes( $args );

		echo sprintf(
			'<a href="%1$s" class="%2$s" %4$s>%3$s</a>',
			esc_url( $args['link'] ),
			esc_attr( $classes ),
			esc_html( $args['value'] ),
			$custom_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);

		echo parent::field_description( $args );
	}
}

return new WOOFC_Settings();
