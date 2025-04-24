<?php
/**
* WooCommerce Flying Cart
*
* @author      WeCreativez
* @copyright   2019 WeCreativez
* @license     GPL-2.0-or-later
*
* @wordpress-plugin
* Plugin Name: 			WooCommerce Flying Cart
* Plugin URI:  			https://codecanyon.net/item/woocommerce-flying-cart/24900763/
* Description: 			A beautiful way of to display a WooCommerce cart.
* Version:				1.6.1
* Requires at least: 	4.5
* Requires PHP:      	5.6
* Author:      			WeCreativez
* Author URI:  			https://wecreativez.com/
* Text Domain: 			woo-flying-cart
* Domain Path: 			/languages/
* License:     			GPL v2 or later
* License URI: 			http://www.gnu.org/licenses/gpl-2.0.txt
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Plugin file
define( 'WOOFC_PLUGIN_FILE', __FILE__ );
// Plugin absolute path
define( 'WOOFC_PLUGIN_PATH', plugin_dir_path( WOOFC_PLUGIN_FILE ) );
// Plugin URL
define( 'WOOFC_PLUGIN_URL', plugin_dir_url( WOOFC_PLUGIN_FILE ) );
// Plugin current version
define( 'WOOFC_PLUGIN_VER', '1.6.1' );

/**
 * Plugin installation.
 *
 * @since 1.0
 */
function woofc_plugin_install() {
	// Run migration first.
	require_once WOOFC_PLUGIN_PATH . 'includes/migration/class-woofc-migration.php';
	// Run installation.
	require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-install.php';

	WOOFC_Install::install();
}
register_activation_hook( WOOFC_PLUGIN_FILE, 'woofc_plugin_install' );

/**
 * Plugin initialization.
 *
 * @since 1.0
 */
function woofc_plugin_init() {
	// Init plugin files if WooCommerce activate.
	if ( class_exists( 'WooCommerce' ) ) {
		require_once WOOFC_PLUGIN_PATH . 'includes/class-woofc-init.php';
		$woofc = new WOOFC_Init;
		$woofc->init();
	} else {
		require_once WOOFC_PLUGIN_PATH . 'includes/admin/class-woofc-admin-no-woocommerce.php';
	}
}
add_action( 'plugins_loaded', 'woofc_plugin_init', 20 );
