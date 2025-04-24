<?php if ( ! defined( 'ABSPATH' ) ) {exit;} // Exit if accessed directly.

/**
 * Theme all translation strings.
 * 
 * @since  4.4.4
 * @author https://codevz.com/
 * @link https://xtratheme.com/
 */

if ( ! class_exists( 'Xtra_Strings' ) ) {

	class Xtra_Strings {

		// Class instance.
		private static $instance = null;

		public function __construct() {

			add_action( 'after_setup_theme', [ $this, 'language' ] );

		}

		// Instance.
		public static function instance() {

			if ( self::$instance === null ) {

				self::$instance = new self();

			}

			return self::$instance;

		}

		// Load language(s)
		public function language() {

			load_textdomain( 'bizpro', trailingslashit( get_template_directory() ) . 'languages/bizpro-' . get_locale() . '.mo' );

		}

		// Get strings.
		public static function get( $string, $sprintf = '' ) {

			$strings = [

				'theme_name' 			=> apply_filters( 'xtra_config_theme_name', esc_html__( 'XTRA', 'bizpro' ) ),
				'codevz_plus' 			=> esc_html__( 'Codevz Plus', 'bizpro' ),
				'copyright' 			=> esc_html__( '© All rights reserved, Powered by WordPress.', 'bizpro' ),
				'homepage' 				=> esc_html__( 'Home page', 'bizpro' ),
				'primary' 				=> esc_html__( 'Primary', 'bizpro' ),
				'secondary' 			=> esc_html__( 'Secondary', 'bizpro' ),
				'footer' 				=> esc_html__( 'Footer', 'bizpro' ),
				'offcanvas_area' 		=> esc_html__( 'Offcanvas', 'bizpro' ),
				'product_primary' 		=> esc_html__( 'Shop primary', 'bizpro' ),
				'product_secondary' 	=> esc_html__( 'Shop secondary', 'bizpro' ),
				'portfolio_primary' 	=> esc_html__( 'Portfolio primary', 'bizpro' ),
				'portfolio_secondary' 	=> esc_html__( 'Portfolio secondary', 'bizpro' ),
				'add_widgets' 			=> esc_html__( 'Add widgets here to appear in your', 'bizpro' ),
				'pro' 					=> esc_html__( 'PRO', 'bizpro' ),
				'author_posts' 			=> esc_html__( 'Author posts', 'bizpro' ),
				'view_all_posts' 		=> esc_html__( 'View all posts', 'bizpro' ),
				'not_found' 			=> esc_html__( 'Nothing Found', 'bizpro' ),
				'search' 				=> esc_html__( 'Search', 'bizpro' ),
				'no_comment' 			=> esc_html__( 'No comment', 'bizpro' ),
				'comment' 				=> esc_html__( 'Comment', 'bizpro' ),
				'comments' 				=> esc_html__( 'Comments', 'bizpro' ),
				'activation' 			=> esc_html__( 'Activation', 'bizpro' ),
				'importer' 				=> esc_html__( 'Demo Importer', 'bizpro' ),
				'importer_page' 		=> esc_html__( 'Page Importer', 'bizpro' ),
				'plugins' 				=> esc_html__( 'Install Plugins', 'bizpro' ),
				'options' 				=> esc_html__( 'Theme Options', 'bizpro' ),
				'status' 				=> esc_html__( 'System Status', 'bizpro' ),
				'uninstall' 			=> esc_html__( 'Uninstall Demo', 'bizpro' ),
				'feedback' 				=> esc_html__( 'Feedback', 'bizpro' ),
				'elementor' 			=> esc_html__( 'Elementor Page Builder', 'bizpro' ),
				'js_composer' 			=> esc_html__( 'WPBakery Page Builder', 'bizpro' ),
				'revslider' 			=> esc_html__( 'Revolution Slider', 'bizpro' ),
				'woocommerce' 			=> esc_html__( 'Woocommerce', 'bizpro' ),
				'cf7' 					=> esc_html__( 'Contact Form 7', 'bizpro' ),
				'wpoptimize' 			=> esc_html__( 'WP Optimize', 'bizpro' ),
				'of' 					=> esc_html__( 'of', 'bizpro' ),
				'close' 				=> esc_html__( 'Close', 'bizpro' ),
				'plugin_before' 		=> esc_html__( 'Installing', 'bizpro' ),
				'plugin_after' 			=> esc_html__( 'Activated', 'bizpro' ),
				'import_before' 		=> esc_html__( 'Importing', 'bizpro' ),
				'import_after' 			=> esc_html__( 'Imported', 'bizpro' ),
				'downloading' 			=> esc_html__( 'Downloading', 'bizpro' ),
				'demo_files' 			=> esc_html__( 'Demo Files', 'bizpro' ),
				'downloaded' 			=> esc_html__( 'Downloaded', 'bizpro' ),
				'widgets' 				=> esc_html__( 'Widgets', 'bizpro' ),
				'slider' 				=> esc_html__( 'Revolution Slider', 'bizpro' ),
				'posts' 				=> esc_html__( 'Pages & Posts', 'bizpro' ),
				'images' 				=> esc_html__( 'Images', 'bizpro' ),
				'error_500' 			=> esc_html__( 'PHP error 500, Internal server error, Please check your server error log file or contact with support.', 'bizpro' ),
				'error_503' 			=> esc_html__( 'PHP error 503, Internal server error, Please try again with same import demo.', 'bizpro' ),
				'ajax_error' 			=> esc_html__( 'An error has occured, Please deactivate all plugins except theme plugins and try again, If still have same issue, Please submit ticket to theme author.', 'bizpro' ),
				'features' 				=> esc_html__( 'Choose at least one feature to import.', 'bizpro' ),
				'feedback_empty' 		=> esc_html__( 'Message box is empty, Please fill the box then submit.', 'bizpro' ),
				'page_importer_empty' 	=> esc_html__( 'URL input is empty, Please fill the input then submit.', 'bizpro' ),
				'welcome' 				=> esc_html__( 'Welcome to %s WordPress Theme', 'bizpro' ),
				'version' 				=> esc_html__( 'Current version:', 'bizpro' ),
				'based_on' 				=> esc_html__( 'Based on XTRA framework', 'bizpro' ),
				'ref' 					=> apply_filters( 'xtra_config_buy_link', esc_html__( 'https://1.envato.market/xtratheme', 'bizpro' ) ),
				'docs' 					=> esc_html__( 'https://xtratheme.com/docs', 'bizpro' ),
				'documentation' 		=> esc_html__( 'Documentation', 'bizpro' ),
				'youtube' 				=> esc_html__( 'https://www.youtube.com/channel/UCrS1L4oeTRfU1hvIo1gJGjg/videos', 'bizpro' ),
				'video_tutorials' 		=> esc_html__( 'Video Tutorials', 'bizpro' ),
				'changelog' 			=> apply_filters( 'xtra_config_changelog_link', esc_html__( 'https://xtratheme.com/changelog', 'bizpro' ) ),
				'change_log' 			=> esc_html__( 'Change Log', 'bizpro' ),
				'ticksy' 				=> apply_filters( 'xtra_config_support_link', esc_html__( 'https://codevz.ticksy.com', 'bizpro' ) ),
				'support' 				=> esc_html__( 'Support', 'bizpro' ),
				'faqs' 					=> apply_filters( 'xtra_config_faq_link', esc_html__( 'https://xtratheme.com/faqs', 'bizpro' ) ),
				'faq' 					=> esc_html__( 'F.A.Q', 'bizpro' ),
				'certificate' 			=> esc_html__( 'Activation Certificate', 'bizpro' ),
				'deregister_license' 	=> esc_html__( 'Deregister License', 'bizpro' ),
				'purchase_code' 		=> esc_html__( 'Your Purchase Code', 'bizpro' ),
				'purchase_date' 		=> esc_html__( 'Purchase date:', 'bizpro' ),
				'support_until' 		=> esc_html__( 'Support until:', 'bizpro' ),
				'support_expired' 		=> esc_html__( 'Your support has been expired, Click on below link and extend your support.', 'bizpro' ),
				'extend' 				=> esc_html__( 'Buy extended support or new license', 'bizpro' ),
				'license_activation' 	=> esc_html__( 'License Activation', 'bizpro' ),
				'deregistered' 			=> esc_html__( 'Your license code on this website deregistered successfully.', 'bizpro' ),
				'congrats' 				=> esc_html__( 'Congratulation', 'bizpro' ),
				'activated' 			=> esc_html__( 'Your theme has been activated successfully.', 'bizpro' ),
				'insert' 				=> esc_html__( 'Please insert a valid license code.', 'bizpro' ),
				'activate_war' 			=> esc_html__( 'Please activate your theme via purchase code to access theme features, updates and demo importer.', 'bizpro' ),
				'placeholder' 			=> esc_html__( 'Please insert purchase code ...', 'bizpro' ),
				'activate' 				=> esc_html__( 'Activate', 'bizpro' ),
				'find' 					=> esc_html__( 'How to find purchase code?', 'bizpro' ),
				'buy_new' 				=> esc_html__( 'Buy new license', 'bizpro' ),
				'install' 				=> esc_html__( 'Install Plugins', 'bizpro' ),
				'required' 				=> esc_html__( 'Required', 'bizpro' ),
				'recommended' 			=> esc_html__( 'Recommended', 'bizpro' ),
				'private' 				=> esc_html__( 'Private repository', 'bizpro' ),
				'premium' 				=> esc_html__( 'Premium', 'bizpro' ),
				'wp' 					=> esc_html__( 'WordPress repository', 'bizpro' ),
				'free_ver' 				=> esc_html__( 'Free version', 'bizpro' ),
				'activated_s' 			=> esc_html__( 'Activated successfully', 'bizpro' ),
				'tas' 					=> esc_html__( 'Theme activated successfully', 'bizpro' ),
				'install_activate' 		=> esc_html__( 'Install & Activate', 'bizpro' ),
				'installed_activated' 	=> esc_html__( 'Installed & Activated', 'bizpro' ),
				'unlock' 				=> esc_html__( 'Unlock', 'bizpro' ),
				'please_wait' 			=> esc_html__( 'Please wait', 'bizpro' ),
				'no_plugins' 			=> esc_html__( 'You have installed all the plugins and there is no any plugin to install.', 'bizpro' ),
				'filters' 				=> esc_html__( 'Fitlers:', 'bizpro' ),
				'all' 					=> esc_html__( 'All', 'bizpro' ),
				'starter' 				=> esc_html__( 'Starter', 'bizpro' ),
				'type' 					=> esc_html__( 'Type a keyword ...', 'bizpro' ),
				'free' 					=> esc_html__( 'FREE', 'bizpro' ),
				'import' 				=> esc_html__( 'Import', 'bizpro' ),
				'uninstall' 			=> esc_html__( 'Uninstall', 'bizpro' ),
				'preview' 				=> esc_html__( 'Preview', 'bizpro' ),
				'back' 					=> esc_html__( 'Back to demos', 'bizpro' ),
				'welcome_to' 			=> esc_html__( 'Welcome to', 'bizpro' ),
				'selected' 				=> esc_html__( 'Selected demo:', 'bizpro' ),
				'exclusive' 			=> esc_html__( 'Exclusive', 'bizpro' ),
				'wizard' 				=> esc_html__( 'Demo Importer Wizard', 'bizpro' ),
				'live_preview' 			=> esc_html__( 'Live preview:', 'bizpro' ),
				'elementor_s' 			=> esc_html__( 'Elementor', 'bizpro' ),
				'wpbakery' 				=> esc_html__( 'WPBakery', 'bizpro' ),
				'choose' 				=> esc_html__( 'Choose page builder:', 'bizpro' ),
				'choose_2' 				=> esc_html__( 'Choose Builder', 'bizpro' ),
				'ata' 					=> esc_html__( 'Activate your theme with license code to access this feature.', 'bizpro' ),
				'desc' 					=> esc_html__( 'By checking this field, wizard will import Arabic version of current demo that you have selected.', 'bizpro' ),
				'rtl' 					=> esc_html__( 'RTL version?', 'bizpro' ),
				'full_import' 			=> esc_html__( 'Full Import', 'bizpro' ),
				'custom_import' 		=> esc_html__( 'Custom Import', 'bizpro' ),
				'media' 				=> esc_html__( 'Images & Media', 'bizpro' ),
				'imported' 				=> esc_html__( 'Your website has been imported successfully.', 'bizpro' ),
				'view_website' 			=> esc_html__( 'View your website', 'bizpro' ),
				'customize' 			=> esc_html__( 'Customize webiste', 'bizpro' ),
				'error' 				=> esc_html__( 'Error!', 'bizpro' ),
				'occured' 				=> esc_html__( 'An error has occured, Please try again.', 'bizpro' ),
				'troubleshooting' 		=> esc_html__( 'Troubleshooting', 'bizpro' ),
				'prev_step' 			=> esc_html__( 'Prev Step', 'bizpro' ),
				'getting_started' 		=> esc_html__( 'Getting Started', 'bizpro' ),
				'config' 				=> esc_html__( 'Configuration', 'bizpro' ),
				'importing' 			=> esc_html__( 'Please wait, Importing', 'bizpro' ),
				'ready' 				=> esc_html__( 'Ready to go!', 'bizpro' ),
				'next_step' 			=> esc_html__( 'Next Step', 'bizpro' ),
				'single_page' 			=> esc_html__( 'Single Page Importer', 'bizpro' ),
				'page_pro' 				=> esc_html__( 'Page importer feature is available only when you %s activate your theme with a valid license code.', 'bizpro' ),
				'page_import_war' 		=> esc_html__( 'The demo page you want to import may have a second color, To avoid the color problem, set a second color for your site from Theme Options > General > Colors', 'bizpro' ),
				'page_insert' 			=> esc_html__( 'Insert a demo page URL and click on import button then wait for the process to complete.', 'bizpro' ),
				'page_insert_link' 		=> esc_html__( 'Insert the demo link ...', 'bizpro' ),
				'activation_error' 		=> esc_html__( 'Please activate your theme via purchase code to access theme features, updates and demo importer.', 'bizpro' ),
				'valid_url' 			=> esc_html__( 'Please insert a valid URL', 'bizpro' ),
				'allow_url_fopen' 		=> esc_html__( 'Enable allow_url_fopen on your server then you can import page.', 'bizpro' ),
				'page_imported' 		=> esc_html__( 'Page imported successfully.', 'bizpro' ),
				'try_again' 			=> esc_html__( 'Error, Please try again ...', 'bizpro' ),
				'responding' 			=> esc_html__( 'Server not responding, Please make sure your link is valid.', 'bizpro' ),
				'wrong' 				=> esc_html__( 'Something went wrong, Please try again ...', 'bizpro' ),
				'status' 				=> esc_html__( 'System Status', 'bizpro' ),
				'good' 					=> esc_html__( 'Good', 'bizpro' ),
				'not_active' 			=> esc_html__( 'Theme is not activated', 'bizpro' ),
				'php_ver' 				=> esc_html__( 'Server PHP Version', 'bizpro' ),
				'php_error' 			=> esc_html__( 'PHP 8.0 or above recommended', 'bizpro' ),
				'php_memory' 			=> esc_html__( 'Server PHP Memory Limit', 'bizpro' ),
				'128m' 					=> esc_html__( '128M recommended', 'bizpro' ),
				'8r' 					=> esc_html__( '8 recommended', 'bizpro' ),
				'30r' 					=> esc_html__( '30 recommended', 'bizpro' ),
				'max_size' 				=> esc_html__( 'Server PHP Post Max Size', 'bizpro' ),
				'execution' 			=> esc_html__( 'Server PHP Max Execution Time', 'bizpro' ),
				'server_php' 			=> esc_html__( 'Server PHP', 'bizpro' ),
				'curl' 					=> esc_html__( 'PHP cURL or allow_url_fopen is required.', 'bizpro' ),
				'active' 				=> esc_html__( 'Active', 'bizpro' ),
				'contact' 				=> esc_html__( 'Contact with your server support.', 'bizpro' ),
				'feedback' 				=> esc_html__( 'Feedback', 'bizpro' ),
				'please_help' 			=> esc_html__( 'Please help us improve the "%s" theme, we have added a feedback form, you can send us your comments and criticisms.', 'bizpro' ),
				'thanks' 				=> esc_html__( 'Thanks for purchasing the "%s" theme; to improve the theme, through the following form, you can send your feedback such as report a bug, request a feature, request a demo, ask non-support questions, etc.', 'bizpro' ),
				'submit' 				=> esc_html__( 'Submit', 'bizpro' ),
				'sent' 					=> esc_html__( 'Your message has been sent successfully.', 'bizpro' ),
				'sent_error' 			=> esc_html__( 'Could not send your message, Please try again.', 'bizpro' ),
				'no_msg' 				=> esc_html__( 'There is no message to send, Please try again.', 'bizpro' ),
				'un_demos' 				=> esc_html__( 'Uninstall Demo(s)', 'bizpro' ),
				'un_desc' 				=> esc_html__( 'In this list you can see demos imported on your site previously, You can uninstall any demo data.', 'bizpro' ),
				'yet' 					=> esc_html__( 'You have not imported any demo yet.', 'bizpro' ),
				'are_you_sure' 			=> esc_html__( 'Are you sure for this?', 'bizpro' ),
				'delete' 				=> esc_html__( 'This will be deleted all your website data such as posts, pages, attachments, theme options, sliders, etc. and there is no undo button for this action.', 'bizpro' ),
				'no' 					=> esc_html__( 'No, never mind', 'bizpro' ),
				'uninstalling' 			=> esc_html__( 'Uninstalling, Please wait', 'bizpro' ),
				'yes' 					=> esc_html__( 'Yes please', 'bizpro' ),
				'uninstalled' 			=> esc_html__( 'Demo "DEMONAME" uninstalled successfully.', 'bizpro' ),
				'reload' 				=> esc_html__( 'Reload page', 'bizpro' ),
				'envato_error' 			=> esc_html__( 'Envato error: Your license code is invalid.', 'bizpro' ),
				'envato_api' 			=> esc_html__( 'Envato API error:', 'bizpro' ),
				'envato_exist' 			=> esc_html__( 'Envato error: The purchase code does not exist.', 'bizpro' ),
				'envato_http' 			=> esc_html__( 'Envato error: Failed to validate code due to an HTTP error', 'bizpro' ),
				'envato_10sec' 			=> esc_html__( 'Envato error: Please try again in 10 seconds.', 'bizpro' ),
				'envato_parsing' 		=> esc_html__( 'Envato error: Parsing response.', 'bizpro' ),
				'envato_another' 		=> esc_html__( 'Envato error: Your purchase code is valid but it seems its for another item, Please add correct purchase code.', 'bizpro' ),
				'envato_check' 			=> esc_html__( 'Your license not found in our database, Please check your license and try again ...', 'bizpro' ),
				'ajax_error' 			=> esc_html__( 'AJAX requested name is empty, Please try again.', 'bizpro' ),
				'redirected' 			=> esc_html__( 'Redirected successfully.', 'bizpro' ),
				'find_plugin' 			=> esc_html__( 'Could not find plugin "%s" API, Please refresh page and try again.', 'bizpro' ),
				'cp_error' 				=> esc_html__( 'Codevz plus plugin is not installed or activated.', 'bizpro' ),
				'listed' 				=> esc_html__( 'Plugin "%s" is no listed as a valid plugin.', 'bizpro' ),
				'ftp' 					=> esc_html__( 'WordPress required FTP login details', 'bizpro' ),
				'wp_api' 				=> esc_html__( 'WordPress API Error:', 'bizpro' ),
				'manually' 				=> esc_html__( 'Could not download "%s" plugin ZIP file, Please go to Appearance > Install Plugins and install it manually, and try again demo importer.', 'bizpro' ),
				'300s' 					=> esc_html__( 'Error, Through FTP delete plugins > "%s" folder & increase PHP max_execution_time to 300 then try again.', 'bizpro' ),
				'plugin_error' 			=> esc_html__( 'Plugin activation error, ', 'bizpro' ),
				'plugin_installed' 		=> esc_html__( 'Plugin "%s" installed and activated successfully.', 'bizpro' ),
				'plugin_api' 			=> esc_html__( 'Plugin API error:', 'bizpro' ),
				'demo_uninstalled' 		=> esc_html__( 'Demo "%s" uninstalled successfully.', 'bizpro' ),
				'uninstall_error' 		=> esc_html__( 'Could not uninstall "%s" demo.', 'bizpro' ),
				'search_error' 			=> esc_html__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'bizpro' ),
				'slider_placeholder' 	=> esc_html__( 'Please install and activate Slider Revolution Plugin from Dashboard > XTRA > Plugins', 'bizpro' ),
				'slider_select' 		=> esc_html__( 'Please Edit your page in backend and from Page settings > Header settings, Select slider name.', 'bizpro' ),

			];

			return isset( $strings[ $string ] ) ? sprintf( $strings[ $string ], $sprintf ) : '';

		}

	}

	Xtra_Strings::instance();

}