<?php if ( ! defined( 'ABSPATH' ) ) {exit;} // Exit if accessed directly.

/**
 * Theme dashboard, activtion, importer, plugins, system status, etc.
 * 
 * @since  4.3.0
 * @author https://codevz.com/
 * @link https://xtratheme.com/
 */

if ( ! class_exists( 'Xtra_Dashboard' ) ) {

	class Xtra_Dashboard extends Xtra_Theme {

		private static $instance = null;

		public function __construct() {

			add_action( 'init', [ $this, 'init' ] );

		}

		public function init() {

			// Check free.
			$this->is_free = self::is_free();

			// Disable features.
			if ( ! self::$premium ) {

				$this->disable = array_flip( [ 'envato', 'activation', 'importer_page', 'plugins', 'status', 'uninstall', 'feedback', 'docs', 'youtube', 'changelog', 'ticksy', 'faq' ] );

			} else {

				$this->disable = array_flip( (array) self::option( 'disable' ) );

			}

			if ( Xtra_Theme::option( 'white_label_exclude_admin' ) && current_user_can( 'administrator' ) ) {
				$this->disable = [];
			}

			// Check white label for menu.
			if ( ! isset( $this->disable[ 'menu' ] ) || $this->is_free ) {

				// Theme info.
				$this->theme = wp_get_theme();
				$this->theme->version = empty( $this->theme->parent() ) ? $this->theme->get( 'Version' ) : $this->theme->parent()->Version;

				// IDs.
				$this->slug 	= 'xtra-activation';
				$this->option 	= 'codevz_theme_activation';

				// Admin menus.
				$this->menus 	= [

					'activation' 	=> Xtra_Strings::get( 'activation' ),
					'importer' 		=> Xtra_Strings::get( 'importer' ),
					'importer_page' => Xtra_Strings::get( 'importer_page' ),
					'plugins' 		=> Xtra_Strings::get( 'plugins' ),
					'options' 		=> Xtra_Strings::get( 'options' ),
					'status' 		=> Xtra_Strings::get( 'status' ),
					'uninstall' 	=> Xtra_Strings::get( 'uninstall' ),
					'feedback' 		=> Xtra_Strings::get( 'feedback' ),

				];

				// White label check activation.
				if ( isset( $this->disable[ 'activation' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'activation' ] );

				}

				// White label check importer.
				if ( isset( $this->disable[ 'importer' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'importer' ] );
					unset( $this->menus[ 'uninstall' ] );

				}

				if ( isset( $this->disable[ 'importer_page' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'importer_page' ] );

				}

				if ( isset( $this->disable[ 'plugins' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'plugins' ] );

				}

				if ( isset( $this->disable[ 'uninstall' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'uninstall' ] );

				}

				if ( isset( $this->disable[ 'status' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'status' ] );

				}

				if ( isset( $this->disable[ 'feedback' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'feedback' ] );

				}

				// White label check theme options.
				if ( isset( $this->disable[ 'options' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'options' ] );

				}

				// White label check videos.
				if ( isset( $this->disable[ 'videos' ] ) && ! $this->is_free ) {

					unset( $this->menus[ 'videos' ] );

				}

				// Theme plugins.
				$this->plugins 	= apply_filters( 'xtra_plugins_list', [
					'codevz-plus' 	=> [
						'name' 				=> Xtra_Strings::get( 'codevz_plus' ),
						'source' 			=> self::$api . 'codevz-plus.zip',
						'required' 			=> true,
						'class_exists' 		=> 'Codevz_Plus'
					],
					'elementor' 	=> [
						'name' 				=> Xtra_Strings::get( 'elementor' ),
						'recommended' 		=> true,
						'function_exists' 	=> 'elementor_load_plugin_textdomain'
					],
					'js_composer' 	=> [
						'name' 				=> Xtra_Strings::get( 'js_composer' ),
						'source' 			=> self::$api . 'js_composer.zip',
						'recommended' 		=> true,
						'class_exists' 		=> 'Vc_Manager'
					],
					'revslider' 	=> [
						'name' 				=> Xtra_Strings::get( 'revslider' ),
						'source' 			=> self::$api . 'revslider.zip',
						'recommended' 		=> true,
						'function_exists' 	=> 'get_rs_plugin_url'
					],
					'woocommerce' 	=> [
						'name' 				=> Xtra_Strings::get( 'woocommerce' ),
						'recommended' 		=> true,
						'class_exists' 		=> 'WooCommerce'
					],
					'contact-form-7' => [
						'name' 				=> Xtra_Strings::get( 'cf7' ),
						'recommended' 		=> true,
						'class_exists' 		=> 'WPCF7'
					],
					'wp-optimize' 	=> [
						'name' 				=> Xtra_Strings::get( 'wpoptimize' ),
						'recommended' 		=> true,
						'class_exists' 		=> 'WP_Optimize'
					],
				] );

				// List of demos.
				$this->demos = apply_filters( 'xtra_demos_list', [

					//'xxx' 	=> [

					//	'js_composer' 	=> true,
					//	'elementor' 	=> false,
					//	'free' 			=> true,
					//	'rtl' 			=> [ 'js_composer' = true, 'elementor' = true ],
					//	'plugins' 		=> [ 'revslider' => false, 'bbpress' => true ],

					//],

					'factory-2' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'beauty-salon-2' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'agency-2' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'music-and-band' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'nail-salon' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'perfume-shop' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'watch-shop' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'book-shop' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'flower-shop' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'architect-2' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'photographer' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'elderly-care' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],

					],
					'investment' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'dance' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'cryptocurrency-2' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],

					],
					'business-5' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'construction-2' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'advisor' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'seo-2' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'portfolio' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'personal-blog-2' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],

					],
					'insurance' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'corporate-2' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'business-4' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'startup' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'medical' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'factory' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'furniture' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'carwash' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'rims' 				=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'jewelry' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'church' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'yoga' 				=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'moving' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'plumbing' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'travel' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'beauty-salon'      => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'home-renovation' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'creative-business' => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'mechanic'        	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'lawyer'         	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'web-agency'        => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'gardening'         => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'corporate'         => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'business-3'        => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'digital-marketing' => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'business-classic'  => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'charity'        	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'creative-studio'   => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'kids'      	    => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'smart-home'        => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'logistic'          => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'industrial'      	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'tattoo'      		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'personal-blog'    	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'cleaning'      	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'metro-blog'      	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'parallax'      	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'3d-portfolio'      => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'agency'            => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'photography3'      => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'spa'               => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'app'               => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'architect'         => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'barber'            => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'building'          => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'business'          => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'camping-adventures'=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'coffee'            => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'conference' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'business-2' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'construction' 		=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'cryptocurrency' 	=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'cv-resume'         => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'dentist'           => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'fashion-shop'      => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'fast-food'         => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'finance'           => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'game'              => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'gym'               => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'hosting'           => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'hotel' 			=> [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'interior'          => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'lawyers'           => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'logo-portfolio'    => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						'free' 				=> true,
						'plugins' 			=> [ 'revslider' => false ]

					],
					'music'             => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'photography'       => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'photography2'      => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'plastic-surgery'   => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'restaurant'        => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'dubai-investment'  => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'seo'               => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'single-shop'       => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ],
						//'free' 				=> true,
						//'plugins' 			=> [ 'revslider' => false ]

					],
					'wedding'           => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],
					'winery'            => [

						'elementor' 		=> true,
						'rtl' 				=> [ 'js_composer' => true, 'elementor' => true ]

					],

				] );

				// Actions.
				add_action( 'admin_menu', [ $this, 'admin_menu' ] );
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
				add_action( 'wp_ajax_xtra_wizard', [ $this, 'wizard' ] );
				add_action( 'wp_ajax_xtra_feedback', [ $this, 'feedback_submit' ] );
				add_action( 'wp_ajax_xtra_page_importer', [ $this, 'xtra_page_importer' ] );

			}

		}

		public static function instance() {

			if ( self::$instance === null ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Load admin dashboard assets
		 * 
		 * @return -
		 */
		public function enqueue( $hook ) {

			if ( ! self::contains( $hook, 'xtra' ) ) {
				return false;
			}

			// Assets.
			wp_enqueue_style( 'xtra-font', 'https://fonts.googleapis.com/css?family=Poppins:400,500,600,700' );
			wp_enqueue_style( 'xtra', esc_url( self::$url ) . 'assets/css/dashboard.css', [], $this->theme->version, 'all' );
			wp_enqueue_script( 'xtra', esc_url( self::$url ) . 'assets/js/dashboard.js', [], $this->theme->version, true );

			// RTL styles.
			if ( is_rtl() ) {
				wp_enqueue_style( 'xtra-rtl', esc_url( self::$url ) . 'assets/css/dashboard.rtl.css', [], $this->theme->version, 'all' );
			}

			$plugins = [];

			// List of inactive plugins.
			foreach( $this->plugins as $slug => $plugin ) {

				if ( ! $this->plugin_is_active( $slug ) ) {

					$plugins[ $slug ] = true;

				}

			}

			// Translations for scripts.
			wp_localize_script( 'xtra', 'xtraWizard', [

				'plugins' 			=> $plugins,
				'of' 				=> Xtra_Strings::get( 'of' ),
				'close' 			=> Xtra_Strings::get( 'close' ),
				'plugin_before' 	=> Xtra_Strings::get( 'plugin_before' ),
				'plugin_after' 		=> Xtra_Strings::get( 'plugin_after' ),
				'import_before' 	=> Xtra_Strings::get( 'import_before' ),
				'import_after' 		=> Xtra_Strings::get( 'import_after' ),
				'codevz_plus' 		=> Xtra_Strings::get( 'codevz_plus' ),
				'js_composer' 		=> Xtra_Strings::get( 'js_composer' ),
				'elementor' 		=> Xtra_Strings::get( 'elementor' ),
				'revslider' 		=> Xtra_Strings::get( 'revslider' ),
				'cf7' 				=> Xtra_Strings::get( 'cf7' ),
				'woocommerce' 		=> Xtra_Strings::get( 'woocommerce' ),
				'downloading' 		=> Xtra_Strings::get( 'downloading' ),
				'demo_files' 		=> Xtra_Strings::get( 'demo_files' ),
				'downloaded' 		=> Xtra_Strings::get( 'downloaded' ),
				'options' 			=> Xtra_Strings::get( 'options' ),
				'widgets' 			=> Xtra_Strings::get( 'widgets' ),
				'slider' 			=> Xtra_Strings::get( 'slider' ),
				'posts' 			=> Xtra_Strings::get( 'posts' ),
				'images' 			=> Xtra_Strings::get( 'images' ),
				'error_500' 		=> Xtra_Strings::get( 'error_500' ),
				'error_503' 		=> Xtra_Strings::get( 'error_503' ),
				'ajax_error' 		=> Xtra_Strings::get( 'ajax_error' ),
				'features' 			=> Xtra_Strings::get( 'features' ),
				'feedback_empty' 	=> Xtra_Strings::get( 'feedback_empty' ),
				'page_importer_empty' => Xtra_Strings::get( 'page_importer_empty' )

			]);

		}

		/**
		 * Add admin menus.
		 * 
		 * @return array
		 */
		public function admin_menu() {

			// Deregister license.
			if ( ! empty( $_POST['deregister'] ) ) {

				// Get saved activation.
				$activation = get_option( $this->option );
				$purchase_code = isset( $activation[ 'purchase_code' ] ) ? $activation[ 'purchase_code' ] : null;

				$this->deregister( $purchase_code, strlen( $purchase_code ) < 40 );

			// Register license.
			} else if ( ! empty( $_POST[ 'register' ] ) ) {

				$purchase_code = sanitize_text_field( wp_unslash( $_POST['register'] ) );

				$this->register( $purchase_code, strlen( $purchase_code ) < 40 );

			}

			if ( ! self::$premium ) {

				add_theme_page( Xtra_Strings::get( 'theme_name' ), Xtra_Strings::get( 'theme_name' ), 'manage_options', 'xtra-importer', [ $this, 'importer' ] );

				return false;

			}

			// Icon.
			$icon = 'data:image/svg+xml;bas'.'e6'.'4,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMTEiIGhlaWdodD0iMjEzIiB2aWV3Qm94PSIwIDAgMjExIDIxMyI+IDxkZWZzPiA8c3R5bGU+IC5jbHMtMSB7IGZpbGw6ICNmZmY7IGZpbGwtcnVsZTogZXZlbm9kZDsgfSA8L3N0eWxlPiA8L2RlZnM+IDxwYXRoIGlkPSJDb2xvcl9GaWxsXzEiIGRhdGEtbmFtZT0iQ29sb3IgRmlsbCAxIiBjbGFzcz0iY2xzLTEiIGQ9Ik01Mi41MzMsMTYuMDI4Qzg2LjUyLDE1LjIxMSwxMTMuMDQ2LDQyLjYyLDk3LjgsNzcuMTM4Yy01LjcxNSwxMi45NDQtMTkuMDU0LDIwLjQ1LTMxLjk1NiwyMy45MTMtOS40NTIsMi41MzctMTkuMjY2LTEuNzQzLTIzLjk2Ny00LjQyOC0zLjM5NC0xLjkzOS02Ljk1LTIuMDI2LTkuNzY0LTQuNDI4LTguODQ0LTcuNTUtMjAuODIxLTI2Ljk1Ni0xNC4yLTQ2LjA1NGE0OC41NjEsNDguNTYxLDAsMCwxLDIzLjA4LTI2LjU3QzQ0Ljc1NywxNy42NTMsNDkuMTkzLDE4LjIxNyw1Mi41MzMsMTYuMDI4Wm05NC4wOTQsMGMxMS45MjItLjIxLDIyLjAyMS43MywyOS4yOTMsNS4zMTQsMTQuODkxLDkuMzg2LDI4LjYwNSwzNy45NDQsMTUuMDkxLDU5LjMzOS01Ljk2LDkuNDM2LTE3LjAxMiwxNy4yNjMtMjkuMjkzLDIwLjM3SDE0MS4zYy02LjYwOSwxLjYzOC0xNS40OTUsNC45NDktMjAuNDE3LDguODU3LTEwLjI0Niw4LjEzNi0xNi4wMjgsMjAuNS0xOS41MjgsMzUuNDI2djE5LjQ4NWMtNS4wMzYsMTguMDY4LTIzLjkxNywzOC45MTEtNDkuNzEsMzIuNzY5LTQuNzI0LTEuMTI0LTExLjA1Mi0yLjc3OC0xNS4wOS01LjMxMy01LjcxNC0zLjU4OC05LjU2LTkuMzgyLTEzLjMxNS0xNS4wNTdhNDUuMTUzLDQ1LjE1MywwLDAsMS02LjIxNC0xNC4xN2MtMS45LTcuODkzLjQ5NC0xNS4zNjgsMi42NjMtMjEuMjU2LDMuOTM5LTEwLjY5Myw5LjgyMi0yMC4yOTEsMTkuNTI5LTI0LjgsOC4zNTctMy44ODEsMTguMTcyLTIuNDgxLDI4LjQwNi01LjMxNCwxMi40NjYtMy40NTEsMjUuOTctMTAuMjYzLDMyLjg0NC0xOS40ODRBNjkuMTM5LDY5LjEzOSwwLDAsMCwxMTEuMTIsNjkuMTY3VjU0LjExMWMxLjQ2My02LjM1NywyLjk4NC0xMy42NzcsNi4yMTQtMTguNkMxMjIuMSwyOC4yNTYsMTMxLjEsMjEuMzE5LDEzOS41MjYsMTcuOCwxNDEuOTIsMTYuOCwxNDQuNzQ1LDE3LjI3MiwxNDYuNjI3LDE2LjAyOFptNTEuNDg1LDU0LjAyNWMwLjcxNCwwLjkuMzE1LDAuMjQzLDAuODg4LDEuNzcxaC0wLjg4OFY3MC4wNTNabS00Ni4xNTksNDIuNTEyYzI5LjMzMSwxLjM3OCw1Mi4xNjEsMjQuNjIsNDEuNzIxLDU1LjgtMS4zNTksNC4wNTgtMS4xMjIsOC40MzMtMy41NTEsMTEuNTEzLTYuNDI1LDguMTUyLTE4LjYsMTUuODM4LTMwLjE4MSwxOC42LTcuNzQ3LDEuODQ4LTE1LjE3LTEuNzM5LTE5LjUyOS0zLjU0My0zLjIzNi0xLjMzOS02LC4wNzktOC44NzYtMS43NzEtMTMuNC04LjYyNy0yNi4xMjktMzEuMTQ3LTE3Ljc1NC01My4xNCw0LjA4My0xMC43MjEsMTMuNzItMjAuMjY0LDIzLjk2Ny0yNC44QzE0MS43NDQsMTEzLjQ1NSwxNDguMiwxMTQuNzk0LDE1MS45NTMsMTEyLjU2NVoiLz4gPC9zdmc+';
			$icon = self::option( 'white_label_menu_icon', $icon );

			// Add welcome theme menu.
			$title = self::option( 'white_label_theme_name', Xtra_Strings::get( 'theme_name' ) );

			//if ( self::$plugin && method_exists( 'Codevz_Plus', 'add_menu' ) ) {

			//	Codevz_Plus::add_menu( $title, $title, 'manage_options', $this->slug, [ $this, 'activation' ], $icon, 2 );

			//} else {

				add_menu_page( $title, $title, 'manage_options', $this->slug, [ $this, 'activation' ], $icon, 2 );

			//}

			// Sub menus.
			foreach( $this->menus as $slug => $title ) {

				if ( $slug === 'uninstall' && ! get_option( 'xtra-downloaded-demo' ) ) {
					continue;
				}

				if ( $slug === 'feedback' && ! get_option( 'xtra_awaiting_seen_feedback_1' ) ) {

					$title .= '<span class="xtra-awaiting"><span>1</span></span>';

				}

				if ( $this->is_free && ( $slug === 'importer_page' || $slug === 'plugins' ) ) {

					$x = '';
					//$title .= self::pro_badge( false );

				}

				//if ( self::$plugin && method_exists( 'Codevz_Plus', 'add_sub' ) ) {

					if ( $slug === 'options' ) {

						//Codevz_Plus::add_sub( $this->slug, $title, $title, 'manage_options', 'customize.php', null );
						add_submenu_page( $this->slug, $title, $title, 'manage_options', 'customize.php', null );

					} else {

						//Codevz_Plus::add_sub( $this->slug, $title, $title, 'manage_options', 'xtra-' . $slug, [ $this, $slug ] );
						add_submenu_page( $this->slug, $title, $title, 'manage_options', 'xtra-' . $slug, [ $this, $slug ] );

					}

				//}

			}

		}

		/**
		 * Render before any tab content.
		 * 
		 * @return string.
		 */
		private function render_before( $active = null ) {

			echo '<div class="wrap xtra-dashboard-' . esc_attr( $active ) . '">';

			echo '<div class="xtra-dashboard">';

			echo '<div class="xtra-dashboard-header">';

				$title = self::option( 'white_label_theme_name', Xtra_Strings::get( 'theme_name' ) );

				echo '<img class="xtra-dashboard-logo" src="' . esc_html( self::option( 'white_label_welcome_page_logo', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAACRNJREFUeNrsXQuwTlUUXpfcrvK+aSRJD1IjKkwKPWQKka6GSSKRSapRU4nJTJqhGpRmxDCFHhTNeERJSUxPKhQpVIo8MvIWueG2vvkX3Tn3/P59zt7nnGXmfDPf8J/z/+fuvb/9WHvvtfbJKykpoRR6kHdCkNl5aWkkjaISKpeWgi6kgqSCpEgFSQVJERanRfjsAuY1zBbMS5h1mNXk3iHmDuZ65vfMJcxtCsrjHOYNzCbMBsyazIpybw9zM3MdcynzK+Y/2gWB7dyO2Y/ZXkQxxUrmm8wpkvm4gEpyL7Mn88oAv4MYHzBflX+dTOhczkOKmMOYjS2fs4/5MvM55oEIhajEHMJ8iFnF8lmrJO+zbechLgSpy5woLcMltjIHMN+NQIzOzPHM2o6fu4B5P3NTUhNDdEsrIhCDpLDmMMcyKzh6ZgV53pwIxCAphxVSLrFbWX2Z85iFEffx6FLmM8900EXNl+dFiUIpl75xCoJm+QqzfEwDb1vmXGZ+yN/nS9fXNqb0lpfy6R+HIGiO48SiihNtmJNC/naS/D5O5Ilx0j5KQeqKaVqeksHdMtAHwQD5XRIoL+VVNypBJsQwZuTCaOYFht/F90YlnN5CsUKdC1JkYz04BGbOLxl+dwzzDAVpbifl52xiiJvfOZj0uURz5rcnud80x/24sVqWZEpczENuUSYG8Ljl/bhxuUkPYypIb9KH25nVs9yrbtpFxIx7XAiCPvs2hZk7ndkpy72Ocl8bOtH/q8ehBWmV6yEJ4qaTTCQ1oqKUp5UgzUgvWgS8rgHNbAVpqDhzF1LZhccKcl0rGtoKcp7izGGDrZbnWi2KdifUFnVtBalMulHN87mq8vRWshUkX3kG83J81oZ8W0H2Kc/gQc/nQuXp3WcryA7lGfzrFOuydtgKsl5x5rZTWQ+VOsoFWW8ryCrFmfNL26XKBVllK8hixZn7xOdaI+WCLLYVBB6Fy5Vm7j2fpQnNs/TllMND03S19w2FmcP+zA+eay1J56KicTkGEWSvssyN9bnWRbm560wQWDLjFGXud+ZUn+7qTsWCjCMDn+Uge+rPM7coyRx2A4s91+AwXV2pGFuk/MilIPspeq8/E8BhbqbnGlZ4ByluHQ+T4YpHUDegOVn67riwUVqCF/C9Ol+pGHCWM/aKD+P9jsWxWcxbY84YjIrWlPHeKA0E2awl+5CCKABf4iKf7tUfIb3f8fBuMU8YdzM7+IgBTFAqBsqnq7EYIbus48AKK1xapseQMcRaXMf80ufeo6TTAWO6lM/BoD+0CUc4zOwuA9ahCAfwq3wmgCRCjFImxCEpj+5SPhSnIKUHrUZSeC4Hb8wpEOm00+c+vEpmUHJO39kqTyMpj9BwFRa9QQoP7p3vhK0dshzShzIRsDOyDX2UCYgpUCDCYclvc8n/BtsHRnX4DCZocAqD39S1zHrk73iAzRqEgC1hvp9l0D6RVplrjEiwZRyRVQKMZ4sos7i5y9nTHQV9mprKiOmrLP/fXYomgCfJJLG0oqztS2W8WivGxG4ZmItlYrw1qNUUVJC43GWKpWYFBWbgiHkfHtGyyF6ZU73N/CKMVeQaWv2XKsqM/Anp7lwDlQOBP5MjtBBPeUEgQksxGe+gaJwV0AUNpUxA5r8aa2JcgqDrOZcyTmL5QowLcEhoKOYidvqi3FyC0XAf809D67O2pBfh2PuE209VQUpbWVczL0qwNR5lPsUcSdmjl1AR2sn8ppVUkoIsYw4G/U8pc74Jxp1jLhPr2spqKqZpZ9KxlYoDYu6i7KutWCF+jNmDWSPksg4COseRix1Vh2Yvol1fpExUkxbAjEXgzsdZWvBwseBcHNsBMZ6hzNbEERtBXMzUsRexRpkY6Ka6ZRHjZuaPkm5XZ6hUlQr5NVmGb9gIgoF5mjRXbRFWg8h/bW2I9P21Ivq7OG/rG7JYgQ4rCGK/50v/rA1Yzhjjs+yC7uRZiv5Yw0oy2ewdlyBoGdMpe3xfkoBp2t/HmoIQcfoDYK0NJ80VxSHIaMoe/Zo0nqaynjE452RwAmmBKG8xr4jS7O0sJqTGoBjsodT3zMCxjL+C7M/assE6mQ787drKqkzJHMtkipE+yyHjExYDwImsw6LosgbL8odGYI1qik9r1jLODSTDyGBTQRBY+SDpBXYXvau2QxWlr4LpOGYqSE/SHSo2y/MZBzhrO/CgBxlENJsK0kuxGFgi+dynAmkD5m5dXQhSi3Qfr7HMp7vqqDStHV0I0oZ0w+sYcTHpPX3i+lxWqokgjZULstbzuYnitNbIVVlMBGmgXJA/fOx+zahvK0hN5Rn0Hth/lvL0nm0rSBXlGfTu1FVSnt7KtoIUK8+g1231iPL0FtsKsv8UayHa03vAVpDNijOH1uANxN+kXJBNtoL8pDhz8Db3rvCuUy6I9eEzyxVnbqnPNZxmfVRpen+hHLHqJoJ8Rsr8X0thkc+1PaTriPHSWJjrCyaCQIy5CjMH62pelnszlQoyw4UggMbDZxAzny2+ZBrpc6b+lTIuqE4EgS/TamUZHH2SewismaosvS+QwbsOTQXBg4Ypytw8g3FiBIWPdYzC1J1s8sUge+rYlVugIHMY0wYadhFawqYfMa0cQf2y8Ha2nQlnDlFVvxl+Fw7VKxNO7+sU4KyTciGaXq8E7XzEAgY5twu1sluClQjjbqCXmIXxXDz+csaSmDOHAy97h5yM4aCcuAM60Yo7BP27YR2PceBLvxhbCsIK4GcVduV5mYgS1ynd2MW8kUKsA9p4giNuvFMM3cEEKUzbN0cvoUxQ6c8Rp/cjyoTFbQzzY1vXfMxPcDjMhxFkDHMJROM+QO72ZBAf2FREPuY4vfDdfZIypwCFrqQuYiUw0CNgsoujySO6FbxLvSGVdYBzgf0iMpzpFjp43hGxpC6jjH+xldCugz7zpHvpIzUlyAExME/xmtLXyPzIDRdoJuY8YjmCvFkBXRLiZCYGMMNPjojPOimQWgjCEwQx6cfdUREdi4NnsDewSiyobQnPFxDPgZC01tI661HGbQcFg3WxXWKxrZHxyP1LCmI8fCaFoSDl0lLQhVSQVJAUqSCpICns5yEpUkFSlMV/AgwAF6roGQN4BTIAAAAASUVORK5CYII=' ) ) . '" alt="' . esc_attr( Xtra_Strings::get( 'theme_name' ) ) . '" />';

				echo '<div class="xtra-dashboard-title">' . esc_html( Xtra_Strings::get( 'welcome', $title ) ) . '<small>' . esc_html( Xtra_Strings::get( 'version' ) ) . ' <strong>' . esc_html( $this->theme->version ) . '</strong></small></div>';

				// White label check videos.
				if ( ! isset( $this->disable[ 'envato' ] ) ) {

					echo wp_kses_post( apply_filters( 'xtra_buy_market', '<a href="' . esc_url( Xtra_Strings::get( 'ref' ) ) . '" class="xtra-market" target="_blank"><img src="' . esc_url( self::$url ) . 'assets/img/envato.png" /></a>' ) );

				}

			echo '</div>';

			echo '<div class="xtra-dashboard-content">';

			echo '<div class="xtra-dashboard-menus">';

			$activation = get_option( $this->option );
			$activation = ( empty( $activation['purchase_code'] ) || ! empty( $_POST['deregister'] ) );

			foreach( $this->menus as $slug => $title ) {

				if ( $slug === 'uninstall' && ! get_option( 'xtra-downloaded-demo' ) ) {
					continue;
				}

				$link = ( $slug === 'options' ) ? 'customize.php' : 'admin.php?page=xtra-' . $slug;

				$img = ( $slug === 'activation' && ! $activation ) ? 'activated' : $slug;

				$additional = '';

				if ( $slug === 'feedback' && ! get_option( 'xtra_awaiting_seen_feedback_1' ) ) {

					$additional = '<span class="xtra-awaiting"><span>1</span></span>';

				}

				if ( $this->is_free && ( $slug === 'importer_page' || $slug === 'plugins' ) ) {

					$x = '';
					//$additional = self::pro_badge( false );

				}

				echo '<a href="' . esc_url( admin_url( $link ) ) . '" class="' . esc_attr( $active === $slug ? 'xtra-current' : '' ) . '"><img src="' . esc_url( self::$url ) . 'assets/img/' . esc_attr( $img ) . '.png" /><span>' . esc_html( $title ) . '</span>' . wp_kses_post( $additional ) . '</a>';

			}

			if ( isset( $this->disable[ 'faq' ] ) && isset( $this->disable[ 'docs' ] ) && isset( $this->disable[ 'youtube' ] ) && isset( $this->disable[ 'changelog' ] ) && isset( $this->disable[ 'ticksy' ] ) ) {
				$x = '';
			} else {
				echo '<div class="xtra-dashboard-menus-separator" aria-hidden="true"></div>';
			}

			if ( ! isset( $this->disable[ 'docs' ] ) ) {

				echo '<a href="' . esc_url( Xtra_Strings::get( 'docs' ) ) . '" target="_blank"><img src="' . esc_url( self::$url ) . 'assets/img/docs.png" /><span>' . esc_html( Xtra_Strings::get( 'documentation' ) ) . '</span></a>';

			}

			if ( ! isset( $this->disable[ 'youtube' ] ) ) {

				echo '<a href="' . esc_url( Xtra_Strings::get( 'youtube' ) ) . '" target="_blank"><img src="' . esc_url( self::$url ) . 'assets/img/videos.png" /><span>' . esc_html( Xtra_Strings::get( 'video_tutorials' ) ) . '</span></a>';

			}

			if ( ! isset( $this->disable[ 'changelog' ] ) ) {

				echo '<a href="' . esc_url( Xtra_Strings::get( 'changelog' ) ) . '" target="_blank"><img src="' . esc_url( self::$url ) . 'assets/img/changelog.png" /><span>' . esc_html( Xtra_Strings::get( 'change_log' ) ) . '</span></a>';

			}

			if ( ! isset( $this->disable[ 'ticksy' ] ) ) {

				echo '<a href="' . esc_url( Xtra_Strings::get( 'ticksy' ) ) . '" target="_blank"><img src="' . esc_url( self::$url ) . 'assets/img/support.png" /><span>' . esc_html( Xtra_Strings::get( 'support' ) ) . '</span></a>';

			}

			if ( ! isset( $this->disable[ 'faq' ] ) ) {

				echo '<a href="' . esc_url( Xtra_Strings::get( 'faqs' ) ) . '" target="_blank"><img src="' . esc_url( self::$url ) . 'assets/img/faq.png" /><span>' . esc_html( Xtra_Strings::get( 'faq' ) ) . '</span></a>';

			}

			echo '</div>';

			echo '<div class="xtra-dashboard-main">';

		}

		/**
		 * Activation tab content.
		 * 
		 * @return string.
		 */
		private function render_after() {

			echo '</div>'; // main.

			echo '</div>'; // content.

			echo '</div>'; // Dashboard.

			echo '</div>'; // Wrap.

		}

		/**
		 * Showing error or success message anywhere.
		 * 
		 * @return string.
		 */
		private function message( $type, $message ) {

			$icon = $type === 'error' ? 'no-alt' : ( $type === 'info' ? 'info-outline' : 'saved' );

			if ( $type === 'warning' ) {
				$icon = 'megaphone';
			}

			echo '<div class="xtra-dashboard-' . esc_attr( $type ) . '"><i class="dashicons dashicons-' . esc_attr( $icon ) . '" aria-hidden="true"></i><span>' . wp_kses_post( $message ) . '</span></div>';

		}

		/**
		 * Showing icon and text with custom style.
		 * 
		 * @return string.
		 */
		private function icon_box( $icon, $title, $link, $class = '' ) {

			if ( $class ) {
				$class = ' xtra-dashboard-icon-box-' . $class;
			}

			echo '<a href="' . esc_url( $link ) . '" class="xtra-dashboard-icon-box' . esc_attr( $class ) . '" target="_blank"><i class="dashicons dashicons-' . esc_attr( $icon ) . '" aria-hidden="true"></i><div>' . wp_kses_post( $title ) . '</div></a>';

		}

		/**
		 * Show activation successful message.
		 * 
		 * @return string.
		 */
		private function activated_successfully() {

			$activation = get_option( $this->option );

			if ( empty( $activation['purchase_code'] ) ) {

				delete_option( $this->option );

				header( "Refresh:0" );

			}
			
			$expired = current_time( 'timestamp' ) > strtotime( $activation['support_until'] );

			echo '<div class="xtra-certificate">';

				echo '<div class="xtra-certificate-title">' . esc_html( Xtra_Strings::get( 'certificate' ) );

				echo '<form method="post"><input type="hidden" name="deregister" value="1"><input type="submit" value="' . esc_attr( Xtra_Strings::get( 'deregister_license' ) ) . '"></form>';

				echo '</div>';

				echo '<div class="xtra-purchase-code">' . esc_html( Xtra_Strings::get( 'purchase_code' ) ) . '<div>' . esc_html( str_replace( substr( $activation['purchase_code'], -12, 10 ), '************', $activation['purchase_code'] ) ) . '</div></div>';

				echo '<div class="xtra-purchase-details">';

				$this->icon_box( 'calendar', '<b>' . esc_html( Xtra_Strings::get( 'purchase_date' ) ) . '</b><span>' . date( 'd F Y', strtotime( esc_html( $activation['purchase_date'] ) ) ) . '</span>', '#', 'info' );

				$this->icon_box( 'sos', '<b>' . esc_html( Xtra_Strings::get( 'support_until' ) ) . '</b><span>' . date( 'd F Y', strtotime( esc_html( $activation['support_until'] ) ) ) . '</span>', '#', ( $expired ? 'error' : 'info' ) );

				echo '</div>';

			echo '</div>';

			if ( $expired ) {

				$this->message( 'error', esc_html( Xtra_Strings::get( 'support_expired' ) ) );

			}

			$this->icon_box( 'sos', esc_html( Xtra_Strings::get( 'extend' ) ), esc_html( Xtra_Strings::get( 'ref' ) ), 'info' );

		}

		/**
		 * Activation tab content.
		 * 
		 * @return string.
		 */
		public function activation() {

			$this->render_before( 'activation' );

			ob_start();

			do_action( 'xtra_dashboard_activation_before' );

			$action = ob_get_clean();

			if ( $action ) {

				echo wp_kses_post( $action );

				$this->render_after();

			} else {

				// Get saved activation.
				$activation = get_option( $this->option );

				// Purchase code.
				$purchase_code = isset( $activation[ 'purchase_code' ] ) ? $activation[ 'purchase_code' ] : null;

				echo '<div class="xtra-dashboard-section-title">' . esc_html( Xtra_Strings::get( 'license_activation' ) ) . '</div>';

				$form = true;

				// Deregister license.
				if ( ! empty( $_POST['deregister'] ) ) {

					$this->message( 'success', esc_html( Xtra_Strings::get( 'deregistered' ) ) );

				} else if ( $purchase_code ) {

					if ( isset( $_POST[ 'register' ] ) ) {

						$this->message( 'success', esc_html( Xtra_Strings::get( 'congrats' ) ) . ', ' . esc_html( Xtra_Strings::get( 'activated' ) ) );

					}

					$this->activated_successfully();

					$form = false;

				} else if ( ! empty( $_POST[ 'register' ] ) ) {

					$this->message( 'error', esc_html( Xtra_Strings::get( 'insert' ) ) );

				}

				if ( $form ) {

					echo '<p>' . esc_html( Xtra_Strings::get( 'activate_war' ) ) . '</p>';

					echo '<form class="xtra-dashboard-activation-form" method="post"><input type="text" name="register" placeholder="' . esc_attr( Xtra_Strings::get( 'placeholder' ) ) . '" required><input type="submit" value="' . esc_attr( Xtra_Strings::get( 'activate' ) ) . '"></form>';

					$this->icon_box( 'editor-help', esc_html( Xtra_Strings::get( 'find' ) ), 'https://xtratheme.com/docs/getting-started/how-to-activate-theme-with-license-code/', 'info' );

					$this->icon_box( 'cart', esc_html( Xtra_Strings::get( 'buy_new' ) ), esc_html( Xtra_Strings::get( 'ref' ) ), 'success' );

				}

				$this->render_after();

			}

		}

		/**
		 * Plugins installation tab content.
		 * 
		 * @return string.
		 */
		public function plugins() {

			$this->render_before( 'plugins' );

			echo '<div class="xtra-dashboard-section-title">' . esc_html( Xtra_Strings::get( 'install' ) ) . '</div>';

			//if ( $this->is_free ) {

				//$this->status_item( 'warning', esc_html( Xtra_Strings::get( 'page_pro', '<br />' ) ), '', '<a href="' . esc_url( get_admin_url() ) . 'admin.php?page=xtra-activation" target="_blank">' . esc_html( Xtra_Strings::get( 'activate' ) ) . '</a>' );

			//}

			echo '<div class="xtra-plugins" data-nonce="' . esc_attr( wp_create_nonce( 'xtra-wizard' ) ) . '">';

			$plugins = 0;

			foreach( $this->plugins as $slug => $plugin ) {

				// Check plugin.
				if ( $this->plugin_is_active( $slug ) ) {
					continue;
				}

				echo '<div class="xtra-plugin">';

					echo '<div class="xtra-plugin-header">';

					echo '<img src="' . esc_url( self::$url ) . 'assets/img/' . esc_attr( $slug ) . '.jpg" alt="' . esc_attr( $plugin[ 'name' ] ) . '" />';
					
					if ( isset( $plugin[ 'required' ] ) ) {

						$plugin[ 'name' ] .= '<small>' . esc_html( Xtra_Strings::get( 'required' ) ) . '</small>';

					} else if ( isset( $plugin[ 'recommended' ] ) ) {

						$plugin[ 'name' ] .= '<small>' . esc_html( Xtra_Strings::get( 'recommended' ) ) . '</small>';

					}

					echo '<span>' . wp_kses_post( $plugin[ 'name' ] ) . '</span>';

					echo '</div>';

					echo '<div class="xtra-plugin-footer">';

						echo '<div class="xtra-plugin-details">';

						if ( isset( $plugin[ 'source' ] ) ) {
							echo esc_html( Xtra_Strings::get( 'private' ) ) . '<br /><span>' . esc_html( Xtra_Strings::get( 'premium' ) ) . '</span>';
						} else {
							echo esc_html( Xtra_Strings::get( 'wp' ) ) . '<br /><span>' . esc_html( Xtra_Strings::get( 'free_ver' ) ) . '</span>';
						}

						echo '</div>';

						if ( file_exists( $this->plugin_file( $slug, true ) ) ) {

							$title = Xtra_Strings::get( 'activate' );

							$activated = Xtra_Strings::get( 'activated_s' );

						} else {

							$title = Xtra_Strings::get( 'install_activate' );

							$activated = Xtra_Strings::get( 'installed_activated' );

						}

						if ( $this->is_free && ( $slug === 'codevz-plus' || $slug === 'revslider' || $slug === 'js_composer' ) ) {

							$title = Xtra_Strings::get( 'unlock' );

							echo '<a href="' . esc_url( get_admin_url() ) . 'admin.php?page=xtra-activation" class="xtra-button-primary"><span>' . esc_html( $title ) . '</span></a>';

						} else {

							echo '<a href="#" class="xtra-button-primary" data-plugin="' . esc_attr( $slug ) . '" data-title="' . esc_attr( Xtra_Strings::get( 'please_wait' ) ) . '"><span>' . esc_html( $title ) . '</span><i class="xtra-loading" aria-hidden="true"></i></a>';

						}

						echo '<div class="xtra-plugin-activated hidden"><i class="dashicons dashicons-yes" aria-hidden="true"></i> ' . esc_html( $activated ) . '</div>';

					echo '</div>';

					echo '<div class="xtra-plugin-progress" aria-hidden="true"></div>';

				echo '</div>';

				$plugins++;

			}

			echo '</div>';

			if ( ! $plugins ) {

				$this->message( 'success', Xtra_Strings::get( 'no_plugins' ) );

			}

			$this->render_after();

		}

		/**
		 * Demo importer tab content.
		 * 
		 * @return string.
		 */
		public function importer() {

			$this->render_before( 'importer' );

			$activation = get_option( $this->option );

			echo '<div class="xtra-demo-importer">';

			echo '<div class="xtra-filters">';

				if ( self::$premium && apply_filters( 'xtra_config_filters', true ) ) {

					echo '<div class="xtra-filters-title">' . esc_html( Xtra_Strings::get( 'filters' ) ) . '</div>';

					echo '<a href="#" data-filter="" class="xtra-filters-all xtra-current">' . esc_html( Xtra_Strings::get( 'all' ) ) . '<span>' . count( $this->demos ) . '</span></a>';
					echo '<a href="#" data-filter="free">' . esc_html( Xtra_Strings::get( 'starter' ) ) . '<span>11</span></a>';
					echo '<a href="#" data-filter="pro">' . esc_html( Xtra_Strings::get( 'exclusive' ) ) . '<span>84</span></a>';

				}

				echo '<input type="search" name="search" placeholder="' . esc_html( Xtra_Strings::get( 'type' ) ) . '" />';

				echo '<i class="dashicons dashicons-search" aria-hidden="true"></i>';

			echo '</div>';

			echo '<div class="xtra-demos xtra-lazyload clearfix">';

			foreach( $this->demos as $demo => $args ) {

				$rtl 	= is_rtl() && isset( $args[ 'rtl' ] ) ? 'rtl/' : '';
				$folder = apply_filters( 'xtra_rtl_checker', $rtl );

				$api = apply_filters( 'xtra_config_api', self::$api );

				$preview = $rtl ? 'arabic/' : '';
				$preview = str_replace( 'api', $preview . esc_attr( $demo ), $api );
				$preview = apply_filters( 'xtra_rtl_preview', $preview );

				$args[ 'demo' ] = $demo;
				$args[ 'image' ] = $api . 'demos/' . $folder . esc_attr( $demo ) . '.jpg';
				$args[ 'preview' ] = $preview;

				$is_pro = empty( $args[ 'free' ] );

				if ( $is_pro && ! self::$premium ) {
					continue;
				}

				echo '<div class="xtra-demo">';

					$keywords = isset( $args[ 'keywords' ] ) ? $args[ 'keywords' ] : '';

					$keywords .= empty( $args[ 'rtl' ] ) ? '' : ' rtl arabic';
					$keywords .= $is_pro ? ' pro' : ' free';
					$keywords .= empty( $args[ 'js_composer' ] ) ? ' js_composer wpbakery' : '';

					if ( ! empty( $args[ 'elementor' ] ) || ! empty( $args[ 'rtl' ][ 'elementor' ] ) ) {
						$keywords .= ' elementor';
					}

					$keywords .= ' ' . $demo;

					if ( $this->is_free ) {

						if ( $is_pro ) {
							echo '<div class="xtra-demo-pro-badge" title="' . esc_attr( Xtra_Strings::get( 'activate_war' ) ) . '"><span class="dashicons dashicons-lock" aria-hidden="true"></span></div>';
						} else {
							echo '<div class="xtra-demo-free-badge">' . esc_attr( Xtra_Strings::get( 'free' ) ) . '</div>';
						}

					}

					// Keywords.
					echo '<div class="hidden">' . esc_html( $keywords ) . '</div>';

					// Preview image.
					echo '<img data-src="' . esc_url( $args[ 'image' ] ) . '" />';

					// Demo title.
					echo '<div class="xtra-demo-title">' . esc_html( ucwords( str_replace( '-', ' ', isset( $args[ 'title' ] ) ? $args[ 'title' ] : $args[ 'demo' ] ) ) ) . '</div>';

					// Buttons.
					echo '<div class="xtra-demo-buttons">';

						if ( empty( $activation['purchase_code'] ) && empty( $args[ 'free' ] ) ) {

							echo '<a href="' . esc_url( get_admin_url() ) . 'admin.php?page=xtra-activation" class="xtra-button-primary">' . esc_html( Xtra_Strings::get( 'unlock' ) ) . '</a>';

						} else {

							echo '<a href="#" class="xtra-button-primary" data-args=\'' . esc_html( json_encode( $args ) ) . '\'>' . esc_html( Xtra_Strings::get( 'import' ) ) . '</a>';

						}

						if ( get_option( 'xtra_uninstall_' . $demo ) && self::$premium ) {

							echo '<a href="' . esc_url( get_admin_url() ) . 'admin.php?page=xtra-uninstall" class="xtra-button-secondary xtra-uninstall-button">' . esc_html( Xtra_Strings::get( 'uninstall' ) ) . '</a>';

						} else {

							if ( self::contains( $args[ 'preview' ], 'arabic' ) ) {

								$args[ 'preview' ] = str_replace( '/' . $demo, '-elementor/' . $demo, $args[ 'preview' ] );

							} else {

								$args[ 'preview' ] = str_replace( $demo, 'elementor/' . $demo, $args[ 'preview' ] );

							}

							echo '<a href="' . esc_url( $args[ 'preview' ] ) . '" class="xtra-button-secondary" target="_blank">' . esc_html( Xtra_Strings::get( 'preview' ) ) . '</a>';

						}

					echo '</div>';

				echo '</div>';

			}

			echo '</div>';

			echo '</div>';

			// Wizard.
			echo '<div class="xtra-wizard hidden" data-nonce="' . esc_attr( wp_create_nonce( 'xtra-wizard' ) ) . '">';

				echo '<i class="xtra-back dashicons dashicons-arrow-left-alt"><span>' . esc_html( Xtra_Strings::get( 'back' ) ) . '</span></i>';

				echo '<div class="xtra-wizard-main">';

					echo '<div class="xtra-wizard-preview">';

						// Demo image.
						echo '<img class="xtra-demo-image" src="#" alt="Demo preview" />';

						// Progress bar.
						echo '<img class="xtra-importer-spinner" src="' . esc_url( self::$url ) . 'assets/img/importing.png" />';
						echo '<div class="xtra-wizard-progress"><div data-current="0"><span></span></div></div>';

					echo '</div>';

					echo '<div class="xtra-wizard-content">';

						// Step 1.
						echo '<div data-step="1" class="xtra-current">';

							echo '<div class="xtra-wizard-welcome"><span>' . esc_html( Xtra_Strings::get( 'welcome_to' ) ) . '</span><strong>' . esc_html( Xtra_Strings::get( 'wizard' ) ) . '</strong></div>';

							echo '<div class="xtra-wizard-selected"><span>' . esc_html( Xtra_Strings::get( 'selected' ) ) . '</span><strong>...</strong></div>';

							echo '<div class="xtra-wizard-selected"><span>' . esc_html( Xtra_Strings::get( 'live_preview' ) ) . '</span><br /><br />';

								echo '<a href="#" class="xtra-live-preview xtra-live-preview-elementor xtra-button-secondary" target="_blank">' . esc_html( Xtra_Strings::get( 'elementor_s' ) ) . '</a>';

								echo '<a href="#" class="xtra-live-preview xtra-live-preview-wpbakery xtra-button-secondary" target="_blank">' . esc_html( Xtra_Strings::get( 'wpbakery' ) ) . '</a>';

							echo '</div>';

						echo '</div>'; // step 1.

						// Step 2.
						echo '<div data-step="2">';

							echo '<div class="xtra-step-title">' . esc_html( Xtra_Strings::get( 'choose' ) ) . '</div>';

							echo '<div class="xtra-image-radios">';
								echo '<label class="xtra-image-radio"><input type="radio" name="pagebuilder" value="elementor" checked /><span><img src="' . esc_url( self::$url ) . 'assets/img/elementor.jpg"><b>' . esc_html( Xtra_Strings::get( 'elementor_s' ) ) . '</b></span></label>';
								echo '<label class="xtra-image-radio"><input type="radio" name="pagebuilder" value="js_composer" /><span data-tooltip="' . esc_attr( Xtra_Strings::get( 'ata' ) ) . '"><img src="' . esc_url( self::$url ) . 'assets/img/js_composer.jpg"><b>' . esc_html( Xtra_Strings::get( 'wpbakery' ) ) . '</b></span></label>';
							echo '</div>';

							$free = $this->is_free;

							echo do_shortcode( apply_filters( 'xtra_rtl_checkbox', '<label class="xtra-checkbox xtra-rtl ' . ( $free ? 'xtra-readonly' : '' ) . '" data-tooltip="' . esc_attr( $free ? Xtra_Strings::get( 'ata' ) : Xtra_Strings::get( 'desc' ) ) . '">' . esc_html( Xtra_Strings::get( 'rtl' ) ) . '<input type="checkbox" name="rtl" ' . ( is_rtl() ? 'checked' : '' ) . ' /><span class="checkmark" aria-hidden="true"></span></label>' ) );

						echo '</div>'; // step 2.

						// Step 3.
						echo '<div data-step="3">';

							echo '<label class="xtra-radio"><input type="radio" name="config" value="full" checked /><b>' . esc_html( Xtra_Strings::get( 'full_import' ) ) . '</b><span class="checkmark" aria-hidden="true"></span></label>';
							echo '<label class="xtra-radio"><input type="radio" name="config" value="custom" /><b>' . esc_html( Xtra_Strings::get( 'custom_import' ) ) . '</b><span class="checkmark" aria-hidden="true"></span></label>';

							echo '<div class="xtra-checkboxes clearfix" disabled>';
								echo '<label class="xtra-checkbox">' . esc_html( Xtra_Strings::get( 'options' ) ) . '<input type="checkbox" name="options" checked /><span class="checkmark" aria-hidden="true"></span></label>';
								echo '<label class="xtra-checkbox">' . esc_html( Xtra_Strings::get( 'widgets' ) ) . '<input type="checkbox" name="widgets" checked /><span class="checkmark" aria-hidden="true"></span></label>';
								echo '<label class="xtra-checkbox">' . esc_html( Xtra_Strings::get( 'posts' ) ) . '<input type="checkbox" name="content" checked /><span class="checkmark" aria-hidden="true"></span></label>';
								echo '<label class="xtra-checkbox">' . esc_html( Xtra_Strings::get( 'media' ) ) . '<input type="checkbox" name="images" checked /><span class="checkmark" aria-hidden="true"></span></label>';
								echo '<label class="xtra-checkbox">' . esc_html( Xtra_Strings::get( 'woocommerce' ) ) . '<input type="checkbox" name="woocommerce" checked /><span class="checkmark" aria-hidden="true"></span></label>';
								echo '<label class="xtra-checkbox">' . esc_html( Xtra_Strings::get( 'revslider' ) ) . '<input type="checkbox" name="slider" checked /><span class="checkmark" aria-hidden="true"></span></label>';
							echo '</div>';

						echo '</div>'; // step 3.

						// Step 4.
						echo '<div data-step="4"><ul class="xtra-list"></ul></div>';

						// Step 5.
						echo '<div data-step="5">';

							// Success.
							echo '<div class="xtra-importer-done xtra-demo-success">';

								echo '<img src="' . esc_url( self::$url ) . 'assets/img/tick.png" />';
								echo '<span>' . esc_html( Xtra_Strings::get( 'congrats' ) ) . '</span>';
								echo '<p>' . esc_html( Xtra_Strings::get( 'imported' ) ) . '</p>';

								echo '<a href="' . esc_url( get_home_url() ) . '" class="xtra-button-primary" target="_blank">' . esc_html( Xtra_Strings::get( 'view_website' ) ) . '</a>';
								echo '<a href="' . esc_url( get_admin_url() ) . 'customize.php" class="xtra-button-secondary" target="_blank">' . esc_html( Xtra_Strings::get( 'customize' ) ) . '</a>';

							echo '</div>';

							// Error.
							echo '<div class="xtra-importer-done xtra-demo-error hidden">';

								echo '<img src="' . esc_url( self::$url ) . 'assets/img/error.png" />';
								echo '<span>' . esc_html( Xtra_Strings::get( 'error' ) ) . '</span>';
								echo '<p>' . esc_html( Xtra_Strings::get( 'occured' ) ) . '</p>';

								echo '<a href="' . esc_html( Xtra_Strings::get( 'docs' ) ) . '" class="xtra-button-primary" target="_blank">' . esc_html( Xtra_Strings::get( 'troubleshooting' ) ) . '</a>';
								echo '<a href="#" class="xtra-button-secondary xtra-back-to-demos">' . esc_html( Xtra_Strings::get( 'back' ) ) . '</a>';

							echo '</div>';

						echo '</div>'; // step 5.

					echo '</div>';

				echo '</div>';

				// Wizard footer.
				echo '<div class="xtra-wizard-footer">';

					echo '<a href="#" class="xtra-button-secondary xtra-wizard-prev">' . esc_html( Xtra_Strings::get( 'prev_step' ) ) . '</a>';

					echo '<ul class="xtra-wizard-steps clearfix">';
						echo '<li data-step="1" class="xtra-current"><span>' . esc_html( Xtra_Strings::get( 'getting_started' ) ) . '</span></li>';
						echo '<li data-step="2"><span>' . esc_html( Xtra_Strings::get( 'choose_2' ) ) . '</span></li>';
						echo '<li data-step="3"><span>' . esc_html( Xtra_Strings::get( 'config' ) ) . '</span></li>';
						echo '<li data-step="4"><span>' . esc_html( Xtra_Strings::get( 'importing' ) ) . '</span></li>';
						//echo '<li data-step="5"><span>' . Xtra_Strings::get( 'ready' ) . '</span></li>';
					echo '</ul>';

					echo '<a href="#" class="xtra-button-primary xtra-wizard-next">' . esc_html( Xtra_Strings::get( 'next_step' ) ) . '</a>';

				echo '</div>';

			echo '</div>';

			$this->render_after();

		}

		/**
		 * Page importer.
		 * 
		 * @return string.
		 */
		public function importer_page() {

			$this->render_before( 'importer_page' );

			echo '<div class="xtra-dashboard-section-title">' . esc_html( Xtra_Strings::get( 'single_page' ) ) . '</div>';

			if ( $this->is_free ) {

				$this->status_item( 'warning', wp_kses_post( Xtra_Strings::get( 'page_pro', '<br />' ) ), '', '<a href="' . esc_url( get_admin_url() ) . 'admin.php?page=xtra-activation" target="_blank">' . esc_html( Xtra_Strings::get( 'activate' ) ) . '</a>' );

				$this->render_after();

				return;

			}

			if ( ! self::option( 'site_color_sec' ) ) {

				$this->message( 'warning', esc_html( Xtra_Strings::get( 'page_import_war' ) ) );

			}

			echo '<p style="font-size:14px;color:#7e7e7e;">' . esc_html( Xtra_Strings::get( 'page_insert' ) ) . '</p>';

			echo '<br /><form class="xtra-page-importer-form">';

				echo '<input type="url" placeholder="' . esc_attr( Xtra_Strings::get( 'page_insert_link' ) ) . '" />';

				echo '<a href="#" class="xtra-button-primary" data-nonce="' . esc_attr( wp_create_nonce( 'xtra-page-importer' ) ) . '"><span>' . esc_html( Xtra_Strings::get( 'import' ) ) . '</span><i class="xtra-loading" aria-hidden="true"></i></a>';

				echo '<br /><br /><br /><span class="xtra-page-importer-message" aria-hidden="true"></span>';

			echo '</form>';

			$this->render_after();

		}

		/**
		 * Single page importer AJAX request.
		 * 
		 * @return JSON
		 */
		public function xtra_page_importer() {

			check_ajax_referer( 'xtra-page-importer', 'nonce' );

			// Check activation.
			if ( $this->is_free ) {

				wp_send_json(
					[
						'status' 	=> '202',
						'message' 	=> Xtra_Strings::get( 'activation_error' )
					]
				);

			}

			// Check requested URL.
			if ( ! empty( $_POST[ 'url' ] ) ) {

				$url = sanitize_text_field( wp_unslash( $_POST[ 'url' ] ) );

				if ( filter_var( $url, FILTER_VALIDATE_URL ) === FALSE || ! self::contains( $url, [ 'xtratheme', 'themetor', 'codevz' ] ) ) {

					wp_send_json(
						[
							'status' 	=> '202',
							'message' 	=> Xtra_Strings::get( 'valid_url' )
						]
					);

				}

				$url = sanitize_text_field( $url );

				// Check codevz plus plugin.
				if ( ! $this->plugin_is_active( 'codevz-plus' ) ) {

					$data = $this->install_plugin( 'codevz-plus' );

					if ( is_string( $data ) ) {

						wp_send_json(
							[

								'status' 	=> '202',
								'message' 	=> esc_html( Xtra_Strings::get( 'find_plugin', 'codevz-plus' ) )

							]
						);

					}

				}

				// Check Elementor plugin.
				if ( self::contains( $url, '/elementor' ) && ! $this->plugin_is_active( 'elementor' ) ) {

					$data = $this->install_plugin( 'elementor' );

					if ( is_string( $data ) ) {

						wp_send_json(
							[

								'status' 	=> '202',
								'message' 	=> esc_html( Xtra_Strings::get( 'find_plugin', 'elementor' ) )

							]
						);

					}

				}

				// Get requested page content.
				$response = wp_remote_get( $url . '?export_single_page=' . $url );

				// Check data.
				if ( empty( $response['body'] ) && method_exists( 'Codevz_Plus', 'fgc' ) ) {

					$response = Codevz_Plus::fgc( $url . '?export_single_page=' . $url );

				}

				if ( empty( $response['body'] ) && ! ini_get( 'allow_url_fopen' ) ) {

					wp_send_json(
						[
							'status' 	=> '202',
							'message' 	=> Xtra_Strings::get( 'allow_url_fopen' )
						]
					);

				}

				if ( ! empty( $response[ 'body' ] ) ) {

					$response = json_decode( $response['body'], true );

					if ( ! empty( $response[ 'page' ] ) ) {

						// Start.
						$page = json_decode( $response[ 'page' ] );

						$page->ID = null;

						$page_exist = get_page_by_path( $page->post_name );

						if ( ! empty( $page_exist->ID ) ) {
							$page->post_name = $page->post_name . rand( 111, 999 );
						}

						$page->post_title = $page->post_title . ' (Imported)';

						// Replace colors.
						if ( $page->post_content ) {

							if ( $response[ 'color2' ] ) {
								$color2 = self::option( 'site_color_sec' ) ? self::option( 'site_color_sec' ) : $response[ 'color1' ];
								$page->post_content = Codevz_Options::updateDatabase( $response[ 'color2' ], $color2, $page->post_content );
							}

							if ( $response[ 'color1' ] ) {
								$page->post_content = Codevz_Options::updateDatabase( $response[ 'color1' ], self::option( 'site_color' ), $page->post_content );
							}

						}

						$post_id = wp_insert_post( $page );

						if ( $post_id && ! empty( $response[ 'meta' ] ) ) {

							$meta = json_encode( $response[ 'meta' ] );

							if ( $response[ 'color2' ] ) {
								$color2 = self::option( 'site_color_sec' ) ? self::option( 'site_color_sec' ) : $response[ 'color1' ];
								$meta = Codevz_Options::updateDatabase( $response[ 'color2' ], $color2, $meta );
								$meta = Codevz_Options::updateDatabase( strtoupper( $response[ 'color2' ] ), strtoupper( $color2 ), $meta );
							}

							if ( $response[ 'color1' ] ) {
								$meta = Codevz_Options::updateDatabase( $response[ 'color1' ], self::option( 'site_color' ), $meta );
								$meta = Codevz_Options::updateDatabase( strtoupper( $response[ 'color1' ] ), strtoupper( self::option( 'site_color' ) ), $meta );
							}

							$meta = Codevz_Demo_Importer::replace_upload_url( $meta, true );

							$meta = Codevz_Demo_Importer::replace_demo_link( $meta, false, false, 'elementor/' );
							$meta = Codevz_Demo_Importer::replace_demo_link( $meta, true, false, 'elementor/' );

							update_post_meta( $post_id, '_elementor_data', wp_slash_strings_only( $meta ) );
							update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
							update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
							update_post_meta( $post_id, '_elementor_version', '3.4.3' );

						}

						// Get code.
						$code = get_option( $this->option );
						$code = empty( $code['purchase_code'] ) ? '' : $code['purchase_code'];

						// Stats.
						$prms = [

							'api' 		=> self::$api,
							'code' 		=> $code,
							'page' 		=> str_replace( [ 'http://', 'https://', '.', '/' ], [ '', '', '-', '_' ], rtrim( $url, '/\\' ) ),
							'builder' 	=> self::contains( $url, 'elementor' ) ? 'elementor' : 'wpbakery',
							'domain' 	=> get_permalink( $post_id )

						];
						$stats = wp_remote_get( 'http://theme.support/importer-stats/?import_page=' . json_encode( $prms ) );

						wp_send_json(
							[
								'status' 	=> '200',
								'message' 	=> Xtra_Strings::get( 'page_imported' ),
								'link' 		=> get_permalink( $post_id )
							]
						);

					} else if ( ! empty( $response[ 'message' ] ) ) {

						wp_send_json(
							[
								'status' 	=> '202',
								'message' 	=> $response[ 'message' ]
							]
						);

					} else if ( is_wp_error( $response ) ) {

						wp_send_json(
							[
								'status' 	=> '202',
								'message' 	=> $response->get_error_message()
							]
						);

					} else {

						wp_send_json(
							[
								'status' 	=> '202',
								'message' 	=> Xtra_Strings::get( 'try_again' )
							]
						);

					}

				}

				wp_send_json(
					[
						'status' 	=> '202',
						'message' 	=> Xtra_Strings::get( 'responding' )
					]
				);

			}

			wp_send_json(
				[
					'status' 	=> '202',
					'message' 	=> Xtra_Strings::get( 'wrong' )
				]
			);

		}

		/**
		 * System status item content.
		 * 
		 * @return string.
		 */
		private function status_item( $type, $title, $value, $badge ) {

			echo '<div class="xtra-ss-item xtra-dashboard-' . esc_attr( $type === 'error' ? 'error' : ( $type === 'warning' ? 'warning' : 'success' ) ) . '">';

				echo '<img src="' . esc_url( self::$url ) . 'assets/img/' . esc_attr( $type === 'error' ? 'error' : ( $type === 'warning' ? 'warning' : 'tick' ) ) . '.png" />';

				echo '<b>' . wp_kses_post( $title ) . '</b>';

				echo '<span>' . wp_kses_post( $value ) . '<i>' . wp_kses_post( $badge ) . '</i></span>';

			echo '</div>';

		}

		/**
		 * System status tab content.
		 * 
		 * @return string.
		 */
		public function status() {

			$this->render_before( 'status' );

			echo '<div class="xtra-dashboard-section-title">' . esc_html( Xtra_Strings::get( 'status' ) ) . '</div>';

			echo '<div class="xtra-system-status">';

				// Theme Activated or no.
				if ( ! $this->is_free ) {

					$this->status_item( 'success', esc_html( Xtra_Strings::get( 'tas' ) ), '', esc_html( Xtra_Strings::get( 'good' ) ) );

				} else {

					$this->status_item( 'warning', esc_html( Xtra_Strings::get( 'not_active' ) ), '', '<a href="' . esc_url( get_admin_url() ) . 'admin.php?page=xtra-activation" target="_blank">' . esc_html( Xtra_Strings::get( 'activate' ) ) . '</a>' );

				}

				// PHP version.
				if ( version_compare( phpversion(), '7.0.0', '>=' ) ) {

					$this->status_item( 'success', esc_html( Xtra_Strings::get( 'php_ver' ) ), phpversion(), esc_html( Xtra_Strings::get( 'good' ) ) );

				} else {

					$this->status_item( 'error', esc_html( Xtra_Strings::get( 'php_ver' ) ), phpversion(), esc_html( Xtra_Strings::get( 'php_error' ) ) );

				}

				// PHP Memory limit.
				$memory_limit = ini_get( 'memory_limit' );
				if ( (int) $memory_limit >= 128 || (int) $memory_limit < 0 ) {

					$this->status_item( 'success', esc_html( Xtra_Strings::get( 'php_memory' ) ), $memory_limit, esc_html( Xtra_Strings::get( 'good' ) ) );

				} else {

					$this->status_item( 'error', esc_html( Xtra_Strings::get( 'php_memory' ) ), $memory_limit, esc_html( Xtra_Strings::get( '128m' ) ) );

				}

				// PHP post max size.
				$pms = ini_get( 'post_max_size' );
				if ( (int) $pms >= 8 ) {

					$this->status_item( 'success', esc_html( Xtra_Strings::get( 'max_size' ) ), $pms, esc_html( Xtra_Strings::get( 'good' ) ) );

				} else {

					$this->status_item( 'error', esc_html( Xtra_Strings::get( 'max_size' ) ), $pms, esc_html( Xtra_Strings::get( '8r' ) ) );

				}

				// PHP max execution time.
				$met = ini_get( 'max_execution_time' );
				if ( (int) $met >= 30 ) {

					$this->status_item( 'success', esc_html( Xtra_Strings::get( 'execution' ) ), $met, esc_html( Xtra_Strings::get( 'good' ) ) );

				} else {

					$this->status_item( 'error', esc_html( Xtra_Strings::get( 'execution' ) ), $met, esc_html( Xtra_Strings::get( '30r' ) ) );

				}

				// cURL or fopen.
				if ( ini_get( 'allow_url_fopen' ) ) {

					$this->status_item( 'success', esc_html( Xtra_Strings::get( 'server_php' ) ) . ' allow_url_fopen', esc_html( Xtra_Strings::get( 'active' ) ), esc_html( Xtra_Strings::get( 'good' ) ) );

				} else if ( function_exists( 'curl_version' ) ) {

					$this->status_item( 'success', esc_html( Xtra_Strings::get( 'server_php' ) ) . ' cURL', esc_html( Xtra_Strings::get( 'active' ) ), esc_html( Xtra_Strings::get( 'good' ) ) );

				} else {

					$this->status_item( 'error', esc_html( Xtra_Strings::get( 'curl' ) ), '', esc_html( Xtra_Strings::get( 'contact' ) ) );

				}

			echo '</div>';

			$this->render_after();

		}

		/**
		 * Feedback tab content.
		 * 
		 * @return string.
		 */
		public function feedback() {

			$this->render_before( 'feedback' );

			echo '<div class="xtra-dashboard-section-title">' . esc_html( Xtra_Strings::get( 'feedback' ) ) . '</div>';

			if ( ! get_option( 'xtra_awaiting_seen_feedback_1' ) ) {

				$this->message( 'warning', esc_html( Xtra_Strings::get( 'please_help', Xtra_Strings::get( 'theme_name' ) ) ) );

				update_option( 'xtra_awaiting_seen_feedback_1', true );

			}

			echo '<p style="font-size:14px;color:#7e7e7e;">' . esc_html( Xtra_Strings::get( 'thanks', Xtra_Strings::get( 'theme_name' ) ) ) . '</p>';

			echo '<br /><form class="xtra-feedback-form">';

				wp_editor( false, 'xtra-feedback', [ 'media_buttons' => true, 'textarea_rows' => 10 ] );

				echo '<br /><br /><a href="#" class="xtra-button-primary" data-nonce="' . esc_attr( wp_create_nonce( 'xtra-feedback' ) ) . '"><span>' . esc_html( Xtra_Strings::get( 'submit' ) ) . '</span><i class="xtra-loading" aria-hidden="true"></i></a>';

				echo '<br /><br /><br /><span class="xtra-feedback-message" aria-hidden="true"></span>';

			echo '</form>';

			$this->render_after();

		}

		/**
		 * AJAX process feedback form message send to email.
		 * 
		 * @return string.
		 */
		public function feedback_submit() {

			check_ajax_referer( 'xtra-feedback', 'nonce' );

			if ( ! empty( $_POST[ 'message' ] ) ) {

				// Form.
				$from = get_option( 'admin_email' ); 
				$subject = 'XTRA Feedback';
				$sender = 'From: ' . get_bloginfo( 'name' ) . ' <' . $from . '>' . "\r\n";

				// Message.
				$message = wp_kses_post( wp_unslash( $_POST[ 'message' ] ) );
				$message .= '<br /><br />';
				$message .= get_home_url();
				$message .= '<br />';
				$message .= 'Theme: ' . Xtra_Strings::get( 'theme_name' ) . ' - v' . $this->theme->version;

				// Headers.
				$headers[] = 'MIME-Version: 1.0' . "\r\n";
				$headers[] = 'Content-type: text/html; charset=UTF-8' . "\r\n";
				$headers[] = "X-Mailer: PHP \r\n";
				$headers[] = $sender;

				$mail = '';

				if ( method_exists( 'Codevz_Plus', 'sendMail' ) ) {

					$mail = Codevz_Plus::sendMail( 'xtratheme.com@gmail.com', $subject, $message, $headers );

				}

				if ( $mail ) {

					wp_send_json(
						[
							'status' 	=> '200',
							'message' 	=> esc_html( Xtra_Strings::get( 'sent' ) )
						]
					);

				} else {

					wp_send_json(
						[
							'status' 	=> '202',
							'message' 	=> esc_html( Xtra_Strings::get( 'sent_error' ) )
						]
					);

				}

			}

			wp_send_json(
				[
					'status' 	=> '202',
					'message' 	=> esc_html( Xtra_Strings::get( 'no_msg' ) )
				]
			);

		}

		/**
		 * Uninstall demo tab content.
		 * 
		 * @return string.
		 */
		public function uninstall() {

			$this->render_before( 'uninstall' );

			echo '<div class="xtra-demos xtra-uninstall xtra-lazyload clearfix">';

			echo '<div class="xtra-dashboard-section-title">' . esc_html( Xtra_Strings::get( 'un_demos' ) ) . '</div>';

			echo '<p class="xtra-uninstall-p">' . esc_html( Xtra_Strings::get( 'un_desc' ) ) . '</p>';

			$has_demo = false;

			foreach ( $this->demos as $demo => $args ) {

				if ( get_option( 'xtra_uninstall_' . $demo ) ) {

					$has_demo = true;

					$rtl 	= is_rtl() && isset( $args[ 'rtl' ] ) ? 'rtl/' : '';
					$folder = apply_filters( 'xtra_rtl_checker', $rtl );

					echo '<div class="xtra-demo">';

						$api = apply_filters( 'xtra_config_api', self::$api );

						echo '<img data-src="' . esc_url( $api . 'demos/' . $folder . esc_attr( $demo ) . '.jpg' ) . '" />';

						echo '<div class="xtra-demo-title">' . esc_html( ucwords( str_replace( '-', ' ', isset( $args[ 'title' ] ) ? $args[ 'title' ] : $demo ) ) ) . '</div>';

						echo '<div class="xtra-demo-buttons">';

							echo '<a href="#" class="xtra-button-primary xtra-uninstall-button" data-demo="' . esc_html( $demo ) . '" data-title="' . esc_attr( Xtra_Strings::get( 'wait' ) ) . '"><span>' . esc_html( Xtra_Strings::get( 'uninstall' ) ) . '</span></a>';

						echo '</div>';

					echo '</div>';

				}

			}

			if ( ! $has_demo ) {

				$this->message( 'info', esc_html( Xtra_Strings::get( 'yet' ) ) );

			}

			echo '</div>';

			echo '<div class="xtra-modal" data-nonce="' . esc_attr( wp_create_nonce( 'xtra-wizard' ) ) . '">';

				echo '<div class="xtra-modal-inner">';

					echo '<div class="xtra-uninstall-msg">';

						echo '<div class="xtra-dashboard-section-title"><img src="' . esc_url( self::$url ) . 'assets/img/error.png" />' . esc_html( Xtra_Strings::get( 'are_you_sure' ) ) . '</div>';

						echo '<p>' . esc_html( Xtra_Strings::get( 'delete' ) ) . '</p>';

						echo '<img class="xtra-importer-spinner" src="' . esc_url( self::$url ) . 'assets/img/importing.png" />';

						echo '<a href="#" class="xtra-button-secondary">' . esc_html( Xtra_Strings::get( 'no' ) ) . '</a>';
						echo '<a href="#" class="xtra-button-primary" data-title="' . esc_attr( Xtra_Strings::get( 'uninstalling' ) ) . '">' . esc_html( Xtra_Strings::get( 'yes' ) ) . '</a>';

					echo '</div>';

					// Done message.
					echo '<div class="xtra-uninstalled hidden">';
						echo '<img src="' . esc_url( self::$url ) . 'assets/img/tick.png" />';
						echo '<h2>' . esc_html( Xtra_Strings::get( 'uninstalled' ) ) . '</h2>';
						echo '<a href="#" class="xtra-button-primary xtra-reload">' . esc_html( Xtra_Strings::get( 'reload' ) ) . '</a>';
						//echo '<a href="#" class="xtra-button-secondary">' . Xtra_Strings::get( 'close' ) . '</a>';
					echo '</div>';

				echo '</div>';

			echo '</div>';

			$this->render_after();

		}

		/**
		 * Deregister license and delete activation option.
		 * 
		 * @return -
		 */
		public function deregister( $code, $envato ) {

			if ( ! $envato ) {
				$verify = wp_remote_get( 'https://xtratheme.com?type=deregister&domain=' . $this->get_host_name() . '&code=' . $code );
			}

			delete_option( $this->option );

			return true;

		}

		/**
		 * Register license and add activation option to database.
		 * 
		 * @return -
		 */
		public function register( $code, $envato ) {

			if ( $envato ) {

				$item_id 		= apply_filters( 'xtra_config_item_id', '20715590' );
				$personalToken 	= apply_filters( 'xtra_config_token_key', 'ZMdAZMzRH8IUvopEsOv5jb9hgVfczMQf' );
				$userAgent 		= "Purchase code verification on " . $this->get_host_name();

				// Surrounding whitespace can cause a 404 error, so trim it first
				$code = trim( $code );

				// Make sure the code looks valid before sending it to Envato
				if ( ! preg_match( "/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $code ) ) {

					return Xtra_Strings::get( 'envato_error' );

				}

				// Build the request
				$response = wp_remote_get( "https://api.envato.com/v3/market/author/sale?code={$code}", [
					'headers' => [
						'Authorization' => "Bearer {$personalToken}",
						'User-Agent' 	=> "{$userAgent}",
					],
				]);

				// Handle connection errors (such as an API outage)
				// You should show users an appropriate message asking to try again later
				if ( is_wp_error( $response ) ) { 
				    return esc_html( Xtra_Strings::get( 'envato_api' ) ) . ' ' . $response->get_error_message();
				}

				// If we reach this point in the code, we have a proper response!
				// Let's get the response code to check if the purchase code was found
				$responseCode = wp_remote_retrieve_response_code( $response );

				// HTTP 404 indicates that the purchase code doesn't exist
				if ( $responseCode === 404 ) {

				    return esc_html( Xtra_Strings::get( 'envato_exist' ) );

				}

				// Anything other than HTTP 200 indicates a request or API error
				// In this case, you should again ask the user to try again later
				if ( $responseCode !== 200 ) {
					return esc_html( Xtra_Strings::get( 'envato_http' ) ) . ' ' . $responseCode;
				}

				$response = wp_remote_retrieve_body( $response );

				// Parse the response into an object with warnings supressed
				$body = $response ? json_decode( $response , true ) : [];

				if ( ! isset( $body[ 'sold_at' ] ) ) {
					return esc_html( Xtra_Strings::get( 'envato_10sec' ) );
				}

				// Check for errors while decoding the response (PHP 5.3+)
				if ( $body === false && json_last_error() !== JSON_ERROR_NONE ) {
					return esc_html( Xtra_Strings::get( 'envato_parsing' ) );
				}

				// If item id is wrong
				if ( isset( $body['item']['id'] ) && $body['item']['id'] != $item_id ) {
					return esc_html( Xtra_Strings::get( 'envato_another' ) );
				}

				// Compatibility with envato plugin.
				update_option( 'envato_purchase_code_' . $body['item']['id'], $code );

				// Save data for codevz.
				update_option( $this->option, [
					'type'			=> 'success',
					'themeforest'	=> true,
					'item_id' 		=> $body['item']['id'],
					'purchase_code' => $code,
					'purchase_date' => $body[ 'sold_at' ],
					'support_until' => $body[ 'supported_until' ]
				] );

				return true;

			} else {

				// XtraTheme verify purchase
				$verify = wp_remote_get( 'https://xtratheme.com?type=register&domain=' . $this->get_host_name() . '&code=' . $code );

				if ( is_wp_error( $verify ) ) {

					return $verify->get_error_message();

				} else if ( ! isset( $verify['body'] ) ) {

					return esc_html( Xtra_Strings::get( 'envato_10sec' ) );

				} else {

					$verify = json_decode( $verify['body'], true );

					if ( isset( $verify['type'] ) && $verify['type'] === 'error' ) {
						return $verify['message'];
					}

					if ( ! isset( $verify['purchase_code'] ) ) {

						return esc_html( Xtra_Strings::get( 'envato_check' ) );

					}

				}

				// Registered successfully.
				update_option( $this->option, $verify );

				return true;

			}

		}

		/**
		 * Get current site host name.
		 * 
		 * @return string
		 */
		public function get_host_name( $url = '' ) {

			$pieces = parse_url( $url ? $url : home_url() );

			$domain = isset( $pieces['host'] ) ? $pieces['host'] : '';

			if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs ) ) {
				return $regs['domain'];
			}

			return $domain;

		}

		/**
		 * Plugin installation and importer AJAX function.
		 * @return string
		 */
		public function wizard() {

			check_ajax_referer( 'xtra-wizard', 'nonce' );

			if ( ! empty( $_POST ) ) {

				$_POST = wp_unslash( $_POST );

			}

			// Import posts meta.
			if ( ! empty( $_POST[ 'meta' ] ) ) {

				wp_send_json(
					Codevz_Demo_Importer::import_process(
						[ 'meta' => 1 ]
					)
				);

			}

			// Check name.
			if ( empty( $_POST[ 'name' ] ) ) {

				wp_send_json(
					[
						'status' 	=> '202',
						'message' 	=> esc_html( Xtra_Strings::get( 'ajax_error' ) )
					]
				);

			}

			// Fix redirects after plugin installation.
			if ( $_POST[ 'name' ] === 'redirect' ) {

				wp_send_json(
					[
						'status' 	=> '200',
						'message' 	=> esc_html( Xtra_Strings::get( 'redirected' ) )
					]
				);

			}

			// Vars.
			$data = [];
			$name = isset( $_POST[ 'name' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'name' ] ) ) : '';
			$type = isset( $_POST[ 'type' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'type' ] ) ) : '';
			$demo = isset( $_POST[ 'demo' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'demo' ] ) ) : '';

			// Install & activate plugin.
			if ( $type === 'plugin' ) {

				$data = $this->install_plugin( $name );

				if ( is_string( $data ) ) {

					$data = [

						'status' 	=> '202',
						'message' 	=> esc_html( Xtra_Strings::get( 'find_plugin', $name ) )

					];

				}

			// Download demo files.
			} else if ( $type === 'download' ) {

				// Check codevz plus.
				if ( ! class_exists( 'Codevz_Demo_Importer' ) ) {

					wp_send_json(
						[
							'status' 	=> '202',
							'message' 	=> esc_html( Xtra_Strings::get( 'cp_error' ) )
						]
					);

				}

				$folder = isset( $_POST[ 'folder' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'folder' ] ) ) : '';

				$data = Codevz_Demo_Importer::download( $demo, $folder );

			// Import demo data.
			} else if ( $type === 'import' ) {

				$data = Codevz_Demo_Importer::import_process(
					[
						'demo' 			=> $demo,
						'features' 		=> [ $name ],
						'posts' 		=> empty( $_POST[ 'posts' ] ) ? 1 : sanitize_text_field( wp_unslash( $_POST[ 'posts' ] ) )
					]
				);

			// Uninstall demo data.
			} else if ( $type === 'uninstall' ) {

				$data = $this->uninstall_demo( $demo );

			} else {

				$data = [
					'status' 	=> '202',
					'message' 	=> esc_html( Xtra_Strings::get( 'occured' ) )
				];

			}

			wp_send_json( $data );

		}

		/**
		 * Plugin installation and activation process.
		 * 
		 * @return array
		 */
		protected function install_plugin( $plugin = '' ) {

			// Plugin slug.
			$slug = esc_html( urldecode( $plugin ) );

			// Check plugin inside plugins.
			if ( ! isset( $this->plugins[ $slug ] ) ) {

				return [

					'status' 	=> '202',
					'message' 	=> esc_html( Xtra_Strings::get( 'listed', $slug ) )

				];

			}

			// Pass necessary information via URL if WP_Filesystem is needed.
			$url = wp_nonce_url(
				add_query_arg(
					array(
						'plugin' 	=> urlencode( $slug )
					),
					admin_url( 'admin-ajax.php' )
				),
				'xtra-wizard',
				'nonce'
			);

			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), '', false, false, [] ) ) ) {

				return [

					'status' 	=> '202',
					'message' 	=> esc_html( Xtra_Strings::get( 'ftp' ) )

				];

			}

			// Prep variables for Plugin_Installer_Skin class.
			if ( isset( $this->plugins[ $slug ][ 'source' ] ) ) {
				$api = null;
				$source = $this->plugins[ $slug ][ 'source' ];
			} else {
				$api = $this->plugins_api( $slug );
				if ( is_string( $api ) ) {
					return [

						'status' 	=> '202',
						'message' 	=> wp_kses_post( Xtra_Strings::get( 'wp_api' ) . ' ' . $api )

					];
				}
				$source = isset( $api->download_link ) ? $api->download_link : '';
			}

			// Check ZIP file.
			if ( ! $source ) {

				return [

					'status' 	=> '202',
					'message' 	=> esc_html( Xtra_Strings::get( 'manually', $slug ) )

				];

			}

			$url = add_query_arg(
				array(
					'plugin' => urlencode( $slug )
				),
				'update.php'
			);

			if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			$skin_args = array(
				'type'   => 'web',
				'title'  => $this->plugins[ $slug ]['name'],
				'url'    => esc_url_raw( $url ),
				'nonce'  => 'xtra-wizard',
				'plugin' => '',
				'api'    => $source ? null : $api,
				'extra'  => [ 'slug' => $slug ]
			);

			$skin = new Plugin_Installer_Skin( $skin_args );

			// Create a new instance of Plugin_Upgrader.
			$upgrader = new Plugin_Upgrader( $skin );

			// File path.
			$file = $this->plugin_file( $slug, true );

			// FIX: Check if file is not exist but folder exist. 
			$folder = dirname( $file );

			if ( ! file_exists( $file ) && is_dir( $folder ) ) {

				rename( $folder, $folder . '_backup_' . rand( 111, 999 ) );

			}

			// Install plugin.
			if ( ! file_exists( $file ) ) {

				$upgrader->install( $source );

			}

			// Install plugin manually.
			if ( ! file_exists( $file ) ) {

				$plugin_dir = dirname( $file );

				// Final check if plugin installed?
				if ( ! file_exists( $file ) || is_dir( $plugin_dir ) ) {

					return [

						'status' 	=> '202',
						'message' 	=> esc_html( Xtra_Strings::get( '300s', $slug ) )

					];

				}

			}

			if ( ! $this->plugin_is_active( $slug ) ) {

				// Activate plugin.
				$activate = activate_plugin( $this->plugin_file( $slug ) );

				// Check activation error.
				if ( is_wp_error( $activate ) ) {

					return [

						'status' 	=> '202',
						'message' 	=> esc_html( Xtra_Strings::get( 'plugin_error' ) ) . $activate->get_error_message()

					];

				}

			}

			return [

				'status' 	=> '200',
				'message' 	=> esc_html( Xtra_Strings::get( 'plugin_installed', $slug ) )

			];

		}

		/**
		 * Try to grab information from WordPress API.
		 *
		 * @param string $slug Plugin slug.
		 * @return object Plugins_api response object on success, WP_Error on failure.
		 */
		protected function plugins_api( $slug ) {

			static $api = [];

			if ( ! isset( $api[ $slug ] ) ) {

				if ( ! function_exists( 'plugins_api' ) ) {

					require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

				}

				$response = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );

				$api[ $slug ] = false;

				if ( is_wp_error( $response ) ) {

					return esc_html( Xtra_Strings::get( 'plugin_api' ) ) . ' ' . $response->get_error_message();

				} else {

					$api[ $slug ] = $response;

				}

			}

			return $api[ $slug ];

		}

		/**
		 * Check if plugin is active with file_exists function.
		 *
		 * @param string $slug Plugin slug.
		 * @return bool
		 */
		private function plugin_file( $slug, $full_path = false ) {

			if ( $slug === 'contact-form-7' ) {

				$file = 'wp-contact-form-7';

			} else {

				$file = $slug;

			}

			return $full_path ? WP_PLUGIN_DIR . '/' . $slug . '/' . $file . '.php' : $slug . '/' . $file . '.php';

		}

		/**
		 * Check if plugin is active with file_exists function.
		 *
		 * @param string $slug Plugin slug.
		 * @return bool
		 */
		private function plugin_is_active( $slug ) {

			if ( isset( $this->plugins[ $slug ][ 'class_exists' ] ) && class_exists( $this->plugins[ $slug ][ 'class_exists' ] ) ) {

				return true;

			} else if ( isset( $this->plugins[ $slug ][ 'function_exists' ] ) && function_exists( $this->plugins[ $slug ][ 'function_exists' ] ) ) {

				return true;

			}

			return false;

		}

		/**
		 * Retrieves the attachment ID from the file URL
		 * 
		 * @return array
		 */
		private function get_attachment_id_by_url( $url ) {

			global $wpdb;

			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid = %s;", esc_url( $url ) ) ); 

			return isset( $attachment[ 0 ] ) ? $attachment[ 0 ] : false;

		}

		/**
		 * Uninstall imported demo data.
		 * 
		 * @return array
		 */
		private function uninstall_demo( $demo ) {

			$data = get_option( 'xtra_uninstall_' . $demo );

			if ( is_array( $data ) ) {

				foreach( $data as $type => $items ) {

					switch( $type ) {

						case 'options':

							delete_option( 'codevz_theme_options' );

							break;

						case 'posts':

							// Delete posts.
							foreach( $items as $item ) {

								if ( ! empty( $item[ 'id' ] ) && sanitize_title_with_dashes( get_the_title( $item[ 'id' ] ) ) === sanitize_title_with_dashes( $item[ 'title' ] ) ) {

									wp_delete_post( $item[ 'id' ], true );

								}

							}

							break;

						case 'attachments':

							foreach( $items as $item ) {

								if ( ! empty( $item[ 'id' ] ) && sanitize_title_with_dashes( get_the_title( $item[ 'id' ] ) ) === sanitize_title_with_dashes( $item[ 'title' ] ) ) {

									wp_delete_attachment( $item[ 'id' ], true );

								}

							}

							break;

						case 'terms':

							foreach( $items as $item ) {

								if ( ! empty( $item[ 'id' ] ) ) {

									wp_delete_term( $item[ 'id' ], $item[ 'taxonomy' ] );

								}

							}

							break;

						case 'sliders':

							if ( class_exists( 'RevSliderSlider' ) ) {

								foreach( $items as $item ) {

									$slider	= new RevSliderSlider();
									$slider->init_by_id( $item[ 0 ] );
									$slider->delete_slider();

								}

							}

							break;

					}

				}

				delete_option( 'xtra_uninstall_' . $demo );

				// Reset colors.
				delete_option( 'codevz_primary_color' );
				delete_option( 'codevz_secondary_color' );

				// Reset widgets.
				update_option( 'sidebars_widgets', [] );

				// Success.
				wp_send_json(
					[
						'status' 	=> '200',
						'message' 	=> esc_html( Xtra_Strings::get( 'demo_uninstalled', $demo ) )
					]
				);

			} else {

				wp_send_json(
					[
						'status' 	=> '202',
						'message' 	=> esc_html( Xtra_Strings::get( 'uninstall_error', $demo ) )
					]
				);

			}

		}

	}

	// Run dashboard.
	Xtra_Dashboard::instance();

}
