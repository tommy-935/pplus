<?php if (!defined('ABSPATH')) {
	exit;
} // Cannot access pages directly.

/**
 * Theme core class and functions
 * If you want to override functions, Please read theme documentation
 * 
 * @link https://xtratheme.com/
 */
if (!defined('_OPTIONS_PRE')) {

	define('_OPTIONS_PRE', '_sms_options');
}


if (!function_exists('_sms_options')) {
	function _sms($option = '', $default = null)
	{
		$options_meta = _OPTIONS_PRE;
		$options      = get_option($options_meta);
		return (isset($options[$option])) ? $options[$option] : $default;
	}
}


if (true || !class_exists('CSF')) {
	$theme_inc_file_path = ABSPATH . 'wp-content/themes/bizpro/inc';
	$options             = array(
		'/codestar-framework-master/codestar-framework.php', //core
		'/codestar-framework-master/samples/admin-options.php', //admin
	);
	foreach ($options as $option) {
		require_once $theme_inc_file_path . $option;
	}
}




if (!class_exists('Xtra_Theme')) {

	class Xtra_Theme
	{

		// Server API address.
		public static $api = 'https://xtratheme.com/api/';

		// Check core plugin.
		public static $plugin;

		// Cache post query.
		public static $post;

		// Get home URL.
		public static $home_url;

		// Header element ID.
		private static $element = 0;

		// Check RTL mode.
		public static $is_rtl = false;

		// Check preview.
		public static $preview = false;

		// Theme folder URL.
		public static $url = false;

		// Check theme is premium ver.
		public static $premium = false;

		// Instance of this class.
		private static $instance = null;

		// Core functionality.
		public function __construct()
		{

			self::$post 	= &$GLOBALS['post'];
			self::$plugin 	= class_exists('Codevz_Plus');
			self::$preview 	= is_customize_preview();
			self::$home_url = esc_url(home_url('/'));
			self::$url 		= trailingslashit(get_template_directory_uri());

			// After loaded.
			add_action('wp', [$this, 'wp']);

			// Translations.
			get_template_part('classes/class-strings');

			// Custom theme configuration.
			get_template_part('classes/class-config');

			// Premium version.
			get_template_part('classes/class-premium');

			self::$premium = class_exists('Xtra_Premium');

			// Default theme options.
			get_template_part('classes/class-settings');

			// Dashboard and importer.
			get_template_part('classes/class-dashboard');

			// Actions.
			add_action('after_setup_theme', [$this, 'theme_setup']);
			add_action('widgets_init', [$this, 'register_sidebars']);
			add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
			add_action('enqueue_block_assets', [$this, 'enqueue_block_assets']);
			add_action('wp_head', [$this, 'load_dynamic_css'], 99);
			add_action('nav_menu_css_class', [$this, 'menu_current_class'], 10, 2);
			add_action('wp_ajax_codevz_selective_refresh', [$this, 'row_inner']);
			add_action('wp_head', [$this, 'wp_head']);

			// Filters.
			add_filter('excerpt_more', [$this, 'excerpt_more']);
			add_filter('excerpt_length', [$this, 'excerpt_length'], 99);
			add_filter('get_the_excerpt', [$this, 'get_the_excerpt'], 21);
			add_filter('the_content_more_link', [$this, 'the_content_more_link']);
			add_filter('wp_list_categories', [$this, 'wp_list_categories']);
			add_filter('get_archives_link',  [$this, 'get_archives_link']);

			// Fix accessibility.
			add_filter('get_avatar', [$this, 'get_avatar']);
		}

		/**
		 * Instance
		 */
		public static function instance()
		{

			if (self::$instance === null) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function wp()
		{

			self::$is_rtl = (self::option('rtl') || is_rtl() || isset($_GET['rtl']));
		}

		/**
		 * Get page settings
		 * 
		 * @var $id = post id
		 * @var $key = array key
		 * 
		 * @return array|string|null
		 */
		public static function meta($id = null, $key = null, $default = '')
		{

			if (!$id && isset(self::$post->ID)) {
				$id = self::$post->ID;
			}

			$meta = get_post_meta($id, 'codevz_page_meta', true);

			$meta = apply_filters('xtra/page/meta', $meta, $id);

			if ($key) {
				return empty($meta[$key]) ? $default : $meta[$key];
			} else {
				return ($id && $meta) ? $meta : $default;
			}
		}

		/**
		 * Get theme options
		 * 
		 * @var 	$key = option name
		 * @var 	$default = default value
		 * 
		 * @return 	array|string|null
		 */
		public static function option($key = '', $default = '')
		{

			$options = apply_filters('xtra/options', get_option('codevz_theme_options'));

			return empty($key) ? $options : apply_filters('xtra/option/' . $key, (empty($options[$key]) ? $default : $options[$key]));
		}

		/**
		 * After setup theme
		 */
		public static function theme_setup()
		{

			// Activation check.
			$activation = get_option('codevz_theme_activation');

			if (!isset($activation['support_until'])) {
				delete_option('codevz_theme_activation');
			}

			// Menu location.
			register_nav_menus(['primary' => esc_html(Xtra_Strings::get('primary'))]);

			// Theme Supports.
			add_theme_support('html5');
			add_theme_support('title-tag');
			add_theme_support('automatic-feed-links');

			// Thumbnails and featured image.
			add_theme_support('post-thumbnails');

			// Post formats.
			add_theme_support('post-formats', ['gallery', 'video', 'audio', 'quote']);

			// Add theme support for selective refresh for widgets.
			add_theme_support('customize-selective-refresh-widgets');

			// Add support for Block Styles.
			add_theme_support('wp-block-styles');

			// Add support for full and wide align images.
			add_theme_support('align-wide');

			// Add support for editor styles.
			add_theme_support('editor-styles');
			add_editor_style(self::$url . 'assets/css/editor-style.css');

			// Responsive embedded content.
			add_theme_support('responsive-embeds');
			add_theme_support('jetpack-responsive-videos');

			// Add support for plugins.
			add_theme_support('woocommerce');
			add_theme_support('wc-product-gallery-zoom');
			add_theme_support('wc-product-gallery-lightbox');
			add_theme_support('wc-product-gallery-slider');
			add_theme_support('bbpress');

			// WP theme check.
			if (!self::$plugin) {

				add_theme_support('custom-logo');
				add_theme_support('custom-header');
				add_theme_support('custom-background');

				if (function_exists('register_block_style') && function_exists('register_block_pattern')) {

					register_block_style(
						'core/quote',
						[
							'name' 			=> 'xtra-quote',
							'label' 		=> esc_attr(Xtra_Strings::get('theme_name')),
							'is_default'	 => true,
							'inline_style' 	=> '.wp-block-quote.is-style-xtra-quote {color:blue}',
						]
					);

					register_block_pattern(
						'wpdocs-xtra/xtra-pattern',
						array(
							'title'       => '-',
							'description' => '-',
							'content'     => "<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons aligncenter\"><!-- wp:button {\"backgroundColor\":\"very-dark-gray\",\"borderRadius\":0} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-background has-very-dark-gray-background-color no-border-radius\">" . esc_html(Xtra_Strings::get('theme_name')) . "</a></div>\n<!-- /wp:button -->\n\n<!-- wp:button {\"textColor\":\"very-dark-gray\",\"borderRadius\":0,\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link has-text-color has-very-dark-gray-color no-border-radius\">" . esc_html(Xtra_Strings::get('theme_name')) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
						)
					);
				}
			}

			// Remove support.
			remove_theme_support('widgets-block-editor');

			// Disable Woocommerce features.
			$disable_woo = (array) self::option('woo_gallery_features');
			foreach ($disable_woo as $f) {
				remove_theme_support('wc-product-gallery-' . $f);
			}

			// Images.
			add_image_size('codevz_360_320', 360, 320, true); 	// Medium
			add_image_size('codevz_600_600', 600, 600, true); 	// Square
			add_image_size('codevz_1200_200', 1200, 200, true); 	// CPT Full 1
			add_image_size('codevz_1200_500', 1200, 500, true); 	// CPT Full 2
			add_image_size('codevz_600_1000', 600, 1000, true); 	// Vertical
			add_image_size('codevz_600_9999', 600, 9999); 		// Masonry

			// Content width.
			if (!isset($content_width)) {
				$content_width = apply_filters('codevz_content_width', (int) self::option('site_width', 1280));
			}
		}

		/**
		 * Front-end assets
		 * @return string
		 */
		public static function wp_enqueue_scripts()
		{

			if (!isset($_POST['vc_inline'])) {

				// Path.
				$uri = self::$url;

				// Get theme version.
				$theme = wp_get_theme();
				$ver = empty($theme->parent()) ? $theme->get('Version') : $theme->parent()->Version;

				$name = self::option('white_label_theme_name', 'xtra');

				// Core styles.
				//wp_enqueue_style( 'theme', get_stylesheet_uri(), [], $ver );
				wp_enqueue_style($name, $uri . 'assets/css/core.css', [], $ver);

				if (!self::option('disable_responsive')) {

					wp_enqueue_style($name . '-laptop', self::$url . 'assets/css/core-laptop.css', [$name], $ver, 'screen and (max-width: 1024px)');
					wp_enqueue_style($name . '-tablet', self::$url . 'assets/css/core-tablet.css', [$name], $ver, 'screen and (max-width: ' . self::option('tablet_breakpoint', '768px') . ')');
					wp_enqueue_style($name . '-mobile', self::$url . 'assets/css/core-mobile.css', [$name], $ver, 'screen and (max-width: ' . self::option('mobile_breakpoint', '480px') . ')');
				}

				if (!self::$plugin) {

					//wp_enqueue_style( 'xtra-font-awesome', $uri .'assets/font-awesome/font-awesome.min.css', array(), '5.11.2', 'all' );
					wp_enqueue_style('xtra-font-awesome-shims', $uri . 'assets/font-awesome/font-awesome/css/v4-shims.min.css', array(), '5.11.2', 'all');
					wp_enqueue_style('xtra-font-awesome-all', $uri . 'assets/font-awesome/font-awesome/css/all.min.css', array(), '5.11.2', 'all');
				}

				// Error page.
				if (is_404()) {
					wp_enqueue_style('xtra-404', 		$uri . 'assets/css/404.css', [$name], $ver);
				}

				// RTL mode.
				if (self::$is_rtl) {
					wp_enqueue_style('xtra-rtl', 		$uri . 'assets/css/core.rtl.css', [$name], $ver);
				}

				// Fixed side.
				if (self::option('fixed_side')) {
					wp_enqueue_style('xtra-fixed-side', 		$uri . 'assets/css/fixed_side.css', [$name], $ver);
					if (self::$is_rtl) {
						wp_enqueue_style('xtra-fixed-side-rtl', $uri . 'assets/css/fixed_side.rtl.css', [$name], $ver);
					}
				}

				// Single CSS.
				if (is_single()) {

					wp_enqueue_style('xtra-single', 	$uri . 'assets/css/single.css', [$name], $ver);

					if (self::$is_rtl) {
						wp_enqueue_style('xtra-single-rtl', $uri . 'assets/css/single.rtl.css', [$name], $ver);
					}
				}

				// Custom.js
				wp_enqueue_script($name, $uri . 'assets/js/custom.js', ['jquery'], $ver, true);

				// Comments.
				if (is_singular() && comments_open() && get_option('thread_comments')) {

					wp_enqueue_script('comment-reply');

					wp_enqueue_style('xtra-comments', 	$uri . 'assets/css/comments.css', [$name], $ver);
					wp_enqueue_style('xtra-comments-mobile', self::$url . 'assets/css/comments-mobile.css', [$name], $ver, 'screen and (max-width: ' . self::option('mobile_breakpoint', '480px') . ')');

					if (self::$is_rtl) {
						wp_enqueue_style('xtra-comments-rtl', $uri . 'assets/css/comments.rtl.css', [$name], $ver);
					}
				}

				// Header sticky.
				if (self::option('sticky_header', self::option('mobile_sticky'))) {
					wp_enqueue_script('xtra-sticky', 	$uri . 'assets/js/sticky.js', [$name], $ver, true);
				}

				// Page loading.
				if (self::option('pageloader')) {
					wp_enqueue_style('xtra-loading', 	$uri . 'assets/css/loading.css', [$name], $ver);
					wp_enqueue_script('xtra-loading', 	$uri . 'assets/js/loading.js', [$name], $ver, true);
				}

				// WPML.
				if (function_exists('icl_object_id')) {
					wp_enqueue_script('xtra-wpml', 	$uri . 'assets/js/wpml.js', [$name], $ver, true);
				}

				// Register JS.
				wp_register_script('xtra-search', 		$uri . 'assets/js/search.js');
				wp_register_script('xtra-extra-panel', $uri . 'assets/js/extra_panel.js');
				wp_register_script('xtra-icon-text', 	$uri . 'assets/js/icon_text.js');

				// Only on preview.
				if (self::$preview) {
					wp_enqueue_script('xtra-search');
				}

				// Fonts
				foreach ((array) self::option('fonts_out') as $font) {
					self::enqueue_font($font);
				}
			}
		}

		/**
		 * Load block styles and scripts.
		 * @return string
		 */
		public function enqueue_block_assets()
		{

			wp_enqueue_style('xtra-blocks',  self::$url . '/assets/css/blocks.css');
		}

		/**
		 * Load dynamic style as a file or inline
		 * @return string
		 */
		public static function load_dynamic_css()
		{

			if (!isset($_POST['vc_inline'])) {

				// Custom styles
				$name = self::option('white_label_theme_name', 'xtra');
				$handle = wp_style_is('codevz-plugin') ? 'codevz-plugin' : $name;
				$extra_css = '';

				// Dark
				if (self::option('dark')) {
					$extra_css .= "/* Dark */" . 'body{background-color:#171717;color:#fff}.layout_1,.layout_2{background:#191919}a,.woocommerce-error, .woocommerce-info, .woocommerce-message{color:#fff}.sf-menu li li a,.sf-menu .cz > h6{color: #000}.cz_quote_arrow blockquote{background:#272727}.search_style_icon_dropdown .outer_search, .cz_cart_items {background: #000;color: #c0c0c0 !important}.woocommerce div.product .woocommerce-tabs ul.tabs li.active a {color: #111}#bbpress-forums li{background:none!important}#bbpress-forums li.bbp-header,#bbpress-forums li.bbp-header,#bbpress-forums li.bbp-footer{background:#141414!important;color:#FFF;padding:10px 20px!important}.bbp-header a{color:#fff}.subscription-toggle,.favorite-toggle{padding: 1px 20px !important;}span#subscription-toggle{color: #000}#bbpress-forums #bbp-single-user-details #bbp-user-navigation li.current a{background:#1D1E20!important;color:#FFF;opacity:1}#bbpress-forums li.bbp-body ul.forum,#bbpress-forums li.bbp-body ul.topic{padding:10px 20px!important}.bbp-search-form{margin:0 0 12px!important}.bbp-form .submit{margin:0 auto 20px}div.bbp-breadcrumb,div.bbp-topic-tags{line-height:36px}.bbp-breadcrumb-sep{padding:0 6px}#bbpress-forums li.bbp-header ul{font-size:14px}.bbp-forum-title,#bbpress-forums .bbp-topic-title .bbp-topic-permalink{font-size:16px;font-weight:700}#bbpress-forums .bbp-topic-started-by{display:inline-block}#bbpress-forums p.bbp-topic-meta a{margin:0 4px 0 0;display:inline-block}#bbpress-forums p.bbp-topic-meta img.avatar,#bbpress-forums ul.bbp-reply-revision-log img.avatar,#bbpress-forums ul.bbp-topic-revision-log img.avatar,#bbpress-forums div.bbp-template-notice img.avatar,#bbpress-forums .widget_display_topics img.avatar,#bbpress-forums .widget_display_replies img.avatar{margin-bottom:-2px;border:0}span.bbp-admin-links{color:#4F4F4F}span.bbp-admin-links a{color:#7C7C7C}.bbp-topic-revision-log-item *{display:inline-block}#bbpress-forums .bbp-topic-content ul.bbp-topic-revision-log,#bbpress-forums .bbp-reply-content ul.bbp-topic-revision-log,#bbpress-forums .bbp-reply-content ul.bbp-reply-revision-log{border-top:1px dotted #474747;padding:10px 0 0;color:#888282}.bbp-topics,.bbp-replies,.topic{position:relative}#subscription-toggle,#favorite-toggle{float:right;line-height:34px;color:#DFDFDF;display:block;border:1px solid #DFDFDF;padding:0;margin:0;font-size:12px;border:0!important}.bbp-user-subscriptions #subscription-toggle,.bbp-user-favorites #favorite-toggle{position:absolute;top:0;right:0;line-height:20px}.bbp-reply-author br{display:none}#bbpress-forums li{text-align:left}li.bbp-forum-freshness,li.bbp-topic-freshness{width:23%}.bbp-topics-front ul.super-sticky,.bbp-topics ul.super-sticky,.bbp-topics ul.sticky,.bbp-forum-content ul.sticky{background-color:#2C2C2C!important;border-radius:0!important;font-size:1.1em}#bbpress-forums div.odd,#bbpress-forums ul.odd{background-color:#0D0D0D!important}div.bbp-template-notice a{display:inline-block}div.bbp-template-notice a:first-child,div.bbp-template-notice a:last-child{display:inline-block}#bbp_topic_title,#bbp_topic_tags{width:400px}#bbp_stick_topic_select,#bbp_topic_status_select,#display_name{width:200px}#bbpress-forums #bbp-your-profile fieldset span.description{color:#FFF;border:#353535 1px solid;background-color:#222!important;margin:16px 0}#bbpress-forums fieldset.bbp-form{margin-bottom:40px}.bbp-form .quicktags-toolbar{border:1px solid #EBEBEB}.bbp-form .bbp-the-content,#bbpress-forums #description{border-width:1px!important;height:200px!important}#bbpress-forums #bbp-single-user-details{width:100%;float:none;border-bottom:1px solid #080808;box-shadow:0 1px 0 rgba(34,34,34,0.8);margin:0 0 20px;padding:0 0 20px}#bbpress-forums #bbp-user-wrapper h2.entry-title{margin:-2px 0 20px;display:inline-block;border-bottom:1px solid #FF0078}#bbpress-forums #bbp-single-user-details #bbp-user-navigation a{padding:2px 8px}#bbpress-forums #bbp-single-user-details #bbp-user-navigation{display:inline-block}#bbpress-forums #bbp-user-body,.bbp-user-section p{margin:0}.bbp-user-section{margin:0 0 30px}#bbpress-forums #bbp-single-user-details #bbp-user-avatar{margin:0 20px 0 0;width:auto;display:inline-block}#bbpress-forums div.bbp-the-content-wrapper input{width:auto!important}input#bbp_topic_subscription{width:auto;display:inline-block;vertical-align:-webkit-baseline-middle}.widget_display_replies a,.widget_display_topics a{display:inline-block}.widget_display_replies li,.widget_display_forums li,.widget_display_views li,.widget_display_topics li{display:block;border-bottom:1px solid #282828;line-height:32px;position:relative}.widget_display_replies li div,.widget_display_topics li div{font-size:11px}.widget_display_stats dt{display:block;border-bottom:1px solid #282828;line-height:32px;position:relative}.widget_display_stats dd{float:right;margin:-40px 0 0;color:#5F5F5F}#bbpress-forums div.bbp-topic-content code,#bbpress-forums div.bbp-reply-content code,#bbpress-forums div.bbp-topic-content pre,#bbpress-forums div.bbp-reply-content pre{background-color:#FFF;padding:12px 20px;max-width:96%;margin-top:0}#bbpress-forums div.bbp-forum-author img.avatar,#bbpress-forums div.bbp-topic-author img.avatar,#bbpress-forums div.bbp-reply-author img.avatar{border-radius:100%}#bbpress-forums li.bbp-header,#bbpress-forums li.bbp-footer,#bbpress-forums li.bbp-body ul.forum,#bbpress-forums li.bbp-body ul.topic,div.bbp-forum-header,div.bbp-topic-header,div.bbp-reply-header{border-top:1px solid #252525!important}#bbpress-forums ul.bbp-lead-topic,#bbpress-forums ul.bbp-topics,#bbpress-forums ul.bbp-forums,#bbpress-forums ul.bbp-replies,#bbpress-forums ul.bbp-search-results,#bbpress-forums fieldset.bbp-form,#subscription-toggle,#favorite-toggle{border:1px solid #252525!important}#bbpress-forums div.bbp-forum-header,#bbpress-forums div.bbp-topic-header,#bbpress-forums div.bbp-reply-header{background-color:#1A1A1A!important}#bbpress-forums div.even,#bbpress-forums ul.even{background-color:#161616!important}.bbp-view-title{display:block}div.fixed_contact,i.backtotop,i.fixed_contact,.ajax_search_results{background:#151515}.nice-select{background-color:#fff;color:#000}.nice-select .list{background:#fff}.woocommerce div.product .woocommerce-tabs ul.tabs li.active a,.woocommerce div.product .woocommerce-tabs ul.tabs li a{color: inherit}.woocommerce #reviews #comments ol.commentlist li .comment-text{border-color:rgba(167, 167, 167, 0.2) !important}.woocommerce div.product .woocommerce-tabs ul.tabs li.active{background:rgba(167, 167, 167, 0.2)}.woocommerce div.product .woocommerce-tabs ul.tabs li::before,.woocommerce div.product .woocommerce-tabs ul.tabs li::after{display:none!important}#comments .commentlist li .avatar{box-shadow: 1px 10px 10px rgba(167, 167, 167, 0.1) !important}.cz_line{background:#fff}.xtra-post-title span{color:rgba(255, 255, 255, 0.6)}.woocommerce div.product div.images .woocommerce-product-gallery__wrapper .zoomImg{background-color:#0b0b0b}.cz_popup_in{background:#171717;color:#fff}';
				}

				// Category page custom background
				if (is_category() || is_tag() || is_tax()) {

					global $wp_query;

					if (!empty($wp_query->queried_object->term_id)) {

						$tax_meta = get_term_meta($wp_query->queried_object->term_id, 'codevz_cat_meta', true);

						if (!empty($tax_meta['_css_page_title'])) {

							$extra_css .= '.page_title{' . str_replace(';', ' !important;', $tax_meta['_css_page_title']) . '}';
						}
					}
				}

				// Free version.
				if (!self::$plugin) {

					if (get_header_textcolor()) {

						$extra_css .= 'header, header a {color: #' . get_header_textcolor() . '}';
					}

					$image = get_header_image();

					if ($image) {

						$extra_css .= 'header {background-image: url( ' . esc_url($image) . ' );background-size: cover;background-position: center center;}';
					}
				}

				// Theme styles
				if (self::$preview) {

					//wp_add_inline_style( $handle, $extra_css );

					echo '<style id="xtra-inline-css" data-noptimize>' . do_shortcode($extra_css) . '</style>';
				}

				if (!self::$plugin) {
					$extra_css .= '.xtra-section-focus{display:none}';
				}

				if (!self::$preview || !self::$plugin) {

					$ts = self::option('css_out');

					// Fix.
					if (self::$plugin && (!$ts || get_option('xtra_generate_css_out'))) {

						$options = self::option();

						$options['css_out'] = Codevz_Options::css_out();

						update_option('codevz_theme_options', $options);

						update_option('xtra_generate_css_out', false);
					}

					// Admin bar. 
					$extra_css .= '.admin-bar .cz_fixed_top_border{top:32px}.admin-bar i.offcanvas-close {top: 32px}.admin-bar .offcanvas_area, .admin-bar .hidden_top_bar{margin-top: 32px}.admin-bar .header_5,.admin-bar .onSticky{top: 32px}@media screen and (max-width:' . self::option('tablet_breakpoint', '768px') . ') {.admin-bar .header_5,.admin-bar .onSticky,.admin-bar .cz_fixed_top_border,.admin-bar i.offcanvas-close {top: 46px}.admin-bar .onSticky {top: 0}.admin-bar .offcanvas_area,.admin-bar .offcanvas_area,.admin-bar .hidden_top_bar{margin-top:46px;height:calc(100% - 46px);}}';

					// Add styles
					//wp_add_inline_style( $handle, $extra_css . $ts );

					echo '<style id="xtra-inline-css" data-noptimize>' . do_shortcode($extra_css . $ts) . '</style>';
				}
			}
		}

		/**
		 * Register theme sidebars
		 * @return object
		 */
		public static function register_sidebars()
		{

			if (self::$plugin) {
				$sides = ['primary', 'secondary', 'footer-1', 'footer-2', 'footer-3', 'footer-4', 'footer-5', 'footer-6', 'offcanvas_area'];
			} else {
				$sides = ['primary', 'footer-1', 'footer-2', 'footer-3', 'footer-4'];
			}

			foreach ((array) self::option('sidebars') as $i) {
				if (!empty($i['id'])) {
					$id = strtolower($i['id']);
					$sides[] = sanitize_title_with_dashes($id);
				}
			}

			if (self::$plugin) {

				// Woocommerce
				if (function_exists('is_woocommerce')) {
					$sides[] = 'product-primary';
					$sides[] = 'product-secondary';
				}

				if (function_exists('dwqa')) {
					$sides[] = 'dwqa-question-primary';
					$sides[] = 'dwqa-question-secondary';
				}

				if (function_exists('is_bbpress')) {
					$sides[] = 'bbpress-primary';
					$sides[] = 'bbpress-secondary';
				}

				if (function_exists('is_buddypress')) {
					$sides[] = 'buddypress-primary';
					$sides[] = 'buddypress-secondary';
				}

				if (function_exists('EDD')) {
					$sides[] = 'download-primary';
					$sides[] = 'download-secondary';
				}
			}

			$titles = [

				'primary' 				=> Xtra_Strings::get('primary'),
				'secondary' 			=> Xtra_Strings::get('secondary'),
				'footer-1' 				=> Xtra_Strings::get('footer') . ' 1',
				'footer-2' 				=> Xtra_Strings::get('footer') . ' 2',
				'footer-3' 				=> Xtra_Strings::get('footer') . ' 3',
				'footer-4' 				=> Xtra_Strings::get('footer') . ' 4',
				'footer-5' 				=> Xtra_Strings::get('footer') . ' 5',
				'footer-6' 				=> Xtra_Strings::get('footer') . ' 6',
				'offcanvas_area' 		=> Xtra_Strings::get('offcanvas_area'),
				'product-primary' 		=> Xtra_Strings::get('product_primary'),
				'product-secondary' 	=> Xtra_Strings::get('product_secondary'),
				'portfolio-primary' 	=> Xtra_Strings::get('portfolio_primary'),
				'portfolio-secondary' 	=> Xtra_Strings::get('portfolio_secondary')

			];

			// Post types
			$cpt = (array) get_option('codevz_post_types');

			if (self::$plugin) {

				$cpt['portfolio'] = self::option('slug_portfolio', 'portfolio');
			}

			// Custom post type UI
			if (function_exists('cptui_get_post_type_slugs')) {
				$cptui = cptui_get_post_type_slugs();
				if (is_array($cptui)) {
					$cpt = wp_parse_args($cptui, $cpt);
				}
			}

			// All CPT
			foreach ($cpt as $key => $value) {
				if ($value) {
					if ($key === 'portfolio') {
						$sides['portfolio-primary'] = $value . '-primary';
						$sides['portfolio-secondary'] = $value . '-secondary';
					} else {
						$sides[] = $value . '-primary';
						$sides[] = $value . '-secondary';
					}
				}
			}

			// Custom sidebars
			$move_sidebars = get_option('codevz_move__custom_sidebars_to_options');
			if (empty($move_sidebars)) {
				$custom_s = (array) get_option('codevz_custom_sidebars');
				$sides = wp_parse_args($custom_s, $sides);

				$options = (array) get_option('codevz_theme_options');
				$options['custom_sidebars'] = $custom_s;
				update_option('codevz_theme_options', $options);
				update_option('codevz_move__custom_sidebars_to_options', 1);
			} else {
				$custom_s = (array) self::option('custom_sidebars');
				$sides = wp_parse_args($custom_s, $sides);
			}

			foreach ($sides as $key => $id) {
				if ($id) {
					$id = esc_html($id);

					if (isset($titles[$id])) {
						$name = $titles[$id];
					} else {
						$name = ucwords(str_replace(['cz-custom-', '-'], ' ', $id));
					}

					$class 	= self::contains($id, 'footer') ? 'footer_widget' : 'widget';

					if ($key === 'portfolio-primary') {
						$id = 'portfolio-primary';
					} else if ($key === 'portfolio-secondary') {
						$id = 'portfolio-secondary';
					}

					register_sidebar([
						'name'			=> $name,
						'id'			=> $id,
						'description'   => Xtra_Strings::get('add_widgets') . ' ' . $name,
						'before_widget'	=> '<div id="%1$s" class="' . esc_attr($class) . ' clr %2$s">',
						'after_widget'	=> '</div>',
						'before_title'	=> '<h4>',
						'after_title'	=> '</h4>'
					]);
				}
			}
		}

		/**
		 * WP head
		 * @return string
		 */
		public static function wp_head()
		{

			if (is_singular() && pings_open()) {

				$url = get_bloginfo('pingback_url');
				printf('<link rel="pingback" href="%s">' . "\n", esc_url($url));
			}
		}

		/**
		 * WP Menu current class
		 * @return string
		 */
		public static function menu_current_class($classes, $item)
		{

			$url = trailingslashit($item->url);
			$base = basename($url);

			// Default.
			$classes[] = 'cz';

			// Fix anchor links
			if (self::contains($url, '/#') || is_page_template()) {
				return $classes;
			}

			// Find parent menu
			$in_array = in_array('current_page_parent', $classes);

			// Current menu
			if (in_array('current-menu-ancestor', $classes) || in_array('current-menu-item', $classes) || ($in_array && get_post_type() === 'post')) {
				$classes[] = 'current_menu';
			}

			// Current menu parent.
			if (have_posts()) {

				$c = get_post_type_object(get_post_type(self::$post->ID));

				if (!empty($c)) {

					// Check custom link of post or page in menu.
					$con1 = (is_singular() && $url === trailingslashit(get_the_permalink(self::$post->ID)));

					// Check post type slug changes.
					$con2 = (isset($c->rewrite['slug']) && self::contains($base, $c->rewrite['slug']) && $in_array);

					// Check with post type name.
					$con3 = ($base === strtolower(urlencode(html_entity_decode($c->name))));

					// Fix multisite same name as post type name conflict.
					if ($con3 && trailingslashit(self::$home_url) === $url) {
						$con3 = false;
					}

					// Check with post type label.
					$con4 = ($base === strtolower(urlencode(html_entity_decode($c->label))));

					// Check if CPT name is different in menu URL and fix also for non-english lang.
					$con5 = ($base === strtolower(urlencode(html_entity_decode($c->has_archive))));

					if ($con1 || $con2 || $con3 || $con4 || $con5) {
						$classes[] = 'current_menu';
					}
				}
			}

			// Fix: single post with category in menu.
			if (in_array('menu-item-object-category', $classes) && is_single()) {

				$key = array_search('current-menu-parent', $classes);

				if (isset($classes[$key])) {
					unset($classes[$key]);
				}
			}

			return $classes;
		}

		/**
		 * Get page post type name.
		 * 
		 * @var Post id
		 * @return String
		 */
		public static function get_post_type($id = '', $page = false)
		{

			if (self::$plugin) {

				return Codevz_Plus::get_post_type($id, $page);
			} else {

				return get_post_type($id);
			}
		}

		/**
		 * Get page content and generate styles.
		 * 
		 * @var page ID or title.
		 * @return String
		 */
		public static function get_page_as_element($id = '', $query = 0)
		{

			if (self::$plugin) {

				echo Codevz_Plus::get_page_as_element($id, $query, true);
			}
		}

		/**
		 * Get required data attributes for body
		 * 
		 * @return string
		 */
		public static function intro_attrs()
		{

			$i = ' data-ajax="' . admin_url('admin-ajax.php') . '"';

			// Theme colors for live
			if (self::$preview) {
				$i .= ' data-primary-color="' . esc_attr(self::option('site_color', '#4e71fe')) . '"';
				$i .= ' data-primary-old-color="' . esc_attr(get_option('codevz_primary_color', self::option('site_color', '#4e71fe'))) . '"';
				$i .= ' data-secondary-color="' . esc_attr(self::option('site_color_sec', 0)) . '"';
				$i .= ' data-secondary-old-color="' . esc_attr(get_option('codevz_secondary_color', 0)) . '"';
			}

			return $i;
		}

		/**
		 * Check free and pro version.
		 * 
		 * @return bool
		 */
		public static function is_free()
		{

			$code = get_option('codevz_theme_activation');

			if (isset($code['purchase_code']) && isset($code['support_until']) && !self::contains($code['purchase_code'], '*')) {

				return false;
			}

			return true;
		}

		/**
		 * Get pro badge.
		 * 
		 * @return string
		 */
		public static function pro_badge($link = true)
		{

			if ($link) {

				return '<a href="' . esc_url(get_admin_url()) . 'admin.php?page=xtra-activation" target="_blank" class="xtra-pro"><span">' . esc_html(Xtra_Strings::get('pro')) . '</span></a>';
			} else {

				return '<span class="xtra-pro"><span">' . esc_html(Xtra_Strings::get('pro')) . '</span></span>';
			}
		}

		/**
		 * Filter WordPress excerpt length
		 * 
		 * @return string
		 */
		public static function excerpt_length()
		{

			$cpt = self::get_post_type();

			$default = 20;

			if ($cpt && $cpt !== 'post') {
				return self::option('post_excerpt_' . $cpt, $default);
			}

			return self::option('post_excerpt', $default);
		}

		/**
		 * Excerpt read more button
		 * 
		 * @return string
		 * @since 1.0
		 */
		public static function excerpt_more($more)
		{
			return '';
		}

		public static function get_the_excerpt($excerpt)
		{

			$cpt = self::get_post_type();

			if ($cpt && $cpt !== 'post' && self::contains($excerpt, ' ')) {
				$excerpt = implode(' ', array_slice(explode(' ', $excerpt), 0, self::option('post_excerpt' . (($cpt && $cpt !== 'post') ? '_' . $cpt : ''), 10) + 1));
			}

			// Read more title & icon
			if ($cpt && $cpt !== 'post') {
				$title = esc_html(self::option('readmore_' . $cpt));
				$icon = esc_attr(self::option('readmore_icon_' . $cpt));
			} else {
				$title = esc_html(self::option('readmore'));
				$icon = esc_attr(self::option('readmore_icon'));
			}

			$icon = $icon ? '<i class="' . $icon . '" aria-hidden="true"></i>' : '';
			$button = ($title || $icon) ? '<a class="cz_readmore' . ($title ? '' : ' cz_readmore_no_title') . ($icon ? '' : ' cz_readmore_no_icon') . '" href="' . esc_url(get_the_permalink(self::$post->ID)) . '">' . $icon . '<span>' . do_shortcode($title) . '</span></a>' : '';

			$excerpt_char = self::option(($cpt ? $cpt : 'post') . '_excerpt_type', false);

			if ($excerpt_char === '2') {
				$excerpt = substr($excerpt, 0, self::option('post_excerpt' . (($cpt && $cpt !== 'post') ? '_' . $cpt : ''), 20));
			}

			return $excerpt ? $excerpt . wp_kses_post(self::option($cpt . '_excerpt_dots', ' ... ')) . $button : '';
		}

		/**
		 * More tag read more button
		 * 
		 * @return string
		 * @since 2.6
		 */
		public static function the_content_more_link()
		{
			$cpt = self::get_post_type();

			if ($cpt && $cpt !== 'post') {
				$title = esc_html(self::option('readmore_' . $cpt));
				$icon = esc_attr(self::option('readmore_icon_' . $cpt));
			} else {
				$title = esc_html(self::option('readmore'));
				$icon = esc_attr(self::option('readmore_icon'));
			}

			$icon = $icon ? '<i class="' . $icon . '" aria-hidden="true"></i>' : '';

			$more = '';
			if (strpos(self::$post->post_content, '<!--more-->')) {
				$more = '#more-' . esc_attr(self::$post->ID);
			}

			return ($title || $icon) ? '<a class="cz_readmore' . ($title ? '' : ' cz_readmore_no_title') . ($icon ? '' : ' cz_readmore_no_icon') . '" href="' . esc_url(get_the_permalink(self::$post->ID)) . esc_attr($more) . '">' . $icon . '<span>' . $title . '</span></a>' : '';
		}

		/**
		 * Get next|prev posts for single post page
		 * 
		 * @return string
		 */
		public static function next_prev_item()
		{

			$cpt = self::get_post_type();
			$tax = ($cpt === 'post') ? 'category' : $cpt . '_cat';
			$prevPost = get_previous_post(true, '', $tax) ? get_previous_post(true, '', $tax) : get_previous_post();
			$nextPost = get_next_post(true, '', $tax) ? get_next_post(true, '', $tax) : get_next_post();

			if ($prevPost || $nextPost) { ?>

				</div>
				<div class="content cz_next_prev_posts clr">

					<ul class="next_prev clr">
						<?php if ($prevPost) { ?>
							<li class="previous">
								<?php $prevthumbnail = get_the_post_thumbnail($prevPost->ID, 'thumbnail'); ?>
								<?php previous_post_link('%link', '<i class="fa fa-angle-' . (self::$is_rtl ? 'right' : 'left') . '" aria-hidden="true"></i><h4><small>' . esc_html(do_shortcode(self::option('prev_' . $cpt, self::option('prev_post')))) . '</small>%title</h4>', self::option('next_prev_same_category', false)); ?>
							</li>
						<?php }
						if ($nextPost) { ?>
							<li class="next">
								<?php $nextthumbnail = get_the_post_thumbnail($nextPost->ID, 'thumbnail'); ?>
								<?php next_post_link('%link', '<h4><small>' . esc_html(do_shortcode(self::option('next_' . $cpt, self::option('next_post')))) . '</small>%title</h4><i class="fa fa-angle-' . (self::$is_rtl ? 'left' : 'right') . '" aria-hidden="true"></i>', self::option('next_prev_same_category', false)); ?>
							</li>
						<?php }

						$archive_icon = false; //self::option( 'next_prev_archive_icon' );
						if ($archive_icon) {
						?>
							<li class="cz-next-prev-archive">
								<a href="<?php echo esc_url(get_post_type_archive_link($cpt)); ?>" title="<?php echo esc_attr(ucwords($cpt)); ?>"><i class="<?php echo esc_attr($archive_icon); ?>" aria-hidden="true"></i></a>
							</li>
						<?php
						}
						?>
					</ul>

				<?php

			}
		}

		/**
		 * Modify category widget output
		 * 
		 * @return string
		 */
		public function wp_list_categories($i)
		{

			$i = preg_replace('/cat-item\scat-item-(.?[0-9])\s/', '', $i);
			$i = preg_replace('/current-cat/', 'current', $i);
			$i = preg_replace('/\sclass="cat-item\scat-item-(.?[0-9])"/', '', $i);
			$i = preg_replace('/\stitle="(.*?)"/', '', $i);
			$i = preg_replace('/\sclass=\'children\'/', '', $i);
			$i = str_replace('</a> (', '</a><span>(', $i);

			return str_replace(')', ')</span>', $i);
		}

		/**
		 * Modify archive widget output
		 * 
		 * @return string
		 */
		public function get_archives_link($i)
		{

			$i = str_replace('</a>&nbsp;(', '</a><span>(', $i);

			return str_replace(')', ')</span>', $i);
		}

		public function get_avatar($text)
		{

			$name = get_the_author_meta('display_name');

			return str_replace('alt=\'\'', 'alt=\'Avatar for ' . $name . '\' title=\'Gravatar for ' . $name . '\'', $text);
		}

		/**
		 * Enqueue google font
		 * 
		 * @return string|null
		 */
		public static function enqueue_font($f = '')
		{

			if (!$f || self::contains($f, 'custom_')) {
				return;
			} else {
				$f = self::contains($f, ';') ? self::get_string_between($f, 'font-family:', ';') : $f;
				$f = str_replace('=', ':', $f);
			}

			$defaults = apply_filters('csf/field/fonts/websafe', array(
				'inherit' 			=> 'inherit',
				'initial' 			=> 'initial',
				'FontAwesome' 		=> 'FontAwesome',
				'Font Awesome 5 Free' => 'Font Awesome 5 Free',
				'czicons' 			=> 'czicons',
				'fontelo' 			=> 'fontelo',
				'Arial' 			=> 'Arial',
				'Arial Black' 		=> 'Arial Black',
				'Comic Sans MS' 	=> 'Comic Sans MS',
				'Impact' 			=> 'Impact',
				'Lucida Sans Unicode' => 'Lucida Sans Unicode',
				'Tahoma' 			=> 'Tahoma',
				'Trebuchet MS' 		=> 'Trebuchet MS',
				'Verdana' 			=> 'Verdana',
				'Courier New' 		=> 'Courier New',
				'Lucida Console' 	=> 'Lucida Console',
				'Georgia, serif' 	=> 'Georgia, serif',
				'Palatino Linotype' => 'Palatino Linotype',
				'Times New Roman' 	=> 'Times New Roman'
			));

			// Custom fonts
			$custom_fonts = (array) self::option('custom_fonts');
			foreach ($custom_fonts as $a) {
				if (!empty($a['font'])) {
					$defaults[$a['font']] = $a['font'];
				}
			}

			$f = self::contains($f, ':') ? $f : $f . ':300,400,700';
			$f = explode(':', $f);
			$p = empty($f[1]) ? '' : ':' . trim($f[1]);

			$font = isset($f[0]) ? trim($f[0]) : '';

			$disable = apply_filters('xtra/disable/google_fonts', false);

			if (!$disable && $font && !isset($defaults[$font])) {
				wp_enqueue_style('google-font-' . sanitize_title_with_dashes($font), 'https://fonts.googleapis.com/css?family=' . str_replace(['"', "'"], '', str_replace(' ', '+', ucfirst($font))) . $p);
			}
		}

		/**
		 * SK Style + load font
		 * 
		 * @return string
		 */
		public static function sk_inline_style($sk = '', $important = false)
		{

			$sk = str_replace('CDVZ', '', $sk);

			if (self::contains($sk, 'font-family')) {

				self::enqueue_font($sk);

				// Extract font + params && Fix font for CSS
				$font = $o_font = self::get_string_between($sk, 'font-family:', ';');
				$font = str_replace('=', ':', $font);
				$font = str_replace("''", "", $font);

				if (self::contains($font, ':')) {

					$font = explode(':', $font);

					if (!empty($font[0])) {

						if (!self::contains($font[0], "'")) {
							$font[0] = "'" . $font[0] . "'";
						}

						$sk = str_replace($o_font, $font[0], $sk);
					}
				} else {

					if (!self::contains($font, "'")) {
						$font = "'" . $font . "'";
					}

					$sk = str_replace($o_font, $font, $sk);
				}
			}

			if ($important) {
				$sk = str_replace(';', ' !important;', $sk);
			}

			if (self::$is_rtl) {
				return str_replace('RTL', '', $sk);
			} else if (self::contains($sk, 'RTL')) {
				return strstr($sk, 'RTL', true);
			} else {
				return $sk;
			}
		}

		/**
		 * Get element for row builder 
		 * 
		 * @return string
		 */
		public static function get_row_element($i, $m = [])
		{

			// Check element
			if (empty($i['element'])) {
				return;
			}

			// Check user login
			$is_user_logged_in = is_user_logged_in();

			// Element visibility for users
			if (!empty($i['elm_visibility'])) {
				$v = $i['elm_visibility'];
				if (($v === '1' && !$is_user_logged_in) || ($v === '2' && $is_user_logged_in)) {
					return;
				}
			}

			// Element margin
			$style = '';
			if (!empty($i['margin'])) {
				foreach ($i['margin'] as $key => $val) {
					$style .= $val ? 'margin-' . esc_attr($key) . ':' . esc_attr($val) . ';' : '';
				}
			}

			// Cutstom page width
			if (!empty($i['header_elements_width'])) {
				$style .= 'width:' . esc_attr($i['header_elements_width']) . ';';
			}

			// Classes of element
			$elm_class = empty($i['vertical']) ? '' : ' cz_vertical_elm';
			$elm_class .= empty($i['elm_on_sticky']) ? '' : ' ' . $i['elm_on_sticky'];
			$elm_class .= empty($i['hide_on_mobile']) ? '' : ' hide_on_mobile';
			$elm_class .= empty($i['hide_on_tablet']) ? '' : ' hide_on_tablet';
			$elm_class .= empty($i['elm_center']) ? '' : ' cz_elm_center';

			// Start element
			$elm = $i['element'];
			$element_id = esc_attr($elm . '_' . $m['id']);
			$element_uid = esc_attr($element_id . $m['depth']);
			$data_settings = self::$preview ? " data-settings='" . json_encode($i, JSON_HEX_APOS) . "'" : '';
			echo '<div class="cz_elm ' . esc_attr($element_id . $m['depth'] . ' inner_' . $element_id . $m['inner_depth'] . $elm_class) . '" style="' . esc_attr($style) . '"' . wp_kses_post($data_settings) . '>';

			if (self::$preview) {

				echo '<i class="xtra-section-focus fas fa-cog" data-section="' . esc_attr($m['id']) . '" data-id="' . esc_attr($m['inner_depth']) . '" aria-hidden="true"></i>';

				if ($elm === 'logo' || $elm === 'logo_2') {
					echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-image" data-section="header_logo" aria-hidden="true"></i>';
				}

				if ($elm === 'social') {
					echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-pen" data-section="header_social" aria-hidden="true"></i>';
				}
			}

			$free = self::is_free();

			// Check pro.
			//if ( $free && self::contains( $elm, [ 'logo_2', 'wpml', 'wishlist', 'avatar', 'custom', 'custom_element' ] ) ) {

			//	echo '</div>';

			//	return false;

			//}

			// Check element
			if ($elm === 'logo' || $elm === 'logo_2') {

				$logo = do_shortcode(self::option($elm));

				if (!self::$plugin && get_theme_mod('custom_logo')) {

					$custom_logo = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
					$logo = isset($custom_logo[0]) ? $custom_logo[0] : $logo;
				}

				if ($logo) {

					$sizes = method_exists('Codevz_Plus', 'getimagesize') ? Codevz_Plus::getimagesize($logo) : '';

					if ($sizes) {

						list($lw, $lh) = $sizes;

						if (!empty($i['logo_width'])) {

							$nw = preg_replace('/[^0-9]/', '', $i['logo_width']);
							$lp = preg_replace('/[0-9]/', '', $i['logo_width']);

							$lh = (int) round(($lh * $nw) / $lw, 0);
							$lw = (int) $nw;
						}
					} else {

						$lw = empty($i['logo_width']) ? 'auto' : (int) $i['logo_width'];
						$lh = 'auto';
					}

					if (empty($lp)) {
						$lp = 'px';
					}

					$escaped_size_on_sticky = empty($i['logo_width_sticky']) ? '' : ' data-cz-style=".onSticky .' . esc_attr($element_id . $m['depth']) . ' .logo_is_img img{width:' . esc_attr($i['logo_width_sticky']) . ' !important}"';

					$logo_html = '<div class="logo_is_img ' . esc_attr($elm) . '"><a href="' . self::$home_url . '" title="' . esc_html(get_bloginfo('description')) . '"><img src="' . esc_url($logo) . '" alt="' . esc_attr(get_bloginfo('name')) . '" width="' . esc_attr($lw) . '" height="' . esc_attr($lh) . '" style="width: ' . esc_attr($lw . $lp) . '"' . $escaped_size_on_sticky . '></a>';
				} else {

					$desc = '';

					if (display_header_text()) {

						$desc = '<small>' . esc_html(get_bloginfo('description')) . '</small>';
					}

					$logo_html = '<div class="logo_is_text ' . esc_attr($elm) . '"><a href="' . self::$home_url . '" title="' . esc_html(get_bloginfo('description')) . '"><h1>' . esc_html(get_bloginfo('name')) . $desc . '</h1></a>';
				}

				// Lazyload logo.
				echo do_shortcode(self::$plugin ? Codevz_Plus::lazyload($logo_html) : $logo_html);

				$logo_tooltip = self::option('logo_hover_tooltip');

				if ($logo_tooltip && $logo_tooltip !== 'none' && $m['id'] !== 'header_4' && $m['id'] !== 'header_5') {

					echo '<div class="logo_hover_tooltip" data-cz-style=".logo_hover_tooltip{position:absolute;left:0;opacity:0;z-index:2;width:500px;padding:30px;margin:10px 0 0;background:#fff;border-radius:2px;visibility:hidden;box-sizing:border-box;box-shadow:0 8px 40px rgba(17,17,17,.1);transition:all .2s ease-in-out}.rtl .logo_hover_tooltip{left:0;right:0}.logo:hover .logo_hover_tooltip{margin:0;visibility:visible;opacity:1}footer .logo_hover_tooltip,footer .logo:hover .logo_hover_tooltip{display:none;visibility:hidden;opacity:0}">';

					self::get_page_as_element(esc_html($logo_tooltip));

					echo '</div>';
				}

				echo '</div>';
			} else if ($elm === 'menu') {

				$type = empty($i['menu_type']) ? 'cz_menu_default' : $i['menu_type'];
				if ($type === 'offcanvas_menu_left') {
					$type = 'offcanvas_menu inview_left';
				} else if ($type === 'offcanvas_menu_right') {
					$type = 'offcanvas_menu inview_right';
				}

				$elm_uniqid = 'cz_mi_' . rand(11111, 99999);

				$menu_title = isset($i['menu_title']) ? do_shortcode($i['menu_title']) : '';
				$menu_icon = empty($i['menu_icon']) ? 'fa fa-bars' : $i['menu_icon'];
				$icon_style = empty($i['sk_menu_icon']) ? '' : self::sk_inline_style($i['sk_menu_icon']);

				$data_style = empty($i['sk_menu_title']) ? '' : '.' . $elm_uniqid . ' span{' . self::sk_inline_style($i['sk_menu_title']) . '}';
				$data_style .= empty($i['sk_menu_title_hover']) ? '' : '.' . $elm_uniqid . ':hover span{' . self::sk_inline_style($i['sk_menu_title_hover']) . '}';
				$data_style .= empty($i['sk_menu_icon_hover']) ? '' : '.' . $elm_uniqid . ':hover{' . self::sk_inline_style($i['sk_menu_icon_hover'], true) . '}';

				$menu_icon_class = $menu_title ? ' icon_plus_text' : '';
				$menu_icon_class .= ' ' . $elm_uniqid;

				// Add icon and mobile menu
				if ($type && $type !== 'offcanvas_menu' && $type !== 'cz_menu_default') {
					echo '<i class="' . esc_attr($menu_icon . ' icon_' . $type . $menu_icon_class) . '" style="' . esc_attr($icon_style) . '"' . ($data_style ? ' data-cz-style="' . esc_attr($data_style) . '"' : '') . '><span>' . esc_html($menu_title) . '</span></i>';
				}
				echo '<i class="' . esc_attr($menu_icon . ' hide icon_mobile_' . $type . $menu_icon_class) . '" style="' . esc_attr($icon_style) . '"' . ($data_style ? ' data-cz-style="' . esc_attr($data_style) . '"' : '') . '><span>' . esc_html($menu_title) . '</span></i>';

				// Default
				if (empty($i['menu_location'])) {
					$i['menu_location'] = 'primary';
				}

				// Check for meta box and set one page instead primary
				$page_menu = self::meta(0, 'one_page');
				if ($page_menu && !self::contains($m['id'], 'footer')) {
					$i['menu_location'] = ($page_menu === '1') ? 'one-page' : $page_menu;
				}

				// Disable three dots auto responsive
				$type .= empty($i['menu_disable_dots']) ? '' : ' cz-not-three-dots';

				// Indicators
				$indicator  = self::get_string_between(self::option('_css_menu_indicator_a_' . $m['id']), '_class_indicator:', ';');
				$indicator2 = self::get_string_between(self::option('_css_menu_ul_indicator_a_' . $m['id']), '_class_indicator:', ';');

				// Menu
				wp_nav_menu(
					apply_filters(
						'xtra_nav_menu',
						[
							'theme_location' 	=> esc_attr($i['menu_location']),
							'cz_row_id' 		=> esc_attr($m['id']),
							'cz_indicator' 		=> $indicator,
							'container' 		=> false,
							'fallback_cb' 		=> false,
							'walker' 			=> class_exists('Codevz_Walker_nav') ? new Codevz_Walker_nav() : false,
							'items_wrap' 		=> '<ul id="' . esc_attr($element_id) . '" class="sf-menu clr ' . esc_attr($type) . '" data-indicator="' . esc_attr($indicator) . '" data-indicator2="' . esc_attr($indicator2) . '">%3$s</ul>'
						]
					)
				);

				$iconx = self::$plugin ? 'fa czico-198-cancel' : 'fa fa-times';

				echo '<i class="' . esc_attr($iconx) . ' cz_close_popup xtra-close-icon hide" aria-hidden="true"></i>';

				$mobile_menu_social = self::option('mobile_menu_social');
				$mobile_menu_text = self::option('mobile_menu_text');

				// Mobile menu additional.
				if ($element_id === 'menu_header_4' && ($mobile_menu_social || $mobile_menu_text || self::$preview)) {

					echo '<div class="xtra-mobile-menu-additional hide">';

					if (!self::is_free() && $mobile_menu_social && self::$plugin) {

						echo wp_kses_post(
							Codevz_Plus::social(
								[
									'color_mode' => esc_html(self::option('mobile_menu_social_color_mode'))
								]
							)
						);
					}

					if ($mobile_menu_text || self::$preview) {
						echo '<div class="xtra-mobile-menu-text">' . do_shortcode(wp_kses_post($mobile_menu_text)) . '</div>';
					}

					echo '</div>';
				}
			} else if ($elm === 'social' && self::$plugin) {

				$social = Codevz_Plus::social(
					[
						'type' 		=> isset($i['social_type']) ? $i['social_type'] : '',
						'columnar' 	=> isset($i['social_columnar']) ? $i['social_columnar'] : ''
					]
				);

				if (!empty($i['social_type'])) {

					$icon = empty($i['social_icon']) ? 'fas fa-share-alt' : $i['social_icon'];

					$icon_style = empty($i['sk_social_icon']) ? '' : ' style="' . self::sk_inline_style($i['sk_social_icon']) . '"';

					$icon = '<i class="xtra-social-icon-trigger ' . $icon . '"' . $icon_style . (empty($i['sk_social_icon_hover']) ? '' : ' data-cz-style=".' . $element_id . $m['depth'] . ' .xtra-social-icon-trigger:hover {' . self::sk_inline_style($i['sk_social_icon_hover'], true) . '}"') . '></i>';

					$container = empty($i['sk_social_container']) ? '' : self::sk_inline_style($i['sk_social_container']);

					if ($i['social_type'] === 'popup') {

						echo '<a href="#xtra-social-popup">' . wp_kses_post($icon) . '</a>';

						$iconx = self::$plugin ? 'fa czico-198-cancel' : 'fa fa-times';

						echo do_shortcode('[cz_popup id_popup="xtra-social-popup" icon="' . esc_attr($iconx) . '" sk_popup="' . wp_kses_post($container) . '" sk_icon="color:#fff;"]' . wp_kses_post($social) . '[/cz_popup]');
					} else {

						echo wp_kses_post($icon) . '<div class="xtra-social-dropdown" style="' . wp_kses_post($container) . '">' . wp_kses_post($social) . '</div>';
					}
				} else {

					echo wp_kses_post($social);
				}
			} else if ($elm === 'image' && isset($i['image'])) {

				$link = empty($i['image_link']) ? '' : do_shortcode($i['image_link']);
				$width = empty($i['image_width']) ? 'auto' : $i['image_width'];
				$new_tab = empty($i['image_new_tab']) ? '' : 'rel="noopener noreferrer" target="_blank"';

				if ($link) {
					echo '<a class="elm_h_image" href="' . esc_url($link) . '" ' . esc_html($new_tab) . '><img src="' . esc_url(do_shortcode($i['image'])) . '" alt="image" style="width:' . esc_attr($width) . '" width="' . esc_attr($width) . '" height="auto" /></a>';
				} else {
					echo '<img src="' . esc_url(do_shortcode($i['image'])) . '" alt="#" width="' . esc_attr($width) . '" height="auto" style="width:' . esc_attr($width) . '" />';
				}
			} else if ($elm === 'icon') {

				$link = isset($i['it_link']) ? do_shortcode($i['it_link']) : '';

				$text_style = empty($i['sk_it']) ? '' : self::sk_inline_style($i['sk_it']);
				$icon_style = empty($i['sk_it_icon']) ? '' : self::sk_inline_style($i['sk_it_icon']);

				$hover_css = empty($i['sk_it_hover']) ? '' : '.' . $element_id . $m['depth'] . ' .elm_icon_text:hover .it_text {' . self::sk_inline_style($i['sk_it_hover'], true) . '}';
				$hover_css .= empty($i['sk_it_icon_hover']) ? '' : '.' . $element_id . $m['depth'] . ' .elm_icon_text:hover > i {' . self::sk_inline_style($i['sk_it_icon_hover'], true) . '}';

				if ($link) {
					echo '<a class="elm_icon_text" href="' . esc_attr($link) . '"' . ($hover_css ? ' data-cz-style="' . wp_kses_post($hover_css) . '"' : '') . (empty($i['it_link_target']) ? '' : ' target="_blank"') . '>';
				} else {
					echo '<div class="elm_icon_text"' . ($hover_css ? ' data-cz-style="' . wp_kses_post($hover_css) . '"' : '') . '>';
				}

				if (!empty($i['it_icon'])) {
					echo '<i class="' . esc_attr($i['it_icon']) . '" style="' . esc_attr($icon_style) . '" aria-hidden="true"></i>';
				}

				if (!empty($i['it_text'])) {
					echo '<span class="it_text ' . esc_attr((empty($i['it_icon']) ? '' : 'ml10')) . '" style="' . esc_attr($text_style) . '">' . do_shortcode(wp_kses_post(str_replace('%year%', current_time('Y'), $i['it_text']))) . '</span>';
				} else {
					echo '<span class="it_text" aria-hidden="true"></span>';
				}

				if ($link) {
					echo '</a>';
				} else {
					echo '</div>';
				}
			} else if ($elm === 'icon_info') {

				wp_enqueue_script('xtra-icon-text');

				$link = isset($i['it_link']) ? do_shortcode($i['it_link']) : '';

				$text_style 	= empty($i['sk_it']) ? '' : self::sk_inline_style($i['sk_it']);
				$text_2_style 	= empty($i['sk_it_2']) ? '' : self::sk_inline_style($i['sk_it_2']);
				$icon_style 	= empty($i['sk_it_icon']) ? '' : self::sk_inline_style($i['sk_it_icon']);

				$wrap_style = empty($i['sk_it_wrap']) ? '' : self::sk_inline_style($i['sk_it_wrap']);
				$wrap_hover = empty($i['sk_it_wrap_hover']) ? '' : '.' . $element_id . $m['depth'] . ' .cz_elm_info_box:hover {' . self::sk_inline_style($i['sk_it_wrap_hover'], true) . '}';
				$wrap_hover .= empty($i['sk_it_hover']) ? '' : '.' . $element_id . $m['depth'] . ' .cz_elm_info_box:hover .cz_info_1 {' . self::sk_inline_style($i['sk_it_hover'], true) . '}';
				$wrap_hover .= empty($i['sk_it_2_hover']) ? '' : '.' . $element_id . $m['depth'] . ' .cz_elm_info_box:hover .cz_info_2 {' . self::sk_inline_style($i['sk_it_2_hover'], true) . '}';

				if ($link) {
					echo '<a class="cz_elm_info_box" href="' . esc_url($link) . '" style="' . esc_html($wrap_style) . '"' . (empty($i['it_link_target']) ? '' : ' target="_blank"') . ($wrap_hover ? ' data-cz-style="' . wp_kses_post($wrap_hover) . '"' : '') . '>';
				} else {
					echo '<div class="cz_elm_info_box" style="' . wp_kses_post($wrap_style) . '"' . ($wrap_hover ? ' data-cz-style="' . wp_kses_post($wrap_hover) . '"' : '') . '>';
				}

				if (!empty($i['it_icon'])) {
					echo '<i class="cz_info_icon ' . esc_attr($i['it_icon']) . '" style="' . esc_attr($icon_style) . '"' . (empty($i['sk_it_icon_hover']) ? '' : ' data-cz-style=".' . esc_attr($element_id . $m['depth']) . ' .cz_elm_info_box:hover i {' . self::sk_inline_style($i['sk_it_icon_hover'], true) . '}"') . '></i>';
				}

				echo '<div class="cz_info_content">';
				if (!empty($i['it_text'])) {
					echo '<span class="cz_info_1" style="' . esc_attr($text_style) . '">' . do_shortcode(wp_kses_post($i['it_text'])) . '</span>';
				}
				if (!empty($i['it_text_2'])) {
					echo '<span class="cz_info_2" style="' . esc_attr($text_2_style) . '">' . do_shortcode(wp_kses_post($i['it_text_2'])) . '</span>';
				}
				echo '</div>';

				if ($link) {
					echo '</a>';
				} else {
					echo '</div>';
				}
			} else if ($elm === 'search') {

				wp_enqueue_script('xtra-search');

				$icon_style = empty($i['sk_search_icon']) ? '' : self::sk_inline_style($i['sk_search_icon']);
				$icon_style_hover = empty($i['sk_search_icon_hover']) ? '' : '.' . $element_uid . ' .xtra-search-icon:hover{' . self::sk_inline_style($i['sk_search_icon_hover'], true) . '}';
				$icon_in_style = empty($i['sk_search_icon_in']) ? '' : self::sk_inline_style($i['sk_search_icon_in']);
				$input_style = empty($i['sk_search_input']) ? '' : self::sk_inline_style($i['sk_search_input']);
				$outer_style = empty($i['sk_search_con']) ? '' : self::sk_inline_style($i['sk_search_con']);
				$ajax_style = empty($i['sk_search_ajax']) ? '' : self::sk_inline_style($i['sk_search_ajax']);
				$icon = empty($i['search_icon']) ? 'fa fa-search' : $i['search_icon'];
				$ajax = empty($i['ajax_search']) ? '' : ' cz_ajax_search';

				$form_style = empty($i['search_form_width']) ? '' : 'width: ' . esc_attr($i['search_form_width']);

				$i['search_type'] = empty($i['search_type']) ? 'form' : $i['search_type'];
				$i['search_placeholder'] = empty($i['search_placeholder']) ? '' : do_shortcode($i['search_placeholder']);

				echo '<div class="search_with_icon search_style_' . esc_attr($i['search_type'] . $ajax) . '">';
				echo self::contains(esc_attr($i['search_type']), 'form') ? '' : '<i class="xtra-search-icon ' . esc_attr($icon) . '" style="' . esc_attr($icon_style) . '" data-cz-style="' . esc_attr($icon_style_hover) . '"></i>';

				$iconx = self::$plugin ? 'fa czico-198-cancel' : 'fa fa-times';

				echo '<i class="' . esc_attr($iconx) . ' cz_close_popup xtra-close-icon hide" aria-hidden="true"></i>';

				echo '<div class="outer_search" style="' . esc_attr($outer_style) . '"><div class="search" style="' . esc_attr($form_style) . '">'; ?>

					<form method="get" action="<?php echo esc_url(self::$home_url); ?>" autocomplete="off">

						<?php

						if ($i['search_type'] === 'icon_full') {
							echo '<span' . (empty($i['sk_search_title']) ? '' : ' style="' . esc_attr(self::sk_inline_style($i['sk_search_title'])) . '"') . '>' . esc_html($i['search_placeholder']) . '</span>';
							$i['search_placeholder'] = '';
						}

						if ($ajax) {
							echo '<input name="nonce" type="hidden" value="' . esc_attr(wp_create_nonce('ajax_search_nonce')) . '" />';
						}

						if (!empty($i['search_only_products'])) {
							echo '<input name="post_type" type="hidden" value="product" />';
						}

						if (!empty($i['search_no_thumbnail'])) {
							echo '<input name="no_thumbnail" type="hidden" value="' . esc_attr($i['search_no_thumbnail']) . '" />';
						}

						if (!empty($i['search_post_icon'])) {
							echo '<input name="search_post_icon" type="hidden" value="' . esc_attr($i['search_post_icon']) . '" />';
						}

						if (!empty($i['search_count'])) {
							echo '<input name="search_count" type="hidden" value="' . esc_attr($i['search_count']) . '" />';
						}

						if (!empty($i['sk_search_post_icon'])) {
							echo '<input name="sk_search_post_icon" type="hidden" value="' . esc_attr($i['sk_search_post_icon']) . '" />';
						}

						if (defined('ICL_LANGUAGE_CODE')) {
							echo '<input name="lang" type="hidden" value="' . esc_attr(ICL_LANGUAGE_CODE) . '" />';
						}

						?>

						<label id="searchLabel" class="hidden" for="xtraSearch"><?php echo esc_html($i['search_placeholder']); ?></label>

						<input id="xtraSearch" class="ajax_search_input" aria-labelledby="searchLabel" name="s" type="text" placeholder="<?php echo esc_attr($i['search_placeholder']); ?>" style="<?php echo esc_attr($input_style); ?>">

						<button type="submit" aria-label="<?php echo esc_attr(Xtra_Strings::get('search')); ?>"><i class="<?php echo wp_kses_post($icon); ?>" data-xtra-icon="<?php echo wp_kses_post($icon); ?>" style="<?php echo esc_attr($icon_in_style); ?>" aria-hidden="true"></i></button>

					</form>

					<div class="ajax_search_results" style="<?php echo esc_attr($ajax_style); ?>" aria-hidden="true"></div>

				</div><?php

						echo '</div></div>';
					} else if ($elm === 'widgets') {

						$elm_uniqid = 'cz_ofc_' . rand(11111, 99999);
						$con_style = empty($i['sk_offcanvas']) ? '' : self::sk_inline_style($i['sk_offcanvas']);
						$icon_style = empty($i['sk_offcanvas_icon']) ? '' : 'i.' . $elm_uniqid . '{' . self::sk_inline_style($i['sk_offcanvas_icon']) . '}';
						$icon_style .= empty($i['sk_offcanvas_icon_hover']) ? '' : 'i.' . $elm_uniqid . ':hover{' . self::sk_inline_style($i['sk_offcanvas_icon_hover']) . '}';
						$icon = empty($i['offcanvas_icon']) ? 'fa fa-bars' : $i['offcanvas_icon'];

						$menu_title = isset($i['menu_title']) ? $i['menu_title'] : '';
						$icon .= $menu_title ? ' icon_plus_text' : '';

						$icon_style .= empty($i['sk_menu_title']) ? '' : '.' . $elm_uniqid . ' span{' . self::sk_inline_style($i['sk_menu_title']) . '}';
						$icon_style .= empty($i['sk_menu_title_hover']) ? '' : '.' . $elm_uniqid . ':hover span{' . self::sk_inline_style($i['sk_menu_title_hover']) . '}';

						echo '<div class="offcanvas_container"><i class="' . esc_attr($icon . ' ' . $elm_uniqid) . '" data-cz-style="' . esc_attr($icon_style) . '"><span>' . esc_html($menu_title) . '</span></i><div class="offcanvas_area offcanvas_original ' . (empty($i['inview_position_widget']) ? 'inview_left' : esc_attr($i['inview_position_widget'])) . '" style="' . esc_attr($con_style) . '">';

						if (is_active_sidebar('offcanvas_area')) {

							ob_start();
							dynamic_sidebar('offcanvas_area');
							$offcanvas = ob_get_clean();

							if (self::$plugin && self::option('lazyload')) {
								echo Codevz_Plus::lazyload($offcanvas);
							} else {
								echo do_shortcode($offcanvas);
							}
						}

						echo '</div></div>';
					} else if ($elm === 'hf_elm') {

						$con_style = empty($i['sk_hf_elm']) ? '' : self::sk_inline_style($i['sk_hf_elm']);

						$elm_uniqid = 'cz_hf_' . rand(11111, 99999);
						$icon_style = empty($i['sk_hf_elm_icon']) ? '' : 'i.' . $elm_uniqid . '{' . self::sk_inline_style($i['sk_hf_elm_icon']) . '}';
						$icon_style .= empty($i['sk_hf_elm_icon_hover']) ? '' : 'i.' . $elm_uniqid . ':hover{' . self::sk_inline_style($i['sk_hf_elm_icon_hover']) . '}';

						$icon = empty($i['hf_elm_icon']) ? 'fa fa-bars' : $i['hf_elm_icon'];

						echo '<i class="hf_elm_icon ' . esc_attr($icon . ' ' . $elm_uniqid) . '" data-cz-style="' . wp_kses_post($icon_style) . '" aria-hidden="true"></i><div class="hf_elm_area" style="' . esc_attr($con_style) . '"><div class="row clr">';

						if (!empty($i['hf_elm_page'])) {

							self::get_page_as_element(esc_html($i['hf_elm_page']));
						}

						echo '</div></div>';
					} else if ($elm === 'shop_cart') {

						$shop_plugin = (empty($i['shop_plugin']) || $i['shop_plugin'] === 'woo') ? 'woo' : 'edd';

						$container = empty($i['sk_shop_container']) ? '' : self::sk_inline_style($i['sk_shop_container']);

						$icon_style = empty($i['sk_shop_icon']) ? '' : self::sk_inline_style($i['sk_shop_icon']);
						$icon = empty($i['shopcart_icon']) ? 'fa fa-shopping-basket' : $i['shopcart_icon'];

						$shop_style = empty($i['sk_shop_count']) ? '' : '.' . $element_uid . ' .cz_cart_count, .' . $element_uid . ' .cart_1 .cz_cart_count{' . esc_attr(self::sk_inline_style($i['sk_shop_count'])) . '}';
						$shop_style .= empty($i['sk_shop_content']) ? '' : '.' . $element_uid . ' .cz_cart_items{' . esc_attr(self::sk_inline_style($i['sk_shop_content'])) . '}';

						$cart_url = $cart_content = '';

						if ($shop_plugin === 'woo' && function_exists('is_woocommerce')) {
							$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#';
							$cart_content = '<div class="cz_cart">' . (self::$preview ? '<span class="cz_cart_count">2</span><div class="cz_cart_items cz_cart_dummy"><div><div class="cart_list"><div class="item_small"><a href="#" aria-hidden="true"></a><div class="cart_list_product_title cz_tooltip_up"><h3><a href="#">XXX</a></h3><div class="cart_list_product_quantity">1 x <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>32.00</span></div><a href="#" class="remove" data-product_id="1066" data-title="x"><i class="fa fa-trash" aria-hidden="true"></i></a></div></div><div class="item_small"><a href="#" aria-hidden="true"></a><div class="cart_list_product_title"><h3><a href="#">XXX</a></h3><div class="cart_list_product_quantity">1 x <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>32.00</span></div><a href="#" class="remove" data-product_id="1066" data-title="x"><i class="fa fa-trash" aria-hidden="true"></i></a></div></div></div><div class="cz_cart_buttons clr"><a href="#">XXX, <span><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>64.00</span></span></a><a href="#">XXX</a></div></div></div>' : '') . '</div>';
						} else if (function_exists('EDD')) {
							$cart_url = function_exists('edd_get_checkout_uri') ? edd_get_checkout_uri() : '#';
							$cart_content = '<div class="cz_cart_edd"><span class="cz_cart_count edd-cart-quantity">' . wp_kses_post(edd_get_cart_quantity()) . '</span><div class="cz_cart_items"><div><div class="cart_list">' . str_replace("&nbsp;", '', do_shortcode('[download_cart]')) . '</div></div></div></div>';
						}

						$shopcart_title = empty($i['shopcart_title']) ? '' : $i['shopcart_title'];

						echo '<div class="elms_shop_cart" data-cz-style="' . wp_kses_post($shop_style) . '">';
						echo '<a class="shop_icon noborder" href="' . esc_url($cart_url) . '" aria-label="' . esc_html(self::option('woo_cart', 'Cart')) . '" style="' . esc_attr($container) . '"><i class="' . esc_attr($icon) . '" style="' . esc_attr($icon_style) . '" aria-hidden="true"></i><span>' . do_shortcode(esc_html($shopcart_title)) . '</span></a>';
						echo wp_kses_post($cart_content);
						echo '</div>';
					} else if ($elm === 'wishlist') {

						$container = empty($i['sk_shop_container']) ? '' : self::sk_inline_style($i['sk_shop_container']);
						$icon_style = empty($i['sk_shop_icon']) ? '' : self::sk_inline_style($i['sk_shop_icon']);
						$icon = empty($i['shopcart_icon']) ? 'fa fa-heart-o' : $i['shopcart_icon'];

						$shopcart_title = empty($i['shopcart_title']) ? '' : $i['shopcart_title'];

						$i['wishlist_page'] = self::option('woo_wishlist_page', 'Wishlist');

						$page = get_page_by_title($i['wishlist_page'], 'object', 'page');
						if (!empty($page->ID)) {
							$link = get_permalink($page->ID);
						} else {
							$link = home_url('/wishlist');
						}

						$wishlist_title = $i['wishlist_page'];

						$shop_style = empty($i['sk_shop_count']) ? '' : '.cz_wishlist_count{' . esc_attr(self::sk_inline_style($i['sk_shop_count'])) . '}';

						echo '<div class="elms_wishlist" data-cz-style="' . wp_kses_post($shop_style) . '">';
						echo '<a class="wishlist_icon" href="' . esc_url($link) . '" title="' . esc_attr($wishlist_title) . '" style="' . esc_attr($container) . '"><i class="' . esc_attr($icon) . '" style="' . esc_attr($icon_style) . '" aria-hidden="true"></i><span>' . do_shortcode(esc_html($shopcart_title)) . '</span></a>';
						echo '<span class="cz_wishlist_count" aria-hidden="true"></span>';
						echo '</div>';
					} else if ($elm === 'line' && isset($i['line_type'])) {

						$line = empty($i['sk_line']) ? '' : self::sk_inline_style($i['sk_line']);
						echo '<div class="' . esc_attr($i['line_type']) . '" style="' . esc_attr($line) . '">&nbsp;</div>';
					} else if ($elm === 'button') {

						$elm_uniqid = 'cz_btn_' . rand(11111, 99999);
						$btn_css = empty($i['sk_btn']) ? '' : self::sk_inline_style($i['sk_btn']);
						$btn_hover = empty($i['sk_btn_hover']) ? '' : '.' . esc_attr($elm_uniqid) . ':hover{' . self::sk_inline_style($i['sk_btn_hover'], true) . '}';

						$btn_hover .= empty($i['sk_hf_elm_icon']) ? '' : '.' . esc_attr($elm_uniqid) . ' i {' . self::sk_inline_style($i['sk_hf_elm_icon']) . '}';
						$btn_hover .= empty($i['sk_hf_elm_icon_hover']) ? '' : '.' . esc_attr($elm_uniqid) . ':hover i {' . self::sk_inline_style($i['sk_hf_elm_icon_hover']) . '}';

						$icon_before = $icon_after = '';
						if (!empty($i['hf_elm_icon'])) {
							if (empty($i['btn_icon_pos'])) {
								$icon_before = '<i class="' . $i['hf_elm_icon'] . ' cz_btn_header_icon_before" aria-hidden="true"></i>';
							} else {
								$icon_after = '<i class="' . $i['hf_elm_icon'] . ' cz_btn_header_icon_after" aria-hidden="true"></i>';
							}
						}

						$target = empty($i['btn_link_target']) ? '' : ' target="_blank"';
						echo '<a class="cz_header_button ' . esc_attr($elm_uniqid) . '" href="' . (empty($i['btn_link']) ? '' : do_shortcode(esc_url($i['btn_link']))) . '" style="' . esc_attr($btn_css) . '" data-cz-style="' . esc_attr($btn_hover) . '"' . esc_html($target) . '>' . wp_kses_post($icon_before) . '<span>' . esc_html(empty($i['btn_title']) ? 'Button' : do_shortcode($i['btn_title'])) . '</span>' . wp_kses_post($icon_after) . '</a>';

						// Custom shortcode or HTML codes
					} else if ($elm === 'custom' && isset($i['custom'])) {

						echo do_shortcode(wp_kses_post($i['custom']));

						// WPML Switcher
					} else if ($elm === 'wpml' && function_exists('icl_get_languages')) {

						$wpml = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');

						if (is_array($wpml)) {
							$bg = empty($i['wpml_background']) ? '' : 'background: ' . esc_attr($i['wpml_background']) . '';
							echo '<div class="cz_language_switcher"' . (empty($i['wpml_opposite']) ? '' : ' data-cz-style=".cz_language_switcher a { display: none } .cz_language_switcher div { display: block; position: static; transform: none; } .cz_language_switcher div a { display: block; }"') . '><div style="' . esc_attr($bg) . '">';
							foreach ($wpml as $lang => $vals) {
								if (!empty($vals)) {

									$class = $vals['active'] ? 'cz_current_language' : '';
									if (empty($i['wpml_title'])) {
										$title = $vals['translated_name'];
									} else if ($i['wpml_title'] !== 'no_title') {
										$title = ucwords($vals[$i['wpml_title']]);
									} else {
										$title = '';
									}

									$color = '';
									if ($class && !empty($i['wpml_color'])) {
										$color = 'color: ' . esc_attr($i['wpml_current_color']);
									} else if (!$class && !empty($i['wpml_color'])) {
										$color = 'color: ' . esc_attr($i['wpml_color']);
									}

									if (!empty($i['wpml_flag'])) {
										echo '<a class="' . esc_attr($class) . '" href="' . esc_url($vals['url']) . '" style="' . esc_attr($color) . '"><img src="' . esc_url($vals['country_flag_url']) . '" alt="#" width="200" height="200" class="' . esc_attr($title ? 'mr8' : '') . '" />' . esc_html($title) . '</a>';
									} else {
										echo '<a class="' . esc_attr($class) . '" href="' . esc_url($vals['url']) . '" style="' . esc_attr($color) . '">' . esc_html($title) . '</a>';
									}
								}
							}
							echo '</div></div>';
						}

						// Custom page as element
					} else if ($elm === 'custom_element' && !empty($i['header_elements']) && $i['header_elements'] !== 'none') {

						self::get_page_as_element(esc_html($i['header_elements']));

						// Current user avatar
					} else if ($elm === 'avatar') {

						$sk_avatar = empty($i['sk_avatar']) ? '' : $i['sk_avatar'];
						$link = empty($i['avatar_link']) ? '' : $i['avatar_link'];
						$size = empty($i['avatar_size']) ? '' : $i['avatar_size'];

						echo '<a class="cz_user_gravatar" href="' . esc_url($link) . '" style="' . esc_attr($sk_avatar) . '">';
						if ($is_user_logged_in) {
							global $current_user;
							echo wp_kses_post(get_avatar(esc_html($current_user->user_email), esc_attr($size)));
						} else {
							echo wp_kses_post(get_avatar('xxx@xxx.xxx', esc_attr($size)));
						}
						echo '</a>';
					}

					// Close element
					echo '</div>';
				}

				/**
				 * Generate inner row elements positions
				 * 
				 * @return string
				 */
				public static function row_inner($id = 0, $pos = 0, $out = '')
				{

					if (isset($_POST['id']) && isset($_POST['pos'])) {

						$ajax = 1;
						$id = sanitize_text_field(wp_unslash($_POST['id']));
						$pos = sanitize_text_field(wp_unslash($_POST['pos']));
					}

					$elms = self::option($id . $pos);
					if ($elms) {

						$shape = self::get_string_between(self::option('_css_' . $id . $pos), '_class_shape:', ';');

						if ($shape) {
							$shape = ' ' . $shape;
						}

						$center = self::contains($pos, 'center');

						echo '<div class="elms' . esc_attr($pos . ' ' . $id . $pos . ($shape ? ' ' . $shape : '')) . '">';
						if ($center) {
							echo '<div>';
						}
						$inner_id = 0;
						foreach ((array) $elms as $v) {
							if (empty($v['element'])) {
								continue;
							}
							$more = [];
							$more['id'] = $id;
							$more['depth'] = $pos . '_' . self::$element++;
							$more['inner_depth'] = $pos . '_' . $inner_id++;

							self::get_row_element($v, $more);
						}
						if ($center) {
							echo '</div>';
						}
						echo '</div>';
					}

					if (isset($ajax)) {
						wp_die();
					}
				}

				/**
				 * Generate header|footer|side row elements
				 * 
				 * @return string
				 */
				public static function row($args)
				{

					ob_start();
					foreach ($args['nums'] as $num) {

						$id = esc_attr($args['id']);

						// Check if sticky header is not custom
						if ($num === '5' && !self::option('sticky_header')) {
							continue;
						}

						// Columns
						$left = self::option($id . $num . $args['left']);
						$right = self::option($id . $num . $args['right']);
						$center = self::option($id . $num . $args['center']);

						// Row Shape
						$shape = self::get_string_between(self::option('_css_row_' . $id . $num), '_class_shape:', ';');
						$shape = $shape ? ' ' . $shape : '';

						// Menu FX
						$menufx = self::get_string_between(self::option('_css_menu_a_hover_before_' . $id . $num), '_class_menu_fx:', ';');
						$menufx = $menufx ? ' ' . $menufx : '';

						// Menu FX
						$submenufx = self::get_string_between(self::option('_css_menu_ul_' . $id . $num), '_class_submenu_fx:', ';');
						$submenufx = $submenufx ? ' ' . $submenufx : '';

						// Check sticky header
						$sn = self::option('sticky_header');
						$sticky = (self::contains($sn, $num) && $id !== 'footer_') ? ' header_is_sticky' : '';

						$free = self::is_free();

						if ($free && ($sn == '12' || $sn == '23' || $sn == '13' || $sn == 'x')) {
							$sticky = '';
						}

						$sticky .= (self::option('smart_sticky') && ($sn === '1' || $sn === '2' || $sn === '3' || $sn === '5')) ? ' smart_sticky' : '';
						$sticky .= (self::option('mobile_sticky') && $id . $num === 'header_4') ? ' ' . self::option('mobile_sticky') : '';

						// Before mobile header
						$bmh = self::option('b_mobile_header');
						if ($num === '4' && $bmh && $bmh !== 'none') {

							echo '<div class="row clr cz_before_mobile_header">';

							self::get_page_as_element(self::option('b_mobile_header'));

							echo '</div>';
						}

						// Start
						if ($left || $center || $right) {

							do_action('xtra/before_' . $id . $num);

							echo '<div class="' . esc_attr($id . $num . ($center ? ' have_center' : '') . $shape . $sticky . $menufx . $submenufx) . '">';
							if ($args['row']) {
								echo '<div class="row elms_row"><div class="clr">';
							}

							self::row_inner($id . $num, $args['left']);
							self::row_inner($id . $num, $args['center']);
							self::row_inner($id . $num, $args['right']);

							if ($args['row']) {
								echo '</div></div>';
							}
							echo '</div>';

							do_action('xtra/after_' . $id . $num);
						}

						// After mobile header
						$amh = self::option('a_mobile_header');
						if ($num === '4' && $amh && $amh !== 'none') {

							echo '<div class="row clr cz_after_mobile_header">';

							self::get_page_as_element(esc_html(self::option('a_mobile_header')));

							echo '</div>';
						}
					}
					echo ob_get_clean();
				}

				/**
				 * Generate page
				 * 
				 * @return string
				 */
				public static function generate_page($page = '')
				{

					get_header();

					global $wp_query;

					// Settings
					$cpt = self::get_post_type('', true);
					$is_search = is_search();
					if ($is_search) {
						$option_cpt = '_search';
					} else if (is_home() || is_category() || is_tag() || $cpt === 'post') {
						$option_cpt = '_post';
					} else {
						$option_cpt = ($cpt === 'post' || $cpt === 'page' || empty($cpt)) ? '' : '_' . $cpt;
					}
					$title = self::option('page_title' . $option_cpt);
					$title = (!$title || $title === '1') ? self::option('page_title') : $title;
					$page_title_tag = self::option('page_title_tag', 'h1');
					$layout = self::option('layout' . $option_cpt);

					if (!$cpt || $cpt === 'post' || $cpt === 'page') {
						$primary = 'primary';
						$secondary = 'secondary';
					} else {
						$cpt_slug = get_post_type_object($cpt);
						$cpt_slug = isset($cpt_slug->name) ? $cpt_slug->name : $cpt;
						$primary = $cpt_slug . '-primary';
						$secondary = $cpt_slug . '-secondary';
					}

					$layout = (!$layout || $layout === '1') ? self::option('layout') : $layout;

					// Woo general single layout
					$woo_single_layout = self::option('layout_single_product');
					if ($page === 'woocommerce' && $woo_single_layout && $woo_single_layout !== '1' && is_single()) {
						$layout = $woo_single_layout;
					}

					$blank = ($layout === 'bpnp' || $layout === 'ws') ? 1 : 0;
					$is_404 = (is_404() || $page === '404');
					$current_id = $is_404 ? '404' : (isset(self::$post->ID) ? self::$post->ID : 0);

					if (is_singular() || $cpt === 'page' || $is_404) {

						// Single post layout.
						$single_layout = self::option('layout_single_post');
						if ($cpt === 'post' && $single_layout && $single_layout != '1') {
							$layout = self::option('layout_single_post');
						}

						// Default meta
						$single_meta_cpt = ($cpt === 'page' || empty($cpt)) ? 'post' : $cpt;
						$single_meta = (array) self::option('meta_data_' . $single_meta_cpt);
						$single_meta = array_flip($single_meta);

						// Post meta
						$meta = self::meta($current_id);

						// Set
						if (!empty($meta['layout']) && $meta['layout'] != '1') {
							$layout = $meta['layout'];
							$blank = ($meta['layout'] === 'none' || $meta['layout'] === 'bpnp') ? 1 : 0;

							if (!empty($meta['primary'])) {
								$primary = $meta['primary'];
							}
							if (!empty($meta['secondary'])) {
								$secondary = $meta['secondary'];
							}
						}

						$featured_image = 1;

						if (!empty($meta['hide_featured_image'])) {
							if ($meta['hide_featured_image'] === '1') {
								$featured_image = 0;
							} else {
								$featured_image = 1;
							}
						} else if (!isset($single_meta['image']) || ($cpt === 'page' && empty($meta['hide_featured_image']))) {
							$featured_image = 0;
						}
					}

					// Start page content
					$bpnp = ($layout === 'bpnp') ? ' cz_bpnp' : '';
					$bpnp .= empty($meta['page_content_margin']) ? '' : ' ' . $meta['page_content_margin'];
					echo '<div id="page_content" class="page_content' . esc_attr($bpnp) . '" role="main"><div class="row clr">';

					// Before content
					if ($is_404 || !is_active_sidebar($primary)) {
						echo '<section class="s12 clr">';
					} else if ($layout === 'both-side') {
						echo '<aside class="col s3 sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside><section class="col s6">';
					} else if ($layout === 'both-side2') {
						echo '<aside class="col s2 sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside><section class="col s8">';
					} else if ($layout === 'both-right') {
						echo '<section class="col s6">';
					} else if ($layout === 'both-right2') {
						echo '<section class="col s7">';
					} else if ($layout === 'right') {
						echo '<section class="col s8">';
					} else if ($layout === 'right-s') {
						echo '<section class="col s9">';
					} else if ($layout === 'center') {
						echo '<aside class="col s2" role="complementary">&nbsp</aside>';
						echo '<section class="col s8">';
					} else if ($layout === 'both-left') {
						echo '<section class="col s6 col_not_first righter">';
					} else if ($layout === 'both-left2') {
						echo '<section class="col s7 col_not_first righter">';
					} else if ($layout === 'left') {
						echo '<section class="col s8 col_not_first righter">';
					} else if ($layout === 'left-s') {
						echo '<section class="col s9 col_not_first righter">';
					} else {
						echo '<section class="s12 clr">';
					}

					$single_classes = '';

					if (is_single()) {
						$single_classes = ' ' . implode(' ', get_post_class());
						$single_classes .= self::contains($single_classes, ' product ') ? '' : ' single_con';
					}

					echo '<div class="' . esc_attr(($blank ? 'cz_is_blank' : 'content') . $single_classes) . ' clr">';

					// Action fire before content.
					do_action('xtra_before_archive_content', $cpt);
					do_action('xtra/archive/before', $cpt);

					if ($is_404) {

						$page_404 = get_page_by_path('page-404');

						if ($page_404) {

							self::get_page_as_element($page_404->ID);
						} else {

							echo '<h2 class="xtra-404"><span>' . do_shortcode(esc_html(self::option('404_title', '404'))) . '</span><small>' . do_shortcode(esc_html(self::option('404_msg', 'How did you get here?! It’s cool. We’ll help you out.'))) . '</small></h2>';

							if (self::option('disable_search_404')) {

								echo '<form class="search_404" method="get" action="' . esc_url(self::$home_url) . '">
							<input id="inputhead" name="s" type="text" value="">
							<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
						</form>';
							}

							echo '<a class="button" href="' . esc_url(self::$home_url) . '" style="margin: 80px auto 0;display:table">' . do_shortcode(esc_html(self::option('404_btn', 'Back to Homepage'))) . '</a>';
						}
					} else if ($page === 'page' || $page === 'single') {

						if (have_posts()) {

							the_post();

							$author_url = get_author_posts_url(get_the_author_meta('ID'));

							if ($page === 'single' && self::$preview) {

								if ($cpt === 'post') {
									echo '<i class="xtra-section-focus fas fa-cog" data-section="single_settings" aria-hidden="true"></i>';
									echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="single_styles" aria-hidden="true"></i>';
								} else {
									echo '<i class="xtra-section-focus fas fa-cog" data-section="' . esc_attr($cpt) . '_single_settings" aria-hidden="true"></i>';
									echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="' . esc_attr($cpt) . '_single_styles" aria-hidden="true"></i>';
								}
							}

							// Post title and date.
							if ($cpt !== 'elementor_library' && $page !== 'page' && !$blank && ($title === '1' || $title === '2' || $title === '8')) {

								do_action('xtra/single/before_title', self::$post);

								echo '<' . esc_attr($page_title_tag) . ' class="xtra-post-title section_title">' . wp_kses_post(get_the_title()) . '</' . esc_attr($page_title_tag) . '>';

								do_action('xtra/single/after_title', self::$post);

								echo '<span class="xtra-post-title-date"><a href="' . esc_url(self::$home_url . get_post_time('Y/m/d')) . '"><time datetime="' . esc_attr(get_the_date('c')) . '" itemprop="datePublished"><i class="far fa-clock mr8" aria-hidden="true"></i>' . esc_html(get_the_date()) . '</time></a></span>';

								do_action('xtra/single/after_title_date', self::$post);
							}

							// Single post
							if ($page === 'single' || ($page === 'page')) {

								// Featured image
								$featured_image_out = '';
								if ($featured_image && has_post_thumbnail()) {
									ob_start();
									echo '<div class="cz_single_fi">';
									the_post_thumbnail('full');
									$cap = get_the_post_thumbnail_caption();
									if ($cap) {
										echo '<p class="wp-caption-text">' . wp_kses_post($cap) . '</p>';
									}
									echo '</div><br />';
									$featured_image_out = ob_get_clean();
								}

								// Post format
								if (!empty($meta['post_format'])) {

									$get_post_format = get_post_format();

									if ($meta['post_format'] === 'gallery' && !empty($meta['gallery_layout'])) {

										$post_format_out = '[cz_gallery images="' . esc_attr($meta['gallery']) . '" layout="' . esc_attr($meta['gallery_layout']) . '" gap="' . esc_attr($meta['gallery_gap']) . '" slidestoshow="' . esc_attr($meta['gallery_slides_to_show']) . '"]';
										$featured_image_out = null;
									} else if ($meta['post_format'] === 'video') {

										$video_type = isset($meta['video_type']) ? $meta['video_type'] : '';
										$featured_image_out = null;

										if ($video_type === 'url') {

											$video_url = empty($meta['video_url']) ? 'https://www.youtube.com/watch?v=FyS_zcvmUr4' : $meta['video_url'];

											if (self::contains($video_url, 'vimeo') || is_numeric($video_url)) {

												if (!self::contains($video_url, '/video/')) {
													preg_match('/[0-9]{6,11}/', $video_url, $match);
													$video_url = empty($match[0]) ? '' : 'https://player.vimeo.com/video/' . $match[0];
												}
											} else if (!self::contains($video_url, '/embed/')) {

												preg_match('/^(embed\/|.*?^v=)|[\w+]{11,20}/', $video_url, $match);
												$video_url = empty($match[0]) ? '' : 'https://www.youtube.com/embed/' . $match[0];
											}

											$post_format_out = (self::$plugin && method_exists('Codevz_Plus', 'iframe')) ? Codevz_Plus::iframe($video_url, '800', '500') : '';
										} else if ($video_type === 'selfhost') {

											$video_file = isset($meta['video_file']) ? $meta['video_file'] : '';
											$post_format_out = do_shortcode('[video width="800" height="500" mp4="' . esc_attr($video_file) . '"]');
										} else if ($video_type === 'embed') {

											$video_embed = isset($meta['video_embed']) ? $meta['video_embed'] : '';
											$post_format_out = do_shortcode($video_embed);
										}
									} else if ($meta['post_format'] === 'audio') {

										$audio_file = isset($meta['audio_file']) ? $meta['audio_file'] : '';
										$post_format_out = do_shortcode('[audio mp3="' . esc_attr($audio_file) . '"]');
									} else if ($meta['post_format'] === 'quote') {

										$quote = isset($meta['quote']) ? $meta['quote'] : '';
										$cite = isset($meta['cite']) ? $meta['cite'] : '';
										$post_format_out = '<blockquote>' . $quote . '<cite>' . $cite . '</cite></blockquote>';
										$featured_image_out = null;
									}

									// Echo post format
									if ($post_format_out) {
										$post_format_out = '<div class="cz_single_post_format mb30">' . $post_format_out . '</div>';
									}
								}

								// Image and format
								if (isset($post_format_out)) {
									$fpf = do_shortcode($featured_image_out . $post_format_out);

									if (self::$plugin && self::option('lazyload')) {
										echo do_shortcode(Codevz_Plus::lazyload($fpf));
									} else {
										echo do_shortcode($fpf);
									}
								} else {
									echo do_shortcode(apply_filters('xtra/single/featured_image', do_shortcode($featured_image_out)));
								}
							}

							// Content
							echo '<div class="cz_post_content clr">';
							the_content();
							echo '</div>';

							// Pagination
							wp_link_pages([
								'before' => '<div class="pagination mt20 clr">',
								'after' => '</div>',
								'link_after' => '</b>',
								'link_before' => '<b>'
							]);

							// Single post type meta
							if ($page === 'single' && empty($wp_query->queried_object->taxonomy)) {

								do_action('xtra/single/before_meta', self::$post);

								echo '<div class="clr mt40 relative">';

								if (self::$preview) {
									if ($cpt === 'post') {
										echo '<i class="xtra-section-focus fas fa-cog" data-section="single_settings" aria-hidden="true"></i>';
										echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="single_styles" aria-hidden="true"></i>';
									} else {
										echo '<i class="xtra-section-focus fas fa-cog" data-section="' . esc_attr($cpt) . '_single_settings" aria-hidden="true"></i>';
										echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="' . esc_attr($cpt) . '_single_styles" aria-hidden="true"></i>';
									}
								}

								if (isset($single_meta['author'])) {
									echo '<p class="cz_post_author cz_post_cat mr10">';
									echo '<a href="#" title="icon"><i class="fas fa-user" aria-hidden="true"></i></a>';
									echo '<a href="' . esc_url($author_url) . '">' . esc_html(ucwords(get_the_author())) . '</a>';
									echo '</p>';
								}

								if (isset($single_meta['date'])) {
									echo '<p class="cz_post_date cz_post_cat mr10">';
									echo '<a href="#" title="icon"><i class="fas fa-clock" aria-hidden="true"></i></a>';
									echo '<a href="' . esc_url(self::$home_url . get_post_time('Y/m/d')) . '"><span class="cz_post_date"><time datetime="' . get_the_date('c') . '" itemprop="datePublished">' . esc_html(get_the_date()) . '</time></span></a>';
									echo '</p>';
								}

								if (isset($single_meta['cats'])) {

									echo '<p class="cz_post_cat mr10">';

									$cats = [];
									$tax = ($cpt === 'post') ? 'category' : $cpt . '_cat';

									$terms = (array) get_the_terms(get_the_id(), $tax);
									foreach ($terms as $term) {
										if (isset($term->term_id)) {
											$cats[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
										}
									}

									$cats = implode('', $cats);
									$pre = '<a href="#" title="icon"><i class="fa fa-folder-open" aria-hidden="true"></i></a>';

									echo wp_kses_post($cats ? $pre . $cats : '');

									echo '</p>';
								}

								if (isset($single_meta['tags'])) {

									$tags = '';
									$tax = get_object_taxonomies($cpt, 'objects');

									foreach ($tax as $tax_slug => $taks) {

										$terms = get_the_terms(get_the_id(), $tax_slug);

										if (!empty($terms) && self::contains($taks->name, 'tag')) {

											$tags .= '<p class="tagcloud"><a href="#"><i class="fa fa-tags" aria-hidden="true"></i></a>';
											foreach ($terms as $term) {
												$tags .= '<a href="' . esc_url(get_term_link($term->slug, $tax_slug)) . '">' . esc_html($term->name) . '</a>';
											}
											$tags .= "</p>";
										}
									}

									echo wp_kses_post($tags);
								}

								echo '</div>';

								do_action('xtra/single/after_meta', self::$post);

								// Show social share icons.
								do_action('xtra/share', self::$post);

								do_action('xtra/single/after_social_share', self::$post);

								if (isset($single_meta['next_prev'])) {

									self::next_prev_item();

									if (self::$preview) {
										if ($cpt === 'post') {
											echo '<i class="xtra-section-focus fas fa-cog" data-section="single_settings" aria-hidden="true"></i>';
											echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="single_styles" aria-hidden="true"></i>';
										} else {
											echo '<i class="xtra-section-focus fas fa-cog" data-section="' . esc_attr($cpt) . '_single_settings" aria-hidden="true"></i>';
											echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="' . esc_attr($cpt) . '_single_styles" aria-hidden="true"></i>';
										}
									}
								}

								do_action('xtra/single/after_next_prev', self::$post);

								if ($cpt === 'post' && self::author_box()) {

									echo '</div><div class="content cz_author_box clr">';
									echo '<h4>' . esc_html(ucfirst(get_the_author_meta('display_name'))) . '<small class="righter cz_view_author_posts"><a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' .  esc_html(Xtra_Strings::get('author_posts')) . ' <i class="fa fa-angle-double-' . (self::$is_rtl ? 'left' : 'right') . ' ml4" aria-hidden="true"></i></a></small></h4>';
									echo wp_kses_post(self::author_box());

									if (self::$preview) {
										if ($cpt === 'post') {
											echo '<i class="xtra-section-focus fas fa-cog" data-section="single_settings" aria-hidden="true"></i>';
											echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="single_styles" aria-hidden="true"></i>';
										} else {
											echo '<i class="xtra-section-focus fas fa-cog" data-section="' . esc_attr($cpt) . '_single_settings" aria-hidden="true"></i>';
											echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="' . esc_attr($cpt) . '_single_styles" aria-hidden="true"></i>';
										}
									}
								}

								do_action('xtra/single/after_author_box', self::$post);

								$related_ppp = self::option('related_' . $single_meta_cpt . '_ppp');

								if ($related_ppp && $cpt !== 'page' && $cpt !== 'product' && $cpt !== 'download') {

									self::related([
										'posts_per_page' 	=> esc_attr($related_ppp),
										'related_columns' 	=> esc_attr(self::option('related_' . $single_meta_cpt . '_col', 's4')),
										'section_title' 	=> esc_html(do_shortcode(self::option('related_posts_' . $single_meta_cpt, 'Related Posts ...')))
									]);
								}

								do_action('xtra/single/after_related_posts', self::$post);
							} else {

								do_action('xtra/share', self::$post); // Share icons.

							}
						}

						// Woocommerce shop
					} else if ($page === 'woocommerce') {

						if (self::$preview) {
							if (is_single()) {
								echo '<i class="xtra-section-focus fas fa-cog" data-section="product" aria-hidden="true"></i>';
								echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="product_sk" aria-hidden="true"></i>';
							} else {
								echo '<i class="xtra-section-focus fas fa-cog" data-section="products" aria-hidden="true"></i>';
								echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="products_sk" aria-hidden="true"></i>';
							}
						}

						woocommerce_content();

						// Easy digital download
					} else if ($cpt === 'download') {

						if (have_posts()) {
							echo '<div class="cz_edd_container"><div class="clr mb30">';

							$edd_col = self::option('edd_col', '3');
							if ($edd_col === '2') {
								$edd_col_class = 's6';
							} else if ($edd_col === '4') {
								$edd_col_class = 's3';
							} else if ($edd_col === '3') {
								$edd_col_class = 's4';
							}

							$i = 1;
							while (have_posts()) {
								the_post();
								$id = get_the_ID();
								$link = get_the_permalink();
								$title = get_the_title();
								$author_url = get_author_posts_url(get_the_author_meta('ID'));

								echo '<div class="' . esc_attr(implode(' ', get_post_class('cz_edd_item col ' . $edd_col_class))) . '"><article>';
								if (has_post_thumbnail()) {
									echo '<a class="cz_edd_image" href="' . esc_url($link) . '">';
									the_post_thumbnail('codevz_600_600');
									echo wp_kses_post(edd_price($id));
									echo '</a>';
								}
								echo '<a class="cz_edd_title" href="' . esc_url($link) . '"><h3>' . wp_kses_post($title) . '</h3></a>';
								echo do_shortcode('[purchase_link id="' . esc_attr($id) . '"]');
								echo '</article></div>';

								// Clearfix
								if ($i % $edd_col === 0) {
									echo '</div><div class="clr mb30">';
								}

								$i++;
							}

							echo '</div></div>'; // row

							// Pagination
							echo '<div class="clr tac">';
							the_posts_pagination([
								'prev_text'          => self::$is_rtl ? '<i class="fa fa-angle-double-right mr4" aria-hidden="true"></i>' : '<i class="fa fa-angle-double-left mr4" aria-hidden="true"></i>',
								'next_text'          => self::$is_rtl ? '<i class="fa fa-angle-double-left ml4" aria-hidden="true"></i>' : '<i class="fa fa-angle-double-right ml4" aria-hidden="true"></i>',
								'before_page_number' => ''
							]);
							echo '</div>';
						}

						// Archive posts
					} else if (have_posts()) {

						// Archive title
						if (!is_home() && !is_post_type_archive() && ($title === '2' || $title === '8')) {

							do_action('xtra/page/before_title', self::$post);

							self::page_title($page_title_tag);

							do_action('xtra/page/after_title', self::$post);
						}

						$description = '';

						if (is_category() && category_description()) {

							$description = category_description();
						} else if (is_tag() && tag_description()) {

							$description = tag_description();
						} else if (is_tax() && term_description(get_query_var('term_id'), get_query_var('taxonomy'))) {

							$description = term_description(get_query_var('term_id'), get_query_var('taxonomy'));
						}

						if ($description) {

							echo '<div class="xtra-archive-desc mb50">' . wp_kses_post($description) . '</div>';
						}

						// Author box
						if (is_author() && self::author_box()) {
							echo '<h3>' . esc_html(ucfirst(get_the_author_meta('display_name'))) . '<small class="righter cz_view_author_posts"><a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(Xtra_Strings::get('view_all_posts')) . ' <i class="fa fa-angle-double-right ml4" aria-hidden="true"></i></a></small></h3>';
							echo wp_kses_post(self::author_box());
							echo '</div><div class="content clr">';
						}

						// Archive title
						$archive_desc = self::option('desc_' . $cpt);
						if ($archive_desc) {
							echo '<p>' . do_shortcode(wp_kses_post($archive_desc)) . '</p>';
						}

						// Template
						$template = self::option('template_style');

						if ($cpt && $cpt !== 'post' && $cpt !== 'page') {
							$template = self::option('template_style_' . $cpt, $template);
							$x_height = self::option('2x_height_image_' . $cpt);
							$excerpt = self::option('post_excerpt_' . $cpt, 20);
						} else {
							$cpt = 'post';
							$x_height = self::option('2x_height_image');
							$excerpt = self::option('post_excerpt', 20);
						}

						$custom_template = self::option('template_' . $cpt);

						if ($template === 'x' && $custom_template && $custom_template !== 'none') {

							self::get_page_as_element(esc_html($custom_template), 1);
						} else {

							$gallery_mode = '';
							if ($template === '9' || $template === '10' || $template === '11') {
								$gallery_mode = ' cz_posts_gallery_mode';
							}

							$post_class = '';
							$svg = self::option('default_svg_post') ? 'cz_post_svg' : '';

							// Sizes
							$image_size = 'codevz_360_320';
							$svg_w = '360';
							$svg_h = '320';
							if ($template == '2') {
								$post_class .= ' cz_default_loop_right';
							} else if ($template == '3') {
								$post_class .= ' cz_default_loop_full';
								$image_size = 'codevz_1200_500';
								$svg_w = '1200';
								$svg_h = '500';
							} else if ($template == '4' || $template == '9') {
								$post_class .= ' cz_default_loop_grid col s6';
							} else if ($template == '5' || $template == '10') {
								$post_class .= ' cz_default_loop_grid col s4';
							} else if ($template == '7' || $template == '11') {
								$post_class .= ' cz_default_loop_grid col s3';
							} else if ($template == '8') {
								$post_class .= ' cz_default_loop_full';
								$image_size = 'codevz_1200_200';
								$svg_w = '1200';
								$svg_h = '200';
							}

							// Square size
							if ($template === '4' || $template === '12') {
								$image_size = 'codevz_600_600';
								$svg_w = $svg_h = '600';
							}

							// Square size
							if ($template === '9' || $template === '10' || $template === '11') {
								$post_class .= ' cz_default_loop_square';
								$image_size = 'codevz_600_600';
								$svg_w = $svg_h = '600';
							}

							// Vertical size
							if ($x_height && $template !== '3') {
								$image_size = 'codevz_600_1000';
								$svg_w = '600';
								$svg_h = '1000';

								if ($template === '8') {
									$image_size = 'codevz_1200_500';
									$svg_w = '1200';
									$svg_h = '500';
								}
							}

							$image_size = apply_filters('xtra/archive/thumbnail_size', $image_size, $cpt);

							// Clearfix
							$clr = 999;
							if ($template === '4' || $template === '9') {
								$clr = 2;
							} else if ($template === '5' || $template === '10') {
								$clr = 3;
							} else if ($template === '7' || $template === '11') {
								$clr = 4;
							}

							// Post hover icon
							if (self::contains(self::option('hover_icon_' . $cpt), ['image', 'imhoh', 'iasi'])) {
								$post_hover_icon = '<i class="cz_post_icon"><img src="' . self::option('hover_icon_image_' . $cpt) . '" /></i>';
							} else if (self::option('hover_icon_' . $cpt) === 'none') {
								$post_hover_icon = '';
							} else {
								$post_hover_icon = '<i class="cz_post_icon ' . self::option('hover_icon_icon_' . $cpt, 'fa czico-109-link-symbol-1') . '" aria-hidden="true"></i>';
							}
							if (self::option('hover_icon_' . $cpt) === 'ihoh' || self::option('hover_icon_' . $cpt) === 'imhoh') {
								$gallery_mode .= ' cz_post_hover_icon_hoh';
							} else if (self::option('hover_icon_' . $cpt) === 'asi' || self::option('hover_icon_' . $cpt) === 'iasi') {
								$gallery_mode .= ' cz_post_hover_icon_asi';
							}

							echo '<div class="cz_posts_container cz_posts_template_' . esc_attr($template . $gallery_mode) . '">';

							if (self::$preview) {
								if ($cpt === 'post') {
									echo '<i class="xtra-section-focus fas fa-cog" data-section="blog_settings" aria-hidden="true"></i>';
									echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="blog_styles" aria-hidden="true"></i>';
								} else {
									echo '<i class="xtra-section-focus fas fa-cog" data-section="' . esc_attr($cpt) . '_settings" aria-hidden="true"></i>';
									echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="' . esc_attr($cpt) . '_styles" aria-hidden="true"></i>';
								}
							}

							echo '<div class="clr mb30">';

							// Chess style
							$chess = 0;
							if (self::contains($template, ['12', '13', '14'])) {
								$chess = 1;
							}

							$i = 1;
							while (have_posts()) {
								the_post();
								$link = get_the_permalink();
								$title = get_the_title();
								$author_url = get_author_posts_url(get_the_author_meta('ID'));
								$even_odd = '';
								if ($template === '6') {
									$even_odd = ($i % 2 == 0) ? ' cz_post_even cz_default_loop_right' : ' cz_post_odd';
								}

								echo '<article class="' . esc_attr(implode(' ', get_post_class('cz_default_loop clr' . $post_class . $even_odd))) . '"><div class="clr">';

								do_action('xtra/archive/before_thumbnail');

								// Thumbnail.
								if (has_post_thumbnail()) {

									echo '<a class="cz_post_image" href="' . esc_url($link) . '">';
									// 							the_post_thumbnail( $image_size );
									the_post_thumbnail('full');
									echo wp_kses_post($post_hover_icon) . '</a>';
								} else if ($svg) {

									echo '<a class="cz_post_image ' . esc_attr($svg) . '" href="' . esc_url($link) . '">';
									echo '<img src="data:image/svg+xml,%3Csvg%20xmlns%3D&#39;http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg&#39;%20width=&#39;' . esc_attr($svg_w) . '&#39;%20height=&#39;' . esc_attr($svg_h) . '&#39;%20viewBox%3D&#39;0%200%20' . esc_attr($svg_w) . '%20' . esc_attr($svg_h) . '&#39;%2F%3E" alt="Placeholder" />';
									echo wp_kses_post($post_hover_icon) . '</a>';
								}

								do_action('xtra/archive/after_thumbnail');

								if ($chess) {

									echo '<div class="cz_post_chess_content cz_post_con">';
									echo '<a class="cz_post_title" href="' . esc_url($link) . '"><h3>' . wp_kses_post($title) . '</h3><small><span class="cz_post_date"><time datetime="' . esc_attr(get_the_date('c')) . '" itemprop="datePublished">' . esc_html(get_the_date()) . '</time></span></small></a>';
									echo wp_kses_post(self::excerpt_more(1));
									echo '</div>';
								} else {

									echo '<div class="cz_post_con">';
									echo '<a class="cz_post_title" href="' . esc_url($link) . '"><h3>' . wp_kses_post($title) . '</h3></a>';
									$author_url = get_author_posts_url(get_the_author_meta('ID'));

									do_action('xtra/archive/before_meta');
									echo '<span class="cz_post_meta mt10 mb10">';
									echo '<a class="cz_post_author_avatar" href="' . esc_url($author_url) . '" title="Avatar">' . wp_kses_post(get_avatar(get_the_author_meta('ID'), 40)) . '</a>';
									echo '<span class="cz_post_inner_meta">';
									echo '<a class="cz_post_author_name" href="' . esc_url($author_url) . '">' . esc_html(ucwords(get_the_author())) . '</a>';
									echo '<span class="cz_post_date"><time datetime="' . esc_attr(get_the_date('c')) . '" itemprop="datePublished">' . esc_html(get_the_date()) . '</time></span>';
									echo '</span></span>';
									do_action('xtra/archive/after_meta');

									if (empty($template) || self::contains($template, ['1', '2', '3', '4', '5', '6', '7'])) {

										if ($excerpt !== '-1') {
											$ex = get_the_excerpt();
										} else {
											ob_start();
											the_content();
											$ex = ob_get_clean();
										}

										echo '<div class="cz_post_excerpt">' . wp_kses_post($ex) . '</div>';
									}

									echo '</div>';
								}

								if (!$title) {
									echo wp_kses_post(self::the_content_more_link());
								}

								echo '</div>';
								echo '</article>';

								// Clearfix
								if ($i % $clr === 0) {
									echo '</div><div class="clr mb30">';
								}

								$i++;
							}

							echo '</div></div>'; // row

							// Pagination
							echo '<div class="clr tac relative">';

							if (self::$preview) {
								echo '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="blog_styles" aria-hidden="true"></i>';
							}

							do_action('xtra/before_pagination');

							the_posts_pagination([
								'prev_text' 		=> self::$is_rtl ? '<i class="fa fa-angle-double-right mr4" aria-hidden="true"></i>' : '<i class="fa fa-angle-double-left mr4" aria-hidden="true"></i>',
								'next_text' 		=> self::$is_rtl ? '<i class="fa fa-angle-double-left ml4" aria-hidden="true"></i>' : '<i class="fa fa-angle-double-right ml4" aria-hidden="true"></i>',
								'before_page_number' => ''
							]);

							do_action('xtra/after_pagination');

							echo '</div>';
						}
					} else {
						echo '<h3>' . esc_html(do_shortcode(self::option('not_found', Xtra_Strings::get('not_found')))) . '</h3><p>' . esc_html(do_shortcode(self::option('not_found_msg', Xtra_Strings::get('search_error')))) . '</p>';
						echo '<form class="search_404 search_not_found" method="get" action="' . esc_url(self::$home_url) . '">
					<input id="inputhead" name="s" type="text" value="" placeholder="' . esc_attr(Xtra_Strings::get('search')) . '">
					<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
				</form>';
					}

					// Action fire after content.
					do_action('xtra_after_archive_content', $cpt);
					do_action('xtra/archive/after', $cpt);

					echo '</div>'; // content

					// Comments.
					if (is_single() || is_page()) {

						if (!$is_404 && comments_open()) {

							do_action('xtra/single/before_comments');

							echo '<div id="comments" class="content xtra-comments clr">';

							if (self::$preview) {
								echo '<i class="xtra-section-focus fas fa-paint-brush" data-section="single_styles" aria-hidden="true"></i>';
							}

							comments_template();
							echo '</div>';

							do_action('xtra/single/after_comments');
						} else if (isset($wp_query->queried_object->post_type) && $wp_query->queried_object->post_type == 'post') {
							echo '<p class="cz_nocomment mb10" style="opacity:.4"><i>' . esc_html(do_shortcode(self::option('cm_disabled'))) . '</i></p>';
						}
					}

					echo '</section>';

					// After content
					if ($is_404 || !is_active_sidebar($primary)) {

						$x = '';
					} else if ($layout === 'right') {
						echo '<aside class="col s4 sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside>';
					} else if ($layout === 'right-s') {
						echo '<aside class="col s3 sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside>';
					} else if ($layout === 'left') {
						echo '<aside class="col s4 col_first sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside>';
					} else if ($layout === 'left-s') {
						echo '<aside class="col s3 col_first sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside>';
					} else if ($layout === 'center') {
						echo '<aside class="col s2">&nbsp</aside>';
					} else if ($layout === 'both-side') {
						echo '<aside class="col s3 righter sidebar_secondary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($secondary)) {
							dynamic_sidebar($secondary);
						}
						echo '</div></aside>';
					} else if ($layout === 'both-side2') {
						echo '<aside class="col s2 righter sidebar_secondary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($secondary)) {
							dynamic_sidebar($secondary);
						}
						echo '</div></aside>';
					} else if ($layout === 'both-right') {
						echo '<aside class="col s3 sidebar_secondary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($secondary)) {
							dynamic_sidebar($secondary);
						}
						echo '</div></aside><aside class="col s3 sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside>';
					} else if ($layout === 'both-right2') {
						echo '<aside class="col s2 sidebar_secondary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($secondary)) {
							dynamic_sidebar($secondary);
						}
						echo '</div></aside><aside class="col s3 sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside>';
					} else if ($layout === 'both-left') {
						echo '<aside class="col s3 col_first sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside><aside class="col s3 sidebar_secondary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($secondary)) {
							dynamic_sidebar($secondary);
						}
						echo '</div></aside>';
					} else if ($layout === 'both-left2') {
						echo '<aside class="col s3 col_first sidebar_primary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($primary)) {
							dynamic_sidebar($primary);
						}
						echo '</div></aside><aside class="col s2 sidebar_secondary" role="complementary"><div class="sidebar_inner">';
						if (is_active_sidebar($secondary)) {
							dynamic_sidebar($secondary);
						}
						echo '</div></aside>';
					}

					echo '</div></div>'; // row, page_content
					get_footer();
				}

				/**
				 * Get related posts for single post page
				 * 
				 * @return string
				 */
				public static function related($args = [])
				{

					$id = self::$post->ID;
					$cpt = get_post_type($id);
					$meta = self::meta();

					// Settings
					$args = wp_parse_args($args, [
						'extra_class'		=> '',
						'post_type'			=> $cpt,
						'post__not_in'		=> [$id],
						'posts_per_page'	=> 3,
						'related_columns'	=> 's4'
					]);

					// By tags
					$args['tax_query'] = ['relation' => 'OR'];
					$tax = ($cpt === 'post') ? '_tag' : '_tags';
					$tags = wp_get_post_terms($id, $cpt . $tax, []);
					$args['tax_query'][] = [
						'taxonomy' 	=> $cpt . $tax,
						'field' 	=> 'slug',
						'terms' 	=> 'fix-query-by-tags'
					];

					if (is_array($tags)) {
						foreach ($tags as $tag) {
							if (!empty($tag->slug)) {
								$args['tax_query'][] = [
									'taxonomy' 	=> $cpt . $tax,
									'field' 	=> 'slug',
									'terms' 	=> $tag->slug
								];
							}
						}
					}

					// Generate query
					$query = new WP_Query($args);

					// If posts not found, try categories
					if (empty($query->post_count)) {
						if ($cpt === 'post') {
							$args['category__in'] = wp_get_post_categories($id, ['fields' => 'ids']);
						} else {
							$taxonomy = $cpt . '_cat';
							$get_cats = get_the_terms($id, $taxonomy);
							if (!empty($get_cats)) {
								$tax = ['relation' => 'OR'];
								foreach ($get_cats as $key) {
									if (is_object($key)) {
										$tax[] = [
											'taxonomy' 	=> $taxonomy,
											'terms' 	=> $key->term_id
										];
									}
								}
								$args['tax_query'] = $tax;
							}
						}

						// Regenerate query
						wp_reset_postdata();
						$query = new WP_Query($args);
					}

					// Output
					ob_start();
					echo '<div class="clr">';
					if ($query->have_posts()) :
						$i = 1;
						$col = ($args['related_columns'] === 's6') ? 2 : (($args['related_columns'] === 's4') ? 3 : 4);
						while ($query->have_posts()) : $query->the_post();
							$cats = (!$cpt || $cpt === '' || $cpt === 'post') ? 'category' : $cpt . '_cat';
						?>
					<article id="post-<?php the_ID(); ?>" class="cz_related_post col <?php echo esc_attr($args['related_columns']); ?>">
						<div>
							<?php

							// Post hover icon
							if (self::contains(self::option('hover_icon_' . $cpt), ['image', 'imhoh', 'iasi'])) {
								$post_hover_icon = '<i class="cz_post_icon"><img src="' . self::option('hover_icon_image_' . $cpt) . '" /></i>';
							} else if (self::option('hover_icon_' . $cpt) === 'none') {
								$post_hover_icon = '';
							} else {
								$post_hover_icon = '<i class="cz_post_icon ' . self::option('hover_icon_icon_' . $cpt, 'fa czico-109-link-symbol-1') . '" aria-hidden="true"></i>';
							}

							if (has_post_thumbnail()) { ?>
								<a class="cz_post_image" href="<?php echo esc_url(get_the_permalink()); ?>">
									<?php
									the_post_thumbnail('codevz_360_320');
									echo wp_kses_post($post_hover_icon);
									?>
								</a>
							<?php } ?>
							<a class="cz_post_title mt10 block" href="<?php echo esc_url(get_the_permalink()); ?>">
								<h3><?php the_title(); ?></h3>
							</a>
							<?php
							$cats = get_the_term_list(get_the_id(), $cats, '<small class="cz_related_post_date mt10"><i class="fa fa-folder-open mr10" aria-hidden="true"></i>', ', ', '</small>');
							if (!is_wp_error($cats)) {
								echo wp_kses_post($cats);
							}
							?>
						</div>
					</article>
	<?php
							if ($i % $col === 0) {
								echo '</div><div class="clr">';
							}

							$i++;
						endwhile;
					endif;
					echo '</div>';
					wp_reset_postdata();

					$related = ob_get_clean();

					if ($related && $related !== '<div class="clr" aria-hidden="true"></div>') {

						if (self::$preview) {
							if ($cpt === 'post') {
								$related .= '<i class="xtra-section-focus fas fa-cog" data-section="single_settings" aria-hidden="true"></i>';
								$related .= '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="single_styles" aria-hidden="true"></i>';
							} else {
								$related .= '<i class="xtra-section-focus fas fa-cog" data-section="' . esc_attr($cpt) . '_single_settings" aria-hidden="true"></i>';
								$related .= '<i class="xtra-section-focus xtra-section-focus-second fas fa-paint-brush" data-section="' . esc_attr($cpt) . '_single_styles" aria-hidden="true"></i>';
							}
						}

						echo '</div><div class="content cz_related_posts clr"><h4>' . esc_html($args['section_title']) . '</h4>';
						echo do_shortcode($related);
					}
				}

				/**
				 * Get string between two string
				 * 
				 * @return string
				 */
				public static function get_string_between($c = '', $s = '', $e = '', $m = false)
				{
					if ($c) {
						if ($m) {
							preg_match_all('~' . preg_quote($s, '~') . '(.*?)' . preg_quote($e, '~') . '~s', $c, $matches);
							return $matches[0];
						}

						$r = explode($s, $c);
						if (isset($r[1])) {
							$r = explode($e, $r[1]);
							return $r[0];
						}
					}

					return;
				}

				/**
				 * Check if string contains specific value(s)
				 * 
				 * @return string
				 */
				public static function contains($v = '', $a = [])
				{
					if ($v) {
						foreach ((array) $a as $k) {
							if ($k && strpos((string) $v, (string) $k) !== false) {
								return 1;
								break;
							}
						}
					}

					return null;
				}

				/**
				 * Get current page title
				 * 
				 * @return string
				 */
				public static function page_title($tag = 'h3', $class = '')
				{

					if (is_404()) {
						$i = '404';
					} else if (is_search()) {
						$i = do_shortcode(self::option('search_title_prefix', 'Search result for:')) . ' ' . get_search_query();
					} else if (is_post_type_archive()) {
						ob_start();
						post_type_archive_title();
						$i = ob_get_clean();
					} else if (is_archive()) {
						$i = get_the_archive_title();
						if (self::contains($i, ':')) {
							$i = substr($i, strpos($i, ': ') + 1);
						}
					} else if (is_single()) {
						//$i = single_post_title( '', false );
						//$i = $i ? $i : get_the_title();
						$i = get_the_title();
					} else if (is_home()) {
						$i = get_option('page_for_posts') ? get_the_title(get_option('page_for_posts')) : get_bloginfo('name');
					} else {
						$i = get_the_title();
					}

					echo '<' . esc_attr($tag) . ' class="section_title ' . esc_attr($class) . '">' . do_shortcode(wp_kses_post($i)) . '</' . esc_attr($tag) . '>';
				}

				/**
				 * Get author box
				 * 
				 * @return string
				 */
				public static function author_box()
				{
					return get_the_author_meta('description') ? '<div class="clr"><div class="lefter mr20 mt10">' . get_avatar(get_the_author_meta('user_email'), '100') . '</div><p>' . get_the_author_meta('description') . '</p></div>' : '';
				}

				/**
				 * Get breadcrumbs
				 * 
				 * @return string
				 */
				public static function breadcrumbs($is_right = '')
				{

					if (is_front_page()) {
						return;
					}

					$out = [];
					$bc = (array) self::breadcrumbs_array();
					$count = count($bc);
					$i = 1;

					if (self::option('page_title_hide_breadcrumbs') && $count < 3) {
						return;
					}

					foreach ($bc as $ancestor) {

						if ($i === $count) {
							global $wp;
							$out[] = '<b itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="inactive_l"><a class="cz_br_current" href="' . self::$home_url . $wp->request . '" onclick="return false;" itemprop="url"><span itemprop="title">' . wp_kses_post($ancestor['title']) . '</span></a></b>';
						} else {
							$out[] = '<b itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . esc_url($ancestor['link']) . '" itemprop="url"' . (isset($ancestor['home']) ? ' title="' . esc_attr(Xtra_Strings::get('homepage')) . '"' : '') . '><span itemprop="title">' . wp_kses_post($ancestor['title']) . '</span></a></b>';
						}

						$i++;
					}

					do_action('xtra/before_breadcrumbs');

					echo '<div class="breadcrumbs clr' . esc_attr($is_right) . '">';
					$separator = self::option('breadcrumbs_separator', 'fa fa-long-arrow-right');
					$separator = self::$is_rtl ? str_replace('-right', '-left', $separator) : $separator;
					echo wp_kses_post(implode(' <i class="' . esc_attr($separator) . '" aria-hidden="true"></i> ', $out));
					echo '</div>';

					do_action('xtra/after_breadcrumbs');
				}

				public static function breadcrumbs_array()
				{
					global $post;

					$bc = [];
					$bc[] = ['home' => true, 'title' => '<i class="fa fa-home cz_breadcrumbs_home" aria-hidden="true"></i>', 'link' => self::$home_url];
					$bc = self::add_posts_page_array($bc);
					if (is_404()) {
						$bc[] = ['title' => '404', 'link' => false];
					} else if (is_search()) {
						$bc[] = ['title' => get_search_query(), 'link' => false];
					} else if (is_tax()) {
						$taxonomy = get_query_var('taxonomy');
						$term = get_term_by('slug', get_query_var('term'), $taxonomy);

						if (!empty($term->taxonomy) && get_taxonomy($term->taxonomy)) {
							$ptn = get_taxonomy($term->taxonomy)->object_type[0];
							$label = get_post_type_object($ptn);
							$label = empty($label->label) ? $ptn : $label->label;
							$bc[] = ['title' => ucwords($label), 'link' => get_post_type_archive_link($ptn)];
						}

						if (!empty($term->parent)) {
							$parent = get_term_by('term_id', $term->parent, $taxonomy);
							$bc[] = ['title' => sprintf('%s', $parent->name), 'link' => get_term_link($parent->term_id, $taxonomy)];
						}

						if (!empty($term->name) && !empty($term->term_id)) {
							$bc[] = ['title' => sprintf('%s', $term->name), 'link' => get_term_link($term->term_id, $taxonomy)];
						}
					} else if (is_attachment()) {
						if ($post->post_parent) {
							$parent_post = get_post($post->post_parent);
							if ($parent_post) {
								$singular_bread_crumb_arr = self::singular_breadcrumbs_array($parent_post);
								$bc = array_merge($bc, $singular_bread_crumb_arr);
							}
						}
						if (isset($parent_post->post_title)) {
							$bc[] = ['title' => $parent_post->post_title, 'link' => get_permalink($parent_post->ID)];
						}
						$bc[] = ['title' => sprintf('%s', $post->post_title), 'link' => get_permalink($post->ID)];
					} else if ((is_singular() || is_single()) && !is_front_page()) {
						$singular_bread_crumb_arr = self::singular_breadcrumbs_array($post);
						$bc = array_merge($bc, $singular_bread_crumb_arr);
						$bc[] = ['title' => $post->post_title, 'link' => get_permalink($post->ID)];
					} else if (is_category()) {
						global $cat;

						$category = get_category($cat);
						if ($category->parent != 0) {
							$ancestors = array_reverse(get_ancestors($category->term_id, 'category'));
							foreach ($ancestors as $ancestor_id) {
								$ancestor = get_category($ancestor_id);
								$bc[] = ['title' => $ancestor->name, 'link' => get_category_link($ancestor->term_id)];
							}
						}
						$bc[] = ['title' => sprintf('%s', $category->name), 'link' => get_category_link($cat)];
					} else if (is_tag()) {
						global $tag_id;
						$tag = get_tag($tag_id);
						$bc[] = ['title' => sprintf('%s', $tag->name), 'link' => get_tag_link($tag_id)];
					} else if (is_author()) {
						$author = get_query_var('author');
						$bc[] = ['title' => sprintf('%s', get_the_author_meta('display_name', get_query_var('author'))), 'link' => get_author_posts_url($author)];
					} else if (is_day()) {
						$m = get_query_var('m');
						if ($m) {
							$year = substr($m, 0, 4);
							$month = substr($m, 4, 2);
							$day = substr($m, 6, 2);
						} else {
							$year = get_query_var('year');
							$month = get_query_var('monthnum');
							$day = get_query_var('day');
						}
						$month_title = self::get_month_title($month);
						$bc[] = ['title' => sprintf('%s', $year), 'link' => get_year_link($year)];
						$bc[] = ['title' => sprintf('%s', $month_title), 'link' => get_month_link($year, $month)];
						$bc[] = ['title' => sprintf('%s', $day), 'link' => get_day_link($year, $month, $day)];
					} else if (is_month()) {
						$m = get_query_var('m');
						if ($m) {
							$year = substr($m, 0, 4);
							$month = substr($m, 4, 2);
						} else {
							$year = get_query_var('year');
							$month = get_query_var('monthnum');
						}
						$month_title = self::get_month_title($month);
						$bc[] = ['title' => sprintf('%s', $year), 'link' => get_year_link($year)];
						$bc[] = ['title' => sprintf('%s', $month_title), 'link' => get_month_link($year, $month)];
					} else if (is_year()) {
						$m = get_query_var('m');
						if ($m) {
							$year = substr($m, 0, 4);
						} else {
							$year = get_query_var('year');
						}
						$bc[] = ['title' => sprintf('%s', $year), 'link' => get_year_link($year)];
					} else if (is_post_type_archive()) {
						$post_type = get_post_type_object(get_query_var('post_type'));
						$bc[] = ['title' => sprintf('%s', $post_type->label), 'link' => get_post_type_archive_link($post_type->name)];
					}

					return $bc;
				}

				public static function singular_breadcrumbs_array($post)
				{
					$bc = [];
					$post_type = get_post_type_object($post->post_type);

					if ($post_type && $post_type->has_archive) {
						if ($post_type->name === 'topic') {
							$ppt = get_post_type_object('forum');
							$bc[] = ['title' => sprintf('%s', $ppt->label), 'link' => get_post_type_archive_link($ppt->name)];
						}
						$bc[] = ['title' => sprintf('%s', $post_type->label), 'link' => get_post_type_archive_link($post_type->name)];
					}

					if (is_post_type_hierarchical($post_type->name)) {
						$ancestors = array_reverse(get_post_ancestors($post));
						if (count($ancestors)) {
							$ancestor_posts = get_posts('post_type=' . $post_type->name . '&include=' . implode(',', $ancestors));
							foreach ((array) $ancestors as $ancestor) {
								foreach ((array) $ancestor_posts as $ancestor_post) {
									if ($ancestor === $ancestor_post->ID) {
										$bc[] = ['title' => $ancestor_post->post_title, 'link' => get_permalink($ancestor_post->ID)];
									}
								}
							}
						}
					} else {
						$post_type_taxonomies = get_object_taxonomies($post_type->name, true);
						if (is_array($post_type_taxonomies) && count($post_type_taxonomies)) {
							foreach ($post_type_taxonomies as $tax_slug => $taxonomy) {
								if ($taxonomy->hierarchical && $tax_slug !== 'post_tag') {

									if ($post_type && $post_type->name === 'product' && is_single()) {
										$tax_slug = 'product_cat';
									}

									$terms = get_the_terms(self::$post->ID, $tax_slug);
									if ($terms) {
										$term = array_shift($terms);
										if ($term->parent != 0) {
											$ancestors = array_reverse(get_ancestors($term->term_id, $tax_slug));
											foreach ($ancestors as $ancestor_id) {
												$ancestor = get_term($ancestor_id, $tax_slug);
												$bc[] = ['title' => $ancestor->name, 'link' => get_term_link($ancestor, $tax_slug)];
											}
										}
										$bc[] = ['title' => $term->name, 'link' => get_term_link($term, $tax_slug)];

										foreach ($terms as $t) {
											if ($term->term_id == $t->parent) {
												$bc[] = ['title' => $t->name, 'link' => get_term_link($t, $tax_slug)];
												break;
											}
										}
										break;
									}
								}
							}
						}
					}

					return $bc;
				}

				public static function add_posts_page_array($bc)
				{
					if (is_page() || is_front_page() || is_author() || is_date()) {
						return $bc;
					} else if (is_category()) {
						$tax = get_taxonomy('category');
						if (count($tax->object_type) != 1 || $tax->object_type[0] != 'post') {
							return $bc;
						}
					} else if (is_tag()) {

						$tax = get_taxonomy('post_tag');

						if (count($tax->object_type) != 1 || $tax->object_type[0] != 'post') {

							if (isset($_GET['post_type'])) {

								$type = sanitize_text_field(wp_unslash($_GET['post_type']));

								$bc[] = ['title' => get_post_type_object($type)->labels->name, 'link' => get_post_type_archive_link($type)];
							}

							return $bc;
						}
					} else if (is_tax()) {
						$tax = get_taxonomy(get_query_var('taxonomy'));
						if (count($tax->object_type) != 1 || $tax->object_type[0] != 'post') {
							return $bc;
						}
					} else if (is_home() && !get_query_var('pagename')) {
						return $bc;
					} else {
						$post_type = get_query_var('post_type') ? get_query_var('post_type') : 'post';
						if ($post_type != 'post') {
							return $bc;
						}
					}
					if (get_option('show_on_front') === 'page' && get_option('page_for_posts') && !is_404()) {
						$posts_page = get_post(get_option('page_for_posts'));
						$bc[] = ['title' => $posts_page->post_title, 'link' => get_permalink($posts_page->ID)];
					}

					return $bc;
				}

				public static function get_month_title($monthnum = 0)
				{
					global $wp_locale;
					$monthnum = (int) $monthnum;
					$date_format = get_option('date_format');
					if (in_array($date_format, ['DATE_COOKIE', 'DATE_RFC822', 'DATE_RFC850', 'DATE_RFC1036', 'DATE_RFC1123', 'DATE_RFC2822', 'DATE_RSS'])) {
						$month_format = 'M';
					} else if (in_array($date_format, ['DATE_ATOM', 'DATE_ISO8601', 'DATE_RFC3339', 'DATE_W3C'])) {
						$month_format = 'm';
					} else {
						preg_match('/(^|[^\\\\]+)(F|m|M|n)/', str_replace('\\\\', '', $date_format), $m);
						$month_format = empty($m[2]) ? 'F' : $m[2];
					}

					if ($month_format === 'F') {
						return $wp_locale->get_month($monthnum);
					} else if ($month_format === 'M') {
						return $wp_locale->get_month_abbrev($wp_locale->get_month($monthnum));
					} else {
						return $monthnum;
					}
				}
			}

			// Run theme class.
			Xtra_Theme::instance();
		}



		function is_page_new_shop()
		{
			if (!is_page('NEW SHOP')) {
				return false;
			}
	?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/sms_main.css">
	<link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/sms_app.css">
	<link rel="prefetch" type="text/css" href="<?php echo get_template_directory_uri() ?>/assets/css/sms_daterangepicker.css">
	<script type="text/javascript" async="" src="<?php echo get_template_directory_uri() ?>/assets/js/analytics.js"></script>

	<style>
		.c-collapse[data-v-44775fba] {
			border: 1px solid #ededed
		}

		.c-collapse__header[data-v-44775fba] {
			align-items: center;
			border-left: 5px solid #4a7596;
			cursor: pointer;
			display: flex;
			font-weight: 700;
			justify-content: flex-start;
			padding: 15px 10px
		}

		.c-collapse__content[data-v-44775fba] {
			padding: 10px 20px 15px
		}

		.cc-header[data-v-44775fba] {
			display: flex;
			gap: 10px
		}

		.cc-header__icon[data-v-44775fba] {
			width: 15px
		}

		@media (max-width:768px) {
			.cc-header__icon--after[data-v-44775fba] {
				margin-left: auto
			}
		}

		.c-collapse.is-show>.cc-header>.cc-header__icon[data-v-44775fba] {
			transform: rotate(180deg)
		}
	</style>
	<style>
		.preloader {
			align-items: center;
			background: rgba(0, 0, 0, .9);
			display: flex;
			font-family: Roboto;
			font-size: 2.5vw;
			height: 100vh;
			justify-content: center;
			left: 0;
			opacity: 0;
			position: fixed;
			text-align: center;
			top: 0;
			transition: opacity .4s ease;
			visibility: hidden;
			width: 100vw;
			z-index: 9999
		}

		.preloader__wrapper {
			line-height: 1.2;
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none
		}

		.preloader__title {
			-webkit-text-stroke: .03em #f4f4f4;
			color: transparent;
			display: inline-block;
			font-size: .9684210526em;
			letter-spacing: 13%;
			line-height: 1.1714285714em;
			position: relative;
			text-transform: uppercase;
			white-space: nowrap
		}

		.preloader__animation {
			-webkit-text-storke: 0 transparent;
			background: transparent;
			color: #fff;
			height: 100%;
			left: 0;
			overflow-x: hidden;
			overflow-y: hidden;
			position: absolute;
			top: 0;
			white-space: nowrap;
			width: 100%
		}

		.preloader__slogan {
			color: #91939a;
			display: block;
			font-size: .4em;
			position: relative;
			transform: translateY(-15%);
			white-space: nowrap
		}

		._loaded {
			overflow-x: hidden;
			overflow-y: hidden
		}

		._loaded .preloader {
			opacity: 1;
			visibility: visible
		}

		@media screen and (max-width:1000px) {
			._loaded .preloader {
				font-size: 4vw
			}
		}

		@media screen and (max-width:500px) {
			._loaded .preloader {
				font-size: 7vw
			}
		}

		.preloader__wrapper {
			flex: 0 1 auto;
			padding-left: 1.2em;
			text-align: left
		}

		.preloader__title img {
			display: inline-block;
			height: 1em;
			left: -1.2em;
			opacity: 1;
			position: absolute;
			top: .24em;
			width: auto
		}

		._loaded .preloader__animation {
			-webkit-animation: loading-animation 1s linear infinite;
			animation: loading-animation 1s linear infinite
		}

		._clip .preloader__animation {
			background: #fff;
			-webkit-background-clip: text;
			background-clip: text
		}

		@-webkit-keyframes loading-animation {
			0% {
				width: 0
			}

			40% {
				width: 60%
			}

			90% {
				width: 95%
			}

			to {
				width: 100%
			}
		}

		@keyframes loading-animation {
			0% {
				width: 0
			}

			40% {
				width: 60%
			}

			90% {
				width: 95%
			}

			to {
				width: 100%
			}
		}
	</style>
	<style>
		.c-loader-line[data-v-4671add6] {
			height: 2px;
			overflow-x: hidden;
			position: absolute;
			width: 100%
		}

		.c-loader-line.bottom[data-v-4671add6] {
			bottom: 0
		}

		.c-loader-line.top[data-v-4671add6] {
			top: 0
		}

		.c-loader-line[data-v-4671add6]:before {
			-webkit-animation: lider-4671add6 .75s linear infinite;
			animation: lider-4671add6 .75s linear infinite;
			background: linear-gradient(266.81deg, #e2c299 5.64%, #c5a67c 94.29%);
			bottom: 0;
			content: "";
			display: block;
			left: 0;
			position: absolute;
			top: 0;
			width: 25%
		}

		@-webkit-keyframes lider-4671add6 {
			0% {
				left: -25%
			}

			to {
				left: 125%
			}
		}

		@keyframes lider-4671add6 {
			0% {
				left: -25%
			}

			to {
				left: 125%
			}
		}
	</style>
	<style>
		.c-modal[data-v-061c94dc] {
			align-content: center;
			background-color: rgba(0, 0, 0, .5);
			bottom: 0;
			display: grid;
			justify-content: center;
			left: 0;
			position: fixed;
			right: 0;
			top: 0;
			z-index: 999
		}

		.c-modal__absolute[data-v-061c94dc] {
			border-radius: 10px;
			margin: -30px -20px;
			position: absolute;
			z-index: 100
		}

		.c-modal__container[data-v-061c94dc] {
			background-color: #fff;
			border-radius: 10px;
			box-shadow: 0 2px 3px 0 rgba(0, 0, 0, .6);
			margin: 0 15px;
			max-width: 530px;
			min-width: 530px;
			padding: 30px;
			position: relative
		}

		@media (max-width:580px) {
			.c-modal__container[data-v-061c94dc] {
				min-width: auto;
				overflow-x: auto
			}

			.c-modal__absolute .c-modal__container[data-v-061c94dc] {
				margin-left: 30px;
				margin-right: 30px
			}
		}

		.c-modal__close[data-v-061c94dc] {
			cursor: pointer;
			position: absolute;
			right: 30px;
			top: 30px
		}

		.c-modal__close svg[data-v-061c94dc] {
			fill: rgba(0, 0, 0, .3)
		}

		.c-modal__close:hover svg[data-v-061c94dc] {
			fill: rgba(0, 0, 0, .5)
		}

		.c-modal__header[data-v-061c94dc] {
			color: #505565;
			font-size: 23px;
			font-weight: 600;
			line-height: 140%;
			margin-top: -5px
		}

		.c-modal__content[data-v-061c94dc],
		.c-modal__header[data-v-061c94dc] {
			font-family: Roboto;
			font-style: normal
		}

		.c-modal__content[data-v-061c94dc] {
			color: #1a1a1a;
			font-size: 17px;
			font-weight: 300;
			line-height: 160%
		}

		.c-modal__content[data-v-061c94dc],
		.c-modal__header[data-v-061c94dc] {
			margin-right: 20px
		}

		.c-modal__container>.c-modal__header[data-v-061c94dc] {
			margin-bottom: 15px
		}

		.c-modal__container>.c-modal__footer[data-v-061c94dc] {
			margin-top: 30px
		}
	</style>
	<style>
		.c-progress[data-v-7f6a0b58] {
			background-color: #ebcba2;
			border: 1px solid #c5a77c;
			border-radius: 2px;
			height: 25px;
			margin: 10px 0;
			overflow: hidden;
			position: relative
		}

		.c-progress__bar[data-v-7f6a0b58] {
			background-color: #fff;
			bottom: 0;
			left: 0;
			position: absolute;
			right: 0;
			top: 0;
			z-index: 1
		}

		.c-progress__bar-animation[data-v-7f6a0b58] {
			transition: transform .25s linear
		}

		.c-progress__label[data-v-7f6a0b58] {
			color: #b98b4d;
			font-family: Roboto;
			font-size: 14px;
			font-style: normal;
			font-weight: 300;
			left: calc(50% - 11px);
			line-height: 160%;
			position: absolute;
			top: calc(50% - 11.5px);
			z-index: 2
		}
	</style>
	<style>
		.c-switcher[data-v-b94f6378] {
			height: 24px;
			width: 48px
		}

		.c-switcher-control[data-v-b94f6378] {
			display: none
		}

		.c-switcher-label[data-v-b94f6378] {
			cursor: pointer;
			display: inline-block;
			font-size: 1.2rem;
			padding-top: 40px;
			position: relative
		}

		.c-switcher-label[data-v-b94f6378]:after,
		.c-switcher-label[data-v-b94f6378]:before {
			content: "";
			position: absolute;
			transition: background .15s ease-in-out, left .15s ease-in-out
		}

		.c-switcher-label[data-v-b94f6378]:before {
			background: linear-gradient(260deg, #a1a4b1, #858893);
			border-radius: 24px;
			height: 24px;
			left: 0;
			top: 0;
			width: 48px
		}

		.c-switcher-control:checked+.c-switcher-label[data-v-b94f6378]:before {
			background: linear-gradient(260deg, #e2c299, #c5a67c)
		}

		.c-switcher-label[data-v-b94f6378]:after {
			background-color: #fff;
			border-radius: 50%;
			height: 16px;
			left: 4px;
			top: 4px;
			width: 16px
		}

		.c-switcher-control:checked+.c-switcher-label[data-v-b94f6378]:after {
			left: 28px
		}
	</style>
	<style>
		.settings-dropdown-item[data-v-1637fd3e] {
			position: relative
		}

		.settings-dropdown-item a[data-v-1637fd3e]:before {
			background: url(<?php echo get_template_directory_uri() ?>/assets/img/sms//icons/cog.svg) 50% no-repeat;
			background-size: cover;
			content: "";
			display: block;
			height: 20px;
			margin-right: 5px;
			width: 20px
		}

		.settings-dropdown-item--show a[data-v-1637fd3e] {
			border-bottom: 0 !important;
			border-left: 1px solid #dfdfdf;
			border-right: 1px solid #dfdfdf
		}

		.settings-dropdown-content[data-v-1637fd3e] {
			background-color: #fff;
			border: 1px solid #dfdfdf;
			border-bottom-left-radius: 4px;
			border-bottom-right-radius: 4px;
			border-top: 0;
			bottom: 0;
			box-shadow: -1px 3px 4px rgba(0, 0, 0, .1);
			list-style-type: none;
			min-width: 250px;
			opacity: 0;
			position: absolute;
			right: 0;
			transform: translateY(120%);
			transition: transform 75ms ease-in, opacity 75ms ease-in;
			visibility: hidden;
			z-index: 99
		}

		.settings-dropdown-content__item[data-v-1637fd3e] {
			display: flex;
			flex-direction: row;
			padding: 10px 20px
		}

		.settings-dropdown-content__item+.settings-dropdown-content__item[data-v-1637fd3e] {
			border-top: 1px solid #dfdfdf
		}

		.settings-dropdown-content__label[data-v-1637fd3e] {
			flex: 1
		}

		.settings-dropdown-item--show .settings-dropdown-content[data-v-1637fd3e] {
			opacity: 1;
			transform: translateY(100%);
			transition: transform 75ms ease-out, opacity 75ms ease-out;
			visibility: visible
		}
	</style>
	<style>
		.limitation {
			margin-bottom: 40px
		}

		.limitation .container {
			max-width: 100% !important;
			padding: 0 !important
		}

		.limitation_wrap {
			background: #fff;
			border-radius: 10px;
			box-shadow: 0 5px 15px rgba(0, 0, 0, .1)
		}

		.limitation_wrap .send_limit {
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;
			background: linear-gradient(266.81deg, #e2c299 5.64%, #c5a67c 94.29%);
			border: none;
			border-radius: 10px;
			color: #fff;
			cursor: pointer;
			font: 700 1rem Roboto;
			outline: none;
			padding: 15px
		}

		.limitation_wrap .send_limit:hover {
			background: #e2c299
		}

		.limitation_wrap .send_limit:is(:disabled) {
			background: #e2c299;
			cursor: not-allowed
		}

		.limitation_wrap .limitation_form {
			grid-gap: 15px;
			align-items: end;
			display: grid;
			grid-template-columns: 255px repeat(2, 220px) 100px 1fr;
			margin-bottom: 15px;
			padding: 15px;
			position: relative
		}

		@media (max-width:990px) {
			.limitation_wrap .limitation_form {
				grid-gap: 15px;
				grid-template-columns: 1fr
			}
		}

		.limitation_wrap .limitation_form .limitation_select {
			position: relative
		}

		.limitation_wrap .limitation_form .limitation_input p,
		.limitation_wrap .limitation_form .limitation_select p {
			font: 700 1rem Roboto;
			margin: 0 0 10px
		}

		.limitation_wrap .limitation_form .limitation_input input,
		.limitation_wrap .limitation_form .limitation_select select {
			border: 2px solid #e8eaf1;
			box-sizing: border-box;
			cursor: pointer;
			font: 400 1rem Roboto;
			outline: none;
			padding: 5px;
			width: 100%
		}

		.limitation_wrap .limitation_form .limitation_input input {
			border-color: #e8eaf1;
			cursor: auto
		}

		.limitation_list {
			position: relative
		}

		.limitation_list .limitation_title {
			grid-gap: 10px;
			background: #fafafc;
			display: grid;
			grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
			padding: 15px 20px
		}

		.limitation_list .limitation_title .limitation_title_country {
			text-align: left
		}

		@media (max-width:990px) {
			.limitation_list .limitation_title {
				display: none
			}
		}

		.limitation_list .limitation_title p {
			color: #000;
			font: 700 1rem Roboto;
			text-align: center
		}

		.limitation_item_container .limitation_item_wrap {
			grid-gap: 10px;
			display: grid;
			grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
			padding: 15px 20px;
			position: relative
		}

		.limitation_item_container .highlight {
			-webkit-animation: highlight .8s;
			animation: highlight .8s;
			-webkit-animation-iteration-count: 3;
			animation-iteration-count: 3
		}

		@-webkit-keyframes highlight {
			0% {
				background: transparent
			}

			50% {
				background: #f0e6db
			}

			to {
				background: transparent
			}
		}

		@keyframes highlight {
			0% {
				background: transparent
			}

			50% {
				background: #f0e6db
			}

			to {
				background: transparent
			}
		}

		@media (max-width:990px) {
			.limitation_item_container .limitation_item_wrap {
				grid-gap: 0;
				display: grid;
				grid-template-columns: 1fr
			}
		}

		.limitation_item_container .limitation_item_wrap .limitation_item_box {
			align-items: center;
			display: flex;
			justify-content: center
		}

		.limitation_item_container .limitation_item_wrap .limitation_item_box.limitation_item_box_country {
			justify-content: flex-start
		}

		@media (max-width:990px) {
			.limitation_item_container .limitation_item_wrap .limitation_item_box {
				border-bottom: 1px solid gray;
				justify-content: flex-end;
				margin-bottom: 10px;
				padding-bottom: 10px;
				position: relative
			}

			.limitation_item_container .limitation_item_wrap .limitation_item_box.limitation_item_box_country {
				justify-content: flex-end
			}
		}

		.limitation_item_container .limitation_item_wrap .limitation_item_box:after {
			content: attr(data-label);
			display: none
		}

		@media (max-width:990px) {
			.limitation_item_container .limitation_item_wrap .limitation_item_box:after {
				display: block;
				left: 0;
				position: absolute
			}
		}

		.limitation_item_container .limitation_item_wrap .limitation_item_box img {
			margin-right: 10px
		}

		.limitation_item_container .limitation_item_wrap .limitation_item_box p {
			color: #000;
			font: 400 1rem Roboto;
			margin: 0
		}

		.delete_item path {
			fill: #000
		}

		.delete_item:hover path {
			fill: #c5a67c
		}
	</style>
	<style id="smooth-scrollbar-style">
		[data-scrollbar] {
			display: block;
			position: relative;
		}

		.scroll-content {
			display: flow-root;
			-webkit-transform: translate3d(0, 0, 0);
			transform: translate3d(0, 0, 0);
		}

		.scrollbar-track {
			position: absolute;
			opacity: 0;
			z-index: 1;
			background: rgba(222, 222, 222, .75);
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
			-webkit-transition: opacity 0.5s 0.5s ease-out;
			transition: opacity 0.5s 0.5s ease-out;
		}

		.scrollbar-track.show,
		.scrollbar-track:hover {
			opacity: 1;
			-webkit-transition-delay: 0s;
			transition-delay: 0s;
		}

		.scrollbar-track-x {
			bottom: 0;
			left: 0;
			width: 100%;
			height: 8px;
		}

		.scrollbar-track-y {
			top: 0;
			right: 0;
			width: 8px;
			height: 100%;
		}

		.scrollbar-thumb {
			position: absolute;
			top: 0;
			left: 0;
			width: 8px;
			height: 8px;
			background: rgba(0, 0, 0, .5);
			border-radius: 4px;
		}

		.main {
			padding-top: 0px
		}

		.main .simki .country-box:hover {
			background: linear-gradient(266.81deg, #0000cda8 5.64%, #0000cd 94.29%);
			box-shadow: 0 5px 10px rgba(0, 0, 0, .22);
			color: #fff;
		}

		.active-operator,
		.show-operator:hover,
		.specify-box:hover {
			background: #0000cd;
			color: #fff;
		}

		.main .simki .active-country {
			background: #0000cd;
			box-shadow: 0 5px 10px rgba(0, 0, 0, .22);
			color: #fff;
		}

		.steps-box .icon:after {
			background-image: url(<?php echo get_template_directory_uri() ?>/assets/img/sms/human.svg), linear-gradient(266.81deg, #0000cd 5.64%, #0000cd 94.29%);

		}

		.steps-box .icon.location:after {
			background-image: url(<?php echo get_template_directory_uri() ?>/assets/img/sms/checkmap.svg), linear-gradient(266.81deg, #0000cd 5.64%, #0000cd 94.29%);
		}

		.steps-box .icon.servic:after {
			background-image: url(<?php echo get_template_directory_uri() ?>/assets/img/sms/service.svg), linear-gradient(266.81deg, #0000cd 5.64%, #0000cd 94.29%);
		}

		.steps-box .icon.sms:after {
			background-image: url(<?php echo get_template_directory_uri() ?>/assets/img/sms/sms-send.svg), linear-gradient(266.81deg, #0000cd 5.64%, #0000cd 94.29%);
		}

		.main .simki .service-box:hover .cost {
			background: linear-gradient(266.81deg, #0000cd 5.64%, #0000cd 94.29%);
			color: #fff;
		}

		.main .simki .simki-container .nav-pills .active {
			border-bottom: 1px solid #0000cd;
		}

		.collapse-faq__header {
			border-left-color: #0000cd !important;
		}
	</style>

<?php
		}
		add_action('wp_head', 'is_page_new_shop', 1);

		function send_post($url, $post_data)
		{

			$postdata = http_build_query($post_data);
			$options = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type:application/x-www-form-urlencoded',
					'content' => $postdata,
					'timeout' => 15 * 60 // 超时时间（单位:s）
				)
			);
			$context = stream_context_create($options);
			$result = file_get_contents($url, false, $context);

			return $result;
		}

		function request_by_curl($remote_server, $post_string)
		{

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $remote_server);

			curl_setopt($ch, CURLOPT_POSTFIELDS, 'mypost=' . $post_string);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_USERAGENT, "ireceivesms.online's CURL Example beta");

			$data = curl_exec($ch);

			curl_close($ch);



			return $data;
		}



		function post($url, $post_data = '', $timeout = 5)
		{ //curl

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_POST, 1);

			if ($post_data != '') {

				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			curl_setopt($ch, CURLOPT_HEADER, false);

			$file_contents = curl_exec($ch);

			curl_close($ch);

			return $file_contents;
		}

		function is_sms()
		{
			if (is_page('NEW SHOP')) {
				echo ' class="_loaded"';
			}
		}


		function sms_js()
		{

?>
	<script src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js">
	</script>
	<script>
		$(function() {

			if ($('.service-box.is-serached').length > 0) {
				document.documentElement.classList.remove("_loaded")
			}
			$(document).on("click", ".specify-box", function(obj) {
				document.documentElement.classList.add("_loaded")
				$('.active-operator').removeClass('active-operator')
				$(this).addClass('active-operator')
				$.ajax({
					url: "<?php echo admin_url('admin-ajax.php') ?>",
					type: "post",
					dataType: "json",
					data: {
						action: "get_operator_service_data",
						operator: $(this).children('.specify-text').text(),
						country: $('.active-country').text(),
					},
					success: function(t) {

						$('.js-service-not-repeat-content').html(t)

						document.documentElement.classList.remove("_loaded")
					},
					error: function(t) {
						console.log(t)
					},
				})
			})

			$(document).on("click", ".input-service", function() {
				if ($('.scroll-content').children('.block').length > 9) {
					event.preventDefault();
					alert('Exceeding the maximum quantity')
					return false
				}
				document.documentElement.classList.add("_loaded")

				$.ajax({
					url: "<?php echo admin_url('admin-ajax.php') ?>",
					type: "post",
					dataType: "json",
					data: {
						action: "get_number",
						operator: $(".active-operator").length > 0 ? $(".active-operator").children('.specify-text').text() : 'any',
						country: $('.active-country').text(),
						service: $(this).parent().data('id'),

					},
					success: function(t) {
						if (!t.message) {



							$('.section-numbers').css('display', '');
							$('.scroll-content').append(t);
							$("html, body").animate({
								scrollTop: $('.section-numbers').offset().top + "px"
							}, {
								duration: 1000,
								easing: "swing"
							});
							check_number_code()

						} else {
							alert(t.message)
						}
						document.documentElement.classList.remove("_loaded")
					},
					error: function(t) {
						console.log(t)
					},
				})
			})

			function check_number_code(event) {
				if ($('.scroll-content').children('.block').length > 9) {
					event.preventDefault();
					alert('Exceeding the maximum quantity')
					return false
				}
				if ($('.scroll-content').children('.block').length > 0) {
					var e = setInterval(function() {
							$('.scroll-content').children('.block').each(function(index, value) {
								if (!$(this).find('.spinner').length) {
									return true;
								}

								if ($(this).find('.block__code--animation').length > 0) {

									$.ajax({
										url: "<?php echo admin_url('admin-ajax.php') ?>",
										type: "post",
										dataType: "json",
										data: {
											action: "get_number_code",
											id: $(this).data('number-id')

										},
										success: function(t) {
											if (1 == t.status) {
												console.log($(this).find('.block__code--animation').length, t.status)
												$('.block__code--animation').eq(index).html(t.status)
												if (index + 1 == $('.scroll-content').children('.block').length) {
													clearInterval(e)
													document.documentElement.classList.remove("_loaded")
												}
											}

										},
										error: function(t) {

											console.log(t)
										},
									})

								}

							})
						},
						5e3)
				}
			}
		})

		$('.country-box').click(function(e) {
			document.documentElement.classList.add("_loaded")
			console.log($(this).find('p').text())
			$('.active-country').removeClass('active-country');
			$(this).addClass('active-country');
			$.ajax({
				url: "<?php echo admin_url('admin-ajax.php') ?>",
				type: "post",
				dataType: "json",
				data: {
					action: "base_data",
					country: $(this).find('p').text(),
				},
				success: function(t) {

					$('.operators-list ').html(t.OperatorsHtml);

					$('#show_all').html(t.OperatorsBlockkHtml);

					$('.js-service-not-repeat-content').html(t.serviceList)

					document.documentElement.classList.remove("_loaded")
				},
				error: function(t) {
					console.log(t)
				},
			})
		});

		$('#show_all').click(function() {
			$(this).remove()
			$('.blockk').removeClass('.blockk')
		})


		$('.c-collapse').click(function() {
			$(this).toggleClass('is-show');
			$(this).children('.collapse-faq__content').toggle(
				function() {
					$(this).children('.collapse-faq__content').css("display", "");
				},
				function() {
					$(this).children('.collapse-faq__content').css("display", "none");
				}
			);
		})
	</script>


<?php


		}

		add_action('wp_footer', 'sms_js');


		function get_number_status()
		{
			$country = $_POST['country'];
			if (!empty($country)) {
				$result = post('https://smshub.org/stubs/handler_api.php?api_key=' . get_option('SMS_API') . '&action=getNumbersStatus&country=' . get_country_code($country));
				echo $result;
				exit;
			}
			echo false;
			exit;
		}

		add_action('wp_ajax_nopriv_get_number_status', 'get_number_status');


		function get_country_code($name)
		{
			$country_name = strtolower($name);

			
			// strtolower($name);

			// var_dump($country_name);
			// exit;
			$country_name = preg_replace('/\(.*?\)/', '', $country_name);

			$country_name = str_replace(" ","",$country_name);
			
			$country = array(
				'russia' => '0',
				'ukraine' => '1',
				'kazakhstan' => '2',
				'china' => '3',
				'philippines' => '4',
				'myanmar' => '5',
				'indonesia' => '6',
				'malaysia' => '7',
				'kenya' => '8',
				'tanzania' => '9',
				'vietnam' => '10',
				'kyrgyzstan' => '11',
				'usa' => '12',
				'israel' => '13',
				'hongkong' => '14',
				'poland' => '15',
				'england' => '16',
				'dcongo' => '18',
				'nigeria' => '19',
				'egypt' => '21',
				'india' => '22',
				'ireland' => '23',
				'cambodia' => '24',
				'laos' => '25',
				'haiti' => '26',
				'ivory' => '27',
				'gambia' => '28',
				'serbia' => '29',
				'yemen' => '30',
				'southafrica' => '31',
				'romania' => '32',
				'colombia' => '33',
				'estonia' => '34',
				'canada' => '36',
				'morocco' => '37',
				'ghana' => '38',
				'argentina' => '39',
				'uzbekistan' => '40',
				'cameroon' => '41',
				'chad' => '42',
				'germany' => '43',
				'lithuania' => '44',
				'croatia' => '45',
				'sweden' => '46',
				'iraq' => '47',
				'netherlands' => '48',
				'latvia' => '49',
				'austria' => '50',
				'belarus' => '51',
				'thailand' => '52',
				'saudiarabia' => '53',
				'mexico' => '54',
				'taiwan' => '55',
				'spain' => '56',
				'iran' => '57',
				'algeria' => '58',
				'bangladesh' => '60',
				'senegal' => '61',
				'turkey' => '62',
				'czech' => '63',
				'srilanka' => '64',
				'peru' => '65',
				'pakistan' => '66',
				'newzealand' => '67',
				'guinea' => '68',
				'mali' => '69',
				'venezuela' => '70',
				'mongolia' => '72',
				'brazil' => '73',
				'afghanistan' => '74',
				'uganda' => '75',
				'angola' => '76',
				'cyprus' => '77',
				'france' => '78',
				'papua' => '79',
				'mozambique' => '80',
				'nepal' => '81',
				'bulgaria' => '83',
				'moldova' => '85',
				'paraguay' => '87',
				'honduras' => '88',
				'tunisia' => '89',
				'nicaragua' => '90',
				'bolivia' => '92',
				'guatemala' => '94',
				'uae' => '95',
				'zimbabwe' => '96',
				'sudan' => '98',
				'salvador' => '101',
				'libyan' => '102',
				'jamaica' => '103',
				'trinidad' => '104',
				'ecuador' => '105',
				'dominican' => '109',
				'syrian' => '110',
				'mauritania' => '114',
				'sierraleone' => '115',
				'jordan' => '116',
				'portugal' => '117',
				'benin' => '120',
				'brunei' => '121',
				'botswana' => '123',
				'dominica' => '126',
				'georgia' => '128',
				'greece' => '129',
				'guyana' => '131',
				'liberia' => '135',
				'suriname' => '142',
				'tajikistan' => '143',
				'reunion' => '146',
				'armenia' => '148',
				'congo' => '150',
				'chile' => '151',
				'burkinafaso' => '152',
				'lebanon' => '153',
				'gabon' => '154',
				'mauritius' => '157',
				'bhutan' => '158',
				'maldives' => '159',
				'turkmenistan' => '161',
				'finland' => '163',
				'denmark' => '172',
				'aruba' => '179',
				'usaphysical' => '187',
				'fiji' => '189',
				'bermuda' => '195',
			);

			return $country[$country_name];
		}



		function get_sms_base_data()
		{


			$resquest_country = isset($_POST['country']) ? $_POST['country'] : '';

			
			$ID_countries_Operators = array(
				array('Name' => 'Россия', 'countries' => 'russia', 'availableOperators' => 'aiva, any, beeline, center2m, danycom, ezmobile, lycamobile, matrix, megafon, motiv, mts, mtt, mtt_virtual, rostelecom, sber, simsim, tele2, tinkoff, ttk, winmobile, yota,'),
				array('Name' => 'Украина', 'countries' => 'ukraine', 'availableOperators' => '3mob, any, intertelecom, kyivstar, life, lycamobile, mts, utel, vodafone,'),
				array('Name' => 'Казахстан', 'countries' => 'kazakhstan', 'availableOperators' => 'activ, altel, any, beeline, kcell, tele2,'),
				array('Name' => 'Китай', 'countries' => 'china', 'availableOperators' => 'any, chinamobile, china_unicom, unicom,'),
				array('Name' => 'Филиппины', 'countries' => 'philippines', 'availableOperators' => 'any, globe_telecom, smart, tm,'),
				array('Name' => 'Мьянма', 'countries' => 'myanmar', 'availableOperators' => 'any, telenor,'),
				array('Name' => 'Индонезия', 'countries' => 'indonesia', 'availableOperators' => 'any, axis, indosat, smartfren, telkomsel, three,'),
				array('Name' => 'Малайзия', 'countries' => 'malaysia', 'availableOperators' => 'any, celcom, digi, hotlink, u_mobile, xox,'),
				array('Name' => 'Кения', 'countries' => 'kenya', 'availableOperators' => 'airtel, any, econet, orange, safaricom, telkom,'),
				array('Name' => 'Танзания', 'countries' => 'tanzania', 'availableOperators' => 'airtel, any, tigo, vodacom,'),
				array('Name' => 'Вьетнам', 'countries' => 'vietnam', 'availableOperators' => 'any, itelecom, mobifone, vietnamobile, viettel, vinaphone,'),
				array('Name' => 'Кыргызстан', 'countries' => 'kyrgyzstan', 'availableOperators' => 'any, beeline, megacom, o!,'),
				array('Name' => 'США (виртуальные)', 'countries' => 'usa', 'availableOperators' => 'any, cellular, tmobile,'),
				array('Name' => 'Израиль', 'countries' => 'israel', 'availableOperators' => '019mobile, any, golan_telecom, home_cellular, hot_mobile, jawwal, ooredoo, orange, pelephone, rami_levy,'),
				array('Name' => 'Гонконг', 'countries' => 'hongkong', 'availableOperators' => 'any, chinamobile, csl_mobile, imc, smartone, three, unicom,'),
				array('Name' => 'Польша', 'countries' => 'poland', 'availableOperators' => 'aero2, any, e_telko, klucz, lycamobile, netia, orange, play, plus, tmobile,'),
				array('Name' => 'Англия', 'countries' => 'england (uk)', 'availableOperators' => 'any, cmlink, ee, ezmobile, lebara, lycamobile, o2, orange, talk_telecom, three, tmobile, vectone, vodafone,'),
				array('Name' => 'Дем.Конго', 'countries' => 'dcongo', 'availableOperators' => 'africel, airtel, any, orange, vodacom,'),
				array('Name' => 'Нигерия', 'countries' => 'nigeria', 'availableOperators' => 'airtel, any, etisalat, glomobile, mtn,'),
				array('Name' => 'Египет', 'countries' => 'egypt', 'availableOperators' => 'any, etisalat, orange, vodafone,'),
				array('Name' => 'Индия', 'countries' => 'india', 'availableOperators' => 'any,'),
				array('Name' => 'Ирландия', 'countries' => 'ireland', 'availableOperators' => '48mobile, any, lycamobile, tesco,'),
				array('Name' => 'Камбоджа', 'countries' => 'cambodia', 'availableOperators' => 'any, metfone,'),
				array('Name' => 'Лаос', 'countries' => 'laos', 'availableOperators' => 'any, etl, telekom, tplus, unitel,'),
				array('Name' => 'Гаити', 'countries' => 'haiti', 'availableOperators' => 'any, natcom,'),
				array('Name' => 'Кот дИвуар', 'countries' => 'ivory', 'availableOperators' => 'any, moov, mtn, orange,'),
				array('Name' => 'Гамбия', 'countries' => 'gambia', 'availableOperators' => 'africel, any, comium, gamcel, qcell,'),
				array('Name' => 'Сербия', 'countries' => 'serbia', 'availableOperators' => 'any, mobtel, mts, vip,'),
				array('Name' => 'Йемен', 'countries' => 'yemen', 'availableOperators' => 'any, mtn, sabafon, yemen_mobile,'),
				array('Name' => 'Южная Африка', 'countries' => 'southafrica', 'availableOperators' => 'any, cell_c, lycamobile, mtn, telkom, vodacom,'),
				array('Name' => 'Румыния', 'countries' => 'romania', 'availableOperators' => 'any, digi, lycamobile, my_avon, orange, runex_telecom, telekom, vodafone,'),
				array('Name' => 'Колумбия', 'countries' => 'colombia', 'availableOperators' => 'any, claro, movistar,'),
				array('Name' => 'Эстония', 'countries' => 'estonia', 'availableOperators' => 'any, elisa, goodline, super, tele2, topconnect,'),
				array('Name' => 'Канада', 'countries' => 'canada', 'availableOperators' => 'any, cellular, fido, lucky, rogers, telus,'),
				array('Name' => 'Марокко', 'countries' => 'morocco', 'availableOperators' => 'any, iam, inwi, itissalat, orange,'),
				array('Name' => 'Гана', 'countries' => 'ghana', 'availableOperators' => 'airtel, any, glomobile, millicom, mtn, vodafone,'),
				array('Name' => 'Аргентина', 'countries' => 'argentina', 'availableOperators' => 'any,'),
				array('Name' => 'Узбекистан', 'countries' => 'uzbekistan', 'availableOperators' => 'any, beeline, humans, mobiuz, mts, ucell, uzmobile,'),
				array('Name' => 'Камерун', 'countries' => 'cameroon', 'availableOperators' => 'any, mtn, nexttel, orange,'),
				array('Name' => 'Чад', 'countries' => 'chad', 'availableOperators' => 'airtel, any, salam, tigo,'),
				array('Name' => 'Германия', 'countries' => 'germany', 'availableOperators' => 'any, fonic, lebara, lycamobile, o2, ortel_mobile, telekom, vodafone,'),
				array('Name' => 'Литва', 'countries' => 'lithuania', 'availableOperators' => 'any, bite, tele2, telia,'),
				array('Name' => 'Хорватия', 'countries' => 'croatia', 'availableOperators' => 'a1, any, tele2, tmobile, tomato,'),
				array('Name' => 'Швеция', 'countries' => 'sweden', 'availableOperators' => 'any, comviq, lycamobile, tele2, telenor, telia, three, vectone, vodafone,'),
				array('Name' => 'Ирак', 'countries' => 'iraq', 'availableOperators' => 'any, asiacell, korek, zain,'),
				array('Name' => 'Нидерланды', 'countries' => 'netherlands', 'availableOperators' => 'any, kpn, lebara, lycamobile, l_mobi, tmobile, vodafone,'),
				array('Name' => 'Латвия', 'countries' => 'latvia', 'availableOperators' => 'any, bite, lmt, tele2,'),
				array('Name' => 'Австрия', 'countries' => 'austria', 'availableOperators' => 'a1, any, orange, telering, three, tmobile,'),
				array('Name' => 'Беларусь', 'countries' => 'belarus', 'availableOperators' => 'any, life, mdc, mts,'),
				array('Name' => 'Таиланд', 'countries' => 'thailand', 'availableOperators' => 'ais, any, dtac, truemove,'),
				array('Name' => 'Сауд. Аравия', 'countries' => 'saudiarabia', 'availableOperators' => 'any,'),
				array('Name' => 'Мексика', 'countries' => 'mexico', 'availableOperators' => 'any, telcel,'),
				array('Name' => 'Тайвань', 'countries' => 'taiwan', 'availableOperators' => 'any, chunghwa, fareast,'),
				array('Name' => 'Испания', 'countries' => 'spain', 'availableOperators' => 'altecom, any, euskaltel, lebara, llamaya, lycamobile, movistar, orange, vodafone, yoigo, you_mobile,'),
				array('Name' => 'Иран', 'countries' => 'iran', 'availableOperators' => 'any, aptel, azartel, hamrah_e_aval, irancell, mtn, rightel, samantel, shatel, taliya, tci,'),
				array('Name' => 'Алжир', 'countries' => 'algeria', 'availableOperators' => 'any, djezzy, mobilis, ooredoo,'),
				array('Name' => 'Бангладеш', 'countries' => 'bangladesh', 'availableOperators' => 'any,'),
				array('Name' => 'Сенегал', 'countries' => 'senegal', 'availableOperators' => 'any, expresso, free, orange,'),
				array('Name' => 'Турция', 'countries' => 'turkey', 'availableOperators' => 'any, turkcell,'),
				array('Name' => 'Чехия', 'countries' => 'czech', 'availableOperators' => 'any, o2, tmobile, vodafone,'),
				array('Name' => 'Шри-Ланка', 'countries' => 'srilanka', 'availableOperators' => 'airtel, any, etisalat,'),
				array('Name' => 'Перу', 'countries' => 'peru', 'availableOperators' => 'any,'),
				array('Name' => 'Пакистан', 'countries' => 'pakistan', 'availableOperators' => 'any,'),
				array('Name' => 'Новая Зеландия', 'countries' => 'newzealand', 'availableOperators' => '2degree, any, vodafone,'),
				array('Name' => 'Гвинея', 'countries' => 'guinea', 'availableOperators' => 'any, cellcom, mtn, orange, sotelgui, telecel,'),
				array('Name' => 'Мали', 'countries' => 'mali', 'availableOperators' => 'any, malitel, orange, telecel,'),
				array('Name' => 'Венесуэла', 'countries' => 'venezuela', 'availableOperators' => 'any,'),
				array('Name' => 'Монголия', 'countries' => 'mongolia', 'availableOperators' => 'any, beeline,'),
				array('Name' => 'Бразилия', 'countries' => 'brazil', 'availableOperators' => 'algartelecom, any, claro, correios_celular, oi, tim, vivo,'),
				array('Name' => 'Афганистан', 'countries' => 'afghanistan', 'availableOperators' => 'any, salaam,'),
				array('Name' => 'Уганда', 'countries' => 'uganda', 'availableOperators' => 'airtel, any, k2_telecom, lycamobile, mtn, orange, smart, smile, uganda_telecom,'),
				array('Name' => 'Ангола', 'countries' => 'angola', 'availableOperators' => 'any,'),
				array('Name' => 'Кипр', 'countries' => 'cyprus', 'availableOperators' => 'any, cablenet, cyta, epic, lemontel, primetel, vectone,'),
				array('Name' => 'Франция', 'countries' => 'france', 'availableOperators' => 'any, bouygues, lebara, lycamobile, orange, sfr, syma_mobile, vectone,'),
				array('Name' => 'Папуа-Новая Гвинея', 'countries' => 'papua', 'availableOperators' => 'any,'),
				array('Name' => 'Мозамбик', 'countries' => 'mozambique', 'availableOperators' => 'any,'),
				array('Name' => 'Непал', 'countries' => 'nepal', 'availableOperators' => 'any,'),
				array('Name' => 'Болгария', 'countries' => 'bulgaria', 'availableOperators' => 'a1, any, telenor, vivacom,'),
				array('Name' => 'Молдова', 'countries' => 'moldova', 'availableOperators' => 'any, idc, moldcell, orange, unite,'),
				array('Name' => 'Парагвай', 'countries' => 'paraguay', 'availableOperators' => 'any,'),
				array('Name' => 'Гондурас', 'countries' => 'honduras', 'availableOperators' => 'any, claro,'),
				array('Name' => 'Тунис', 'countries' => 'tunisia', 'availableOperators' => 'any, ooredoo, orange, tunicell,'),
				array('Name' => 'Никарагуа', 'countries' => 'nicaragua', 'availableOperators' => 'any, movistar,'),
				array('Name' => 'Боливия', 'countries' => 'bolivia', 'availableOperators' => 'any,'),
				array('Name' => 'Гватемала', 'countries' => 'guatemala', 'availableOperators' => 'any, claro, movistar, tigo,'),
				array('Name' => 'ОАЭ', 'countries' => 'uae', 'availableOperators' => 'any,'),
				array('Name' => 'Зимбабве', 'countries' => 'zimbabwe', 'availableOperators' => 'any, econet,'),
				array('Name' => 'Судан', 'countries' => 'sudan', 'availableOperators' => 'any, mtn, sudani_one, zain,'),
				array('Name' => 'Сальвадор', 'countries' => 'salvador', 'availableOperators' => 'any, claro, digi, movistar, red, tigo,'),
				array('Name' => 'Ливия', 'countries' => 'libyan', 'availableOperators' => 'any,'),
				array('Name' => 'Ямайка', 'countries' => 'jamaica', 'availableOperators' => 'any, digi,'),
				array('Name' => 'Тринидад и Тобаго', 'countries' => 'trinidad', 'availableOperators' => 'any,'),
				array('Name' => 'Эквадор', 'countries' => 'ecuador', 'availableOperators' => 'any, claro, cnt_mobile, movistar, tuenti,'),
				array('Name' => 'Доминиканская Республика', 'countries' => 'dominican', 'availableOperators' => 'altice, any, claro, viva,'),
				array('Name' => 'Сирия', 'countries' => 'syrian', 'availableOperators' => 'any,'),
				array('Name' => 'Мавритания', 'countries' => 'mauritania', 'availableOperators' => 'any, chinguitel, mattel, mauritel,'),
				array('Name' => 'Сьерра-Леоне', 'countries' => 'sierraleone', 'availableOperators' => 'africel, airtel, any, qcell,'),
				array('Name' => 'Иордания', 'countries' => 'jordan', 'availableOperators' => 'any, orange, umniah, xpress, zain,'),
				array('Name' => 'Португалия', 'countries' => 'portugal', 'availableOperators' => 'any, lycamobile, nos, vodafone,'),
				array('Name' => 'Бенин', 'countries' => 'benin', 'availableOperators' => 'any,'),
				array('Name' => 'Бруней', 'countries' => 'brunei', 'availableOperators' => 'any,'),
				array('Name' => 'Ботсвана', 'countries' => 'botswana', 'availableOperators' => 'any, be_mobile, mascom, orange,'),
				array('Name' => 'Доминика', 'countries' => 'dominica', 'availableOperators' => 'any,'),
				array('Name' => 'Грузия', 'countries' => 'georgia', 'availableOperators' => 'any, beeline, geocell, hamrah_e_aval, magticom,'),
				array('Name' => 'Греция', 'countries' => 'greece', 'availableOperators' => 'any, cosmote, ose, q_telecom, vodafone,'),
				array('Name' => 'Гайана', 'countries' => 'guyana', 'availableOperators' => 'any,'),
				array('Name' => 'Либерия', 'countries' => 'liberia', 'availableOperators' => 'any, cellcom, comium, libercell, libtelco, lonestar,'),
				array('Name' => 'Суринам', 'countries' => 'suriname', 'availableOperators' => 'any,'),
				array('Name' => 'Таджикистан', 'countries' => 'tajikistan', 'availableOperators' => 'any,'),
				array('Name' => 'Реюньон', 'countries' => 'reunion', 'availableOperators' => 'any,'),
				array('Name' => 'Армения', 'countries' => 'armenia', 'availableOperators' => 'any, viva, vivo,'),
				array('Name' => 'Конго', 'countries' => 'congo', 'availableOperators' => 'any,'),
				array('Name' => 'Чили', 'countries' => 'chile', 'availableOperators' => 'any, claro, entel, movistar,'),
				array('Name' => 'Буркина-Фасо', 'countries' => 'burkinafaso', 'availableOperators' => 'airtel, any, onatel, telecel,'),
				array('Name' => 'Ливан', 'countries' => 'lebanon', 'availableOperators' => 'alfa, any, ogero, touch,'),
				array('Name' => 'Габон', 'countries' => 'gabon', 'availableOperators' => 'any,'),
				array('Name' => 'Маврикий', 'countries' => 'mauritius', 'availableOperators' => 'any,'),
				array('Name' => 'Бутан', 'countries' => 'bhutan', 'availableOperators' => 'any,'),
				array('Name' => 'Мальдивы', 'countries' => 'maldives', 'availableOperators' => 'any,'),
				array('Name' => 'Туркменистан', 'countries' => 'turkmenistan', 'availableOperators' => 'any,'),
				array('Name' => 'Финляндия', 'countries' => 'finland', 'availableOperators' => 'any, dna,'),
				array('Name' => 'Дания', 'countries' => 'denmark', 'availableOperators' => 'any, lycamobile,'),
				array('Name' => 'Аруба', 'countries' => 'aruba', 'availableOperators' => 'any,'),
				array('Name' => 'США', 'countries' => 'usaphysical', 'availableOperators' => 'any, lycamobile, tmobile,'),
				array('Name' => 'Фиджи', 'countries' => 'fiji', 'availableOperators' => 'any,'),
				array('Name' => 'Бермуды', 'countries' => 'bermuda', 'availableOperators' => 'any,'),

			);


			//按照运营商返回显示代码
			// foreach ($ID_countries_Operators as $ID => $arr) {



			// 	if (!empty($resquest_country) && $arr['countries'] == strtolower($resquest_country)) {
			// 		$Operators = explode(', ', $arr['availableOperators']);
			// 		break;
			// 	} elseif (empty($resquest_country)) {
			// 		$Operators = explode(', ', $arr['availableOperators']);
			// 		break;
			// 	}
			// }

			// if (!empty($Operators)) {
			// 	foreach ($Operators as $k => $Operator) {
			// 		if ($k + 1 < 4) {
			// 			$OperatorsHtml .=
			// 				' <div class="specify-box" onclick="get_service">
								
			// 					<p class="specify-text">' . $Operator . '</p>
			// 					<div class="specify-deliverability" data-deliverability="" style="display: none;">(43.49%)
			// 					</div>
			// 					<p></p>
			// 				</div>';
			// 		} elseif ($k + 1 == 4) {
			// 			$OperatorsHtml .= '
			// 					<a href="#" id="show_all" class="show-operator">Show all</a>
			// 					<div class="specify-box blockk" onclick="get_service">
									
			// 						<p class="specify-text">' . $Operator . '</p>
			// 						<div class="specify-deliverability" data-deliverability="" style="display: none;">(8.70%)
			// 						</div>

			// 						<p></p>
			// 					</div>';
			// 		} else {

			// 			$OperatorsHtml .= '<div class="specify-box blockk" onclick="get_service">
									
			// 						<p class="specify-text">' . $Operator . '</p>
			// 						<div class="specify-deliverability" data-deliverability="" style="display: none;">(8.70%)
			// 						</div>

			// 						<p></p>
			// 					</div>';
			// 		}
			// 	}
			// }

			$OperatorsHtml .=' <div class="specify-box" onclick="get_service">
								
								<p class="specify-text">any</p>
								<div class="specify-deliverability" data-deliverability="" style="display: none;">(43.49%)
								</div>
								<p></p>
							</div>';

			$service = get_services_ajax($resquest_country);
			
			if (!empty($resquest_country)) {
				echo json_encode(array('OperatorsHtml' => $OperatorsHtml, 'serviceList' => $service));
				exit;
			} else {
				return $OperatorsHtml;
			}
		}
		add_action('wp_ajax_base_data', 'get_sms_base_data');
		add_action('wp_ajax_nopriv_base_data', 'get_sms_base_data');

		function country_arr($country)
		{
			$country_data = array(
				array("contry-name" => "Russia", "contry-flag" => "flag-icon flag-icon-ru"),
				array("contry-name" => "Ukraine", "contry-flag" => "flag-icon flag-icon-ua"),
				array("contry-name" => "Kazakhstan", "contry-flag" => "flag-icon flag-icon-kz"),
				array("contry-name" => "China", "contry-flag" => "flag-icon flag-icon-cn"),
				array("contry-name" => "England (UK)", "contry-flag" => "flag-icon flag-icon-gb"),
				array("contry-name" => "Myanmar", "contry-flag" => "flag-icon flag-icon-mm"),
				array("contry-name" => "Indonesia", "contry-flag" => "flag-icon flag-icon-id"),
				array("contry-name" => "Malaysia", "contry-flag" => "flag-icon flag-icon-my"),
				array("contry-name" => "Kenya", "contry-flag" => "flag-icon flag-icon-ke"),
				array("contry-name" => "Tanzania", "contry-flag" => "flag-icon flag-icon-tz"),
				array("contry-name" => "Viet Nam", "contry-flag" => "flag-icon flag-icon-vn"),
				array("contry-name" => "Kyrgyzstan", "contry-flag" => "flag-icon flag-icon-kg"),
				array("contry-name" => "USA (Virtual)", "contry-flag" => "flag-icon flag-icon-us"),
				array("contry-name" => "Israel", "contry-flag" => "flag-icon flag-icon-il"),
				array("contry-name" => "Hong Kong (China)", "contry-flag" => "flag-icon flag-icon-hk"),
				array("contry-name" => "Poland", "contry-flag" => "flag-icon flag-icon-pl"),
				array("contry-name" => "Philippines", "contry-flag" => "flag-icon flag-icon-ph"),
				array("contry-name" => "Madagascar", "contry-flag" => "flag-icon flag-icon-mg"),
				array("contry-name" => "Dem. Congo", "contry-flag" => "flag-icon flag-icon-cd"),
				array("contry-name" => "Nigeria", "contry-flag" => "flag-icon flag-icon-ng"),
				array("contry-name" => "Macau", "contry-flag" => "flag-icon flag-icon-mo"),
				array("contry-name" => "Egypt", "contry-flag" => "flag-icon flag-icon-eg"),
				array("contry-name" => "India", "contry-flag" => "flag-icon flag-icon-in"),
				array("contry-name" => "Ireland", "contry-flag" => "flag-icon flag-icon-ie"),
				array("contry-name" => "Cambodia", "contry-flag" => "flag-icon flag-icon-kh"),
				array("contry-name" => "Laos", "contry-flag" => "flag-icon flag-icon-la"),
				array("contry-name" => "Haiti", "contry-flag" => "flag-icon flag-icon-ht"),
				array("contry-name" => "Côte d\'Ivoire", "contry-flag" => "flag-icon flag-icon-ci"),
				array("contry-name" => "Gambia", "contry-flag" => "flag-icon flag-icon-gm"),
				array("contry-name" => "Serbia", "contry-flag" => "flag-icon flag-icon-rs"),
				array("contry-name" => "Yemen", "contry-flag" => "flag-icon flag-icon-ye"),
				array("contry-name" => "SOUTH AFRICA", "contry-flag" => "flag-icon flag-icon-za"),
				array("contry-name" => "Romania", "contry-flag" => "flag-icon flag-icon-ro"),
				array("contry-name" => "Colombia", "contry-flag" => "flag-icon flag-icon-co"),
				array("contry-name" => "Estonia", "contry-flag" => "flag-icon flag-icon-ee"),
				array("contry-name" => "Azerbaijan", "contry-flag" => "flag-icon flag-icon-az"),
				array("contry-name" => "Canada", "contry-flag" => "flag-icon flag-icon-ca"),
				array("contry-name" => "Morocco", "contry-flag" => "flag-icon flag-icon-ma"),
				array("contry-name" => "Ghana", "contry-flag" => "flag-icon flag-icon-gh"),
				array("contry-name" => "Argentina", "contry-flag" => "flag-icon flag-icon-ar"),
				array("contry-name" => "Uzbekistan", "contry-flag" => "flag-icon flag-icon-uz"),
				array("contry-name" => "Cameroon", "contry-flag" => "flag-icon flag-icon-cm"),
				array("contry-name" => "Chad", "contry-flag" => "flag-icon flag-icon-td"),
				array("contry-name" => "Germany", "contry-flag" => "flag-icon flag-icon-de"),
				array("contry-name" => "Lithuania", "contry-flag" => "flag-icon flag-icon-lt"),
				array("contry-name" => "Croatia", "contry-flag" => "flag-icon flag-icon-hr"),
				array("contry-name" => "Sweden", "contry-flag" => "flag-icon flag-icon-se"),
				array("contry-name" => "Iraq", "contry-flag" => "flag-icon flag-icon-iq"),
				array("contry-name" => "Netherlands", "contry-flag" => "flag-icon flag-icon-nl"),
				array("contry-name" => "Latvia", "contry-flag" => "flag-icon flag-icon-lv"),
				array("contry-name" => "Austria", "contry-flag" => "flag-icon flag-icon-at"),
				array("contry-name" => "Belarus", "contry-flag" => "flag-icon flag-icon-by"),
				array("contry-name" => "Thailand", "contry-flag" => "flag-icon flag-icon-th"),
				array("contry-name" => "Saudi Arabia", "contry-flag" => "flag-icon flag-icon-sa"),
				array("contry-name" => "Mexico", "contry-flag" => "flag-icon flag-icon-mx"),
				array("contry-name" => "Taiwan", "contry-flag" => "flag-icon flag-icon-tw"),
				array("contry-name" => "Spain", "contry-flag" => "flag-icon flag-icon-es"),
				array("contry-name" => "Iran", "contry-flag" => "flag-icon flag-icon-ir"),
				array("contry-name" => "Algeria", "contry-flag" => "flag-icon flag-icon-dz"),
				array("contry-name" => "Slovenia", "contry-flag" => "flag-icon flag-icon-si"),
				array("contry-name" => "Bangladesh", "contry-flag" => "flag-icon flag-icon-bd"),
				array("contry-name" => "Senegal", "contry-flag" => "flag-icon flag-icon-sn"),
				array("contry-name" => "Turkey", "contry-flag" => "flag-icon flag-icon-tr"),
				array("contry-name" => "Czech Republic", "contry-flag" => "flag-icon flag-icon-cz"),
				array("contry-name" => "Sri Lanka", "contry-flag" => "flag-icon flag-icon-lk"),
				array("contry-name" => "Peru", "contry-flag" => "flag-icon flag-icon-pe"),
				array("contry-name" => "Pakistan", "contry-flag" => "flag-icon flag-icon-pk"),
				array("contry-name" => "New Zealand", "contry-flag" => "flag-icon flag-icon-nz"),
				array("contry-name" => "Guinea", "contry-flag" => "flag-icon flag-icon-gn"),
				array("contry-name" => "Mali", "contry-flag" => "flag-icon flag-icon-ml"),
				array("contry-name" => "Venezuela", "contry-flag" => "flag-icon flag-icon-ve"),
				array("contry-name" => "Ethiopia", "contry-flag" => "flag-icon flag-icon-et"),
				array("contry-name" => "Mongolia", "contry-flag" => "flag-icon flag-icon-mn"),
				array("contry-name" => "Brazil", "contry-flag" => "flag-icon flag-icon-br"),
				array("contry-name" => "Afghanistan", "contry-flag" => "flag-icon flag-icon-af"),
				array("contry-name" => "Uganda", "contry-flag" => "flag-icon flag-icon-ug"),
				array("contry-name" => "Angola", "contry-flag" => "flag-icon flag-icon-ao"),
				array("contry-name" => "Cyprus", "contry-flag" => "flag-icon flag-icon-cy"),
				array("contry-name" => "France", "contry-flag" => "flag-icon flag-icon-fr"),
				array("contry-name" => "New Guinea", "contry-flag" => "flag-icon flag-icon-pg"),
				array("contry-name" => "Mozambique", "contry-flag" => "flag-icon flag-icon-mz"),
				array("contry-name" => "Nepal", "contry-flag" => "flag-icon flag-icon-np"),
				array("contry-name" => "Belgium", "contry-flag" => "flag-icon flag-icon-be"),
				array("contry-name" => "Bulgaria", "contry-flag" => "flag-icon flag-icon-bg"),
				array("contry-name" => "Hungary", "contry-flag" => "flag-icon flag-icon-hu"),
				array("contry-name" => "Moldova", "contry-flag" => "flag-icon flag-icon-md"),
				array("contry-name" => "Italy", "contry-flag" => "flag-icon flag-icon-it"),
				array("contry-name" => "Paraguay", "contry-flag" => "flag-icon flag-icon-py"),
				array("contry-name" => "Honduras", "contry-flag" => "flag-icon flag-icon-hn"),
				array("contry-name" => "Tunisia", "contry-flag" => "flag-icon flag-icon-tn"),
				array("contry-name" => "Nicaragua", "contry-flag" => "flag-icon flag-icon-ni"),
				array("contry-name" => "Timor-Leste", "contry-flag" => "flag-icon flag-icon-tl"),
				array("contry-name" => "Bolivia", "contry-flag" => "flag-icon flag-icon-bo"),
				array("contry-name" => "Costa Rica", "contry-flag" => "flag-icon flag-icon-cr"),
				array("contry-name" => "Guatemala", "contry-flag" => "flag-icon flag-icon-gt"),
				array("contry-name" => "UNITED ARAB EMIRATES", "contry-flag" => "flag-icon flag-icon-ae"),
				array("contry-name" => "Zimbabwe", "contry-flag" => "flag-icon flag-icon-zw"),
				array("contry-name" => "Puerto Rico", "contry-flag" => "flag-icon flag-icon-pr"),
				array("contry-name" => "Sudan", "contry-flag" => "flag-icon flag-icon-sd"),
				array("contry-name" => "Togo", "contry-flag" => "flag-icon flag-icon-tg"),
				array("contry-name" => "Kuwait", "contry-flag" => "flag-icon flag-icon-kw"),
				array("contry-name" => "El Salvador", "contry-flag" => "flag-icon flag-icon-sv"),
				array("contry-name" => "Libya", "contry-flag" => "flag-icon flag-icon-ly"),
				array("contry-name" => "Jamaica", "contry-flag" => "flag-icon flag-icon-jm"),
				array("contry-name" => "Trinidad and Tobago", "contry-flag" => "flag-icon flag-icon-tt"),
				array("contry-name" => "Ecuador", "contry-flag" => "flag-icon flag-icon-ec"),
				array("contry-name" => "Swaziland", "contry-flag" => "flag-icon flag-icon-sz"),
				array("contry-name" => "Oman", "contry-flag" => "flag-icon flag-icon-om"),
				array("contry-name" => "Bosnia and Herzegovina", "contry-flag" => "flag-icon flag-icon-ba"),
				array("contry-name" => "Dominican Republic", "contry-flag" => "flag-icon flag-icon-do"),
				array("contry-name" => "Qatar", "contry-flag" => "flag-icon flag-icon-qa"),
				array("contry-name" => "Cuba", "contry-flag" => "flag-icon flag-icon-cu"),
				array("contry-name" => "Panama", "contry-flag" => "flag-icon flag-icon-pa"),
				array("contry-name" => "Mauritania", "contry-flag" => "flag-icon flag-icon-mr"),
				array("contry-name" => "Sierra Leone", "contry-flag" => "flag-icon flag-icon-sl"),
				array("contry-name" => "Jordan", "contry-flag" => "flag-icon flag-icon-jo"),
				array("contry-name" => "Portugal", "contry-flag" => "flag-icon flag-icon-pt"),
				array("contry-name" => "Barbados", "contry-flag" => "flag-icon flag-icon-bb"),
				array("contry-name" => "Burundi", "contry-flag" => "flag-icon flag-icon-bi"),
				array("contry-name" => "Benin", "contry-flag" => "flag-icon flag-icon-bj"),
				array("contry-name" => "Brunei", "contry-flag" => "flag-icon flag-icon-bn"),
				array("contry-name" => "Bahamas", "contry-flag" => "flag-icon flag-icon-bs"),
				array("contry-name" => "Botswana", "contry-flag" => "flag-icon flag-icon-bw"),
				array("contry-name" => "Belize", "contry-flag" => "flag-icon flag-icon-bz"),
				array("contry-name" => "CAR", "contry-flag" => "flag-icon flag-icon-cf"),
				array("contry-name" => "Dominica", "contry-flag" => "flag-icon flag-icon-dm"),
				array("contry-name" => "Grenada", "contry-flag" => "flag-icon flag-icon-gd"),
				array("contry-name" => "Georgia", "contry-flag" => "flag-icon flag-icon-ge"),
				array("contry-name" => "Greece", "contry-flag" => "flag-icon flag-icon-gr"),
				array("contry-name" => "Guinea-Bissau", "contry-flag" => "flag-icon flag-icon-gw"),
				array("contry-name" => "Guyana", "contry-flag" => "flag-icon flag-icon-gy"),
				array("contry-name" => "Iceland", "contry-flag" => "flag-icon flag-icon-is"),
				array("contry-name" => "Comoros", "contry-flag" => "flag-icon flag-icon-km"),
				array("contry-name" => "Saint Kitts and Nevis", "contry-flag" => "flag-icon flag-icon-kn"),
				array("contry-name" => "Liberia", "contry-flag" => "flag-icon flag-icon-lr"),
				array("contry-name" => "Lesotho", "contry-flag" => "flag-icon flag-icon-ls"),
				array("contry-name" => "Malawi", "contry-flag" => "flag-icon flag-icon-mw"),
				array("contry-name" => "Namibia", "contry-flag" => "flag-icon flag-icon-na"),
				array("contry-name" => "Niger", "contry-flag" => "flag-icon flag-icon-ne"),
				array("contry-name" => "Rwanda", "contry-flag" => "flag-icon flag-icon-rw"),
				array("contry-name" => "Slovakia", "contry-flag" => "flag-icon flag-icon-sk"),
				array("contry-name" => "Suriname", "contry-flag" => "flag-icon flag-icon-sr"),
				array("contry-name" => "Tajikistan", "contry-flag" => "flag-icon flag-icon-tj"),
				array("contry-name" => "Monaco", "contry-flag" => "flag-icon flag-icon-mc"),
				array("contry-name" => "Bahrain", "contry-flag" => "flag-icon flag-icon-bh"),
				array("contry-name" => "Reunion", "contry-flag" => "flag-icon flag-icon-re"),
				array("contry-name" => "Zambia", "contry-flag" => "flag-icon flag-icon-zm"),
				array("contry-name" => "Armenia", "contry-flag" => "flag-icon flag-icon-am"),
				array("contry-name" => "Somalia", "contry-flag" => "flag-icon flag-icon-so"),
				array("contry-name" => "Congo", "contry-flag" => "flag-icon flag-icon-cg"),
				array("contry-name" => "Chile", "contry-flag" => "flag-icon flag-icon-cl"),
				array("contry-name" => "Burkina Faso", "contry-flag" => "flag-icon flag-icon-bf"),
				array("contry-name" => "Lebanon", "contry-flag" => "flag-icon flag-icon-lb"),
				array("contry-name" => "Gabon", "contry-flag" => "flag-icon flag-icon-ga"),
				array("contry-name" => "Albania", "contry-flag" => "flag-icon flag-icon-al"),
				array("contry-name" => "Uruguay", "contry-flag" => "flag-icon flag-icon-uy"),
				array("contry-name" => "Mauritius", "contry-flag" => "flag-icon flag-icon-mu"),
				array("contry-name" => "Bhutan", "contry-flag" => "flag-icon flag-icon-bt"),
				array("contry-name" => "Maldives", "contry-flag" => "flag-icon flag-icon-mv"),
				array("contry-name" => "Guadeloupe", "contry-flag" => "flag-icon flag-icon-gp"),
				array("contry-name" => "Turkmenistan", "contry-flag" => "flag-icon flag-icon-tm"),
				array("contry-name" => "French Guiana", "contry-flag" => "flag-icon flag-icon-gf"),
				array("contry-name" => "Finland", "contry-flag" => "flag-icon flag-icon-fi"),
				array("contry-name" => "Saint Lucia", "contry-flag" => "flag-icon flag-icon-lc"),
				array("contry-name" => "Luxembourg", "contry-flag" => "flag-icon flag-icon-lu"),
				array("contry-name" => "Saint Pierre and Miquelon", "contry-flag" => "flag-icon flag-icon-vc"),
				array("contry-name" => "Equatorial Guinea", "contry-flag" => "flag-icon flag-icon-gq"),
				array("contry-name" => "Djibouti", "contry-flag" => "flag-icon flag-icon-dj"),
				array("contry-name" => "Saint Kitts and Nevis", "contry-flag" => "flag-icon flag-icon-ag"),
				array("contry-name" => "Cayman Islands", "contry-flag" => "flag-icon flag-icon-ky"),
				array("contry-name" => "Montenegro", "contry-flag" => "flag-icon flag-icon-me"),
				array("contry-name" => "Denmark", "contry-flag" => "flag-icon flag-icon-dk"),
				array("contry-name" => "Switzerland", "contry-flag" => "flag-icon flag-icon-ch"),
				array("contry-name" => "Norway", "contry-flag" => "flag-icon flag-icon-no"),
				array("contry-name" => "Australia", "contry-flag" => "flag-icon flag-icon-au"),
				array("contry-name" => "Eritrea", "contry-flag" => "flag-icon flag-icon-er"),
				array("contry-name" => "South Sudan", "contry-flag" => "flag-icon flag-icon-ss"),
				array("contry-name" => "Sao Tome and Principe", "contry-flag" => "flag-icon flag-icon-st"),
				array("contry-name" => "Aruba", "contry-flag" => "flag-icon flag-icon-aw"),
				array("contry-name" => "Montserrat", "contry-flag" => "flag-icon flag-icon-ms"),
				array("contry-name" => "Anguilla", "contry-flag" => "flag-icon flag-icon-ai"),
				array("contry-name" => "Northern Macedonia", "contry-flag" => "flag-icon flag-icon-mk"),
				array("contry-name" => "Republic of Seychelles", "contry-flag" => "flag-icon flag-icon-sc"),
				array("contry-name" => "New Caledonia", "contry-flag" => "flag-icon flag-icon-nc"),
				array("contry-name" => "Cape Verde", "contry-flag" => "flag-icon flag-icon-cv"),
				array("contry-name" => "USA (Real)", "contry-flag" => "flag-icon flag-icon-vi"),
				array("contry-name" => "South Korea", "contry-flag" => "flag-icon flag-icon-kr"),
				array("contry-name" => "Fiji", "contry-flag" => "flag-icon flag-icon-fj"),
				array("contry-name" => "Bermuda", "contry-flag" => "flag-icon flag-icon-bm"),
			);
			if (!empty($country)) {
				foreach ($country_data as $country_k => $country_data) {
					if ($country_data['contry-name'] == $country) {
						return $country_data['contry-flag'];
					}
				}
			}
			return $country_data;
		}

		function echo_country()
		{
			$html = '';
			$country_arr =
				array(
					array("contry-name" => "Russia", "contry-flag" => "flag-icon flag-icon-ru"),
					array("contry-name" => "Ukraine", "contry-flag" => "flag-icon flag-icon-ua"),
					array("contry-name" => "Kazakhstan", "contry-flag" => "flag-icon flag-icon-kz"),
					array("contry-name" => "China", "contry-flag" => "flag-icon flag-icon-cn"),
					array("contry-name" => "England (UK)", "contry-flag" => "flag-icon flag-icon-gb"),
					array("contry-name" => "Myanmar", "contry-flag" => "flag-icon flag-icon-mm"),
					array("contry-name" => "Indonesia", "contry-flag" => "flag-icon flag-icon-id"),
					array("contry-name" => "Malaysia", "contry-flag" => "flag-icon flag-icon-my"),
					array("contry-name" => "Kenya", "contry-flag" => "flag-icon flag-icon-ke"),
					array("contry-name" => "Tanzania", "contry-flag" => "flag-icon flag-icon-tz"),
					array("contry-name" => "Viet Nam", "contry-flag" => "flag-icon flag-icon-vn"),
					array("contry-name" => "Kyrgyzstan", "contry-flag" => "flag-icon flag-icon-kg"),
					array("contry-name" => "USA (Virtual)", "contry-flag" => "flag-icon flag-icon-us"),
					array("contry-name" => "Israel", "contry-flag" => "flag-icon flag-icon-il"),
					array("contry-name" => "Hong Kong (China)", "contry-flag" => "flag-icon flag-icon-hk"),
					array("contry-name" => "Poland", "contry-flag" => "flag-icon flag-icon-pl"),
					array("contry-name" => "Philippines", "contry-flag" => "flag-icon flag-icon-ph"),
					array("contry-name" => "Madagascar", "contry-flag" => "flag-icon flag-icon-mg"),
					array("contry-name" => "Dem. Congo", "contry-flag" => "flag-icon flag-icon-cd"),
					array("contry-name" => "Nigeria", "contry-flag" => "flag-icon flag-icon-ng"),
					array("contry-name" => "Macau", "contry-flag" => "flag-icon flag-icon-mo"),
					array("contry-name" => "Egypt", "contry-flag" => "flag-icon flag-icon-eg"),
					array("contry-name" => "India", "contry-flag" => "flag-icon flag-icon-in"),
					array("contry-name" => "Ireland", "contry-flag" => "flag-icon flag-icon-ie"),
					array("contry-name" => "Cambodia", "contry-flag" => "flag-icon flag-icon-kh"),
					array("contry-name" => "Laos", "contry-flag" => "flag-icon flag-icon-la"),
					array("contry-name" => "Haiti", "contry-flag" => "flag-icon flag-icon-ht"),
					array("contry-name" => "Côte d\'Ivoire", "contry-flag" => "flag-icon flag-icon-ci"),
					array("contry-name" => "Gambia", "contry-flag" => "flag-icon flag-icon-gm"),
					array("contry-name" => "Serbia", "contry-flag" => "flag-icon flag-icon-rs"),
					array("contry-name" => "Yemen", "contry-flag" => "flag-icon flag-icon-ye"),
					array("contry-name" => "SOUTH AFRICA", "contry-flag" => "flag-icon flag-icon-za"),
					array("contry-name" => "Romania", "contry-flag" => "flag-icon flag-icon-ro"),
					array("contry-name" => "Colombia", "contry-flag" => "flag-icon flag-icon-co"),
					array("contry-name" => "Estonia", "contry-flag" => "flag-icon flag-icon-ee"),
					array("contry-name" => "Azerbaijan", "contry-flag" => "flag-icon flag-icon-az"),
					array("contry-name" => "Canada", "contry-flag" => "flag-icon flag-icon-ca"),
					array("contry-name" => "Morocco", "contry-flag" => "flag-icon flag-icon-ma"),
					array("contry-name" => "Ghana", "contry-flag" => "flag-icon flag-icon-gh"),
					array("contry-name" => "Argentina", "contry-flag" => "flag-icon flag-icon-ar"),
					array("contry-name" => "Uzbekistan", "contry-flag" => "flag-icon flag-icon-uz"),
					array("contry-name" => "Cameroon", "contry-flag" => "flag-icon flag-icon-cm"),
					array("contry-name" => "Chad", "contry-flag" => "flag-icon flag-icon-td"),
					array("contry-name" => "Germany", "contry-flag" => "flag-icon flag-icon-de"),
					array("contry-name" => "Lithuania", "contry-flag" => "flag-icon flag-icon-lt"),
					array("contry-name" => "Croatia", "contry-flag" => "flag-icon flag-icon-hr"),
					array("contry-name" => "Sweden", "contry-flag" => "flag-icon flag-icon-se"),
					array("contry-name" => "Iraq", "contry-flag" => "flag-icon flag-icon-iq"),
					array("contry-name" => "Netherlands", "contry-flag" => "flag-icon flag-icon-nl"),
					array("contry-name" => "Latvia", "contry-flag" => "flag-icon flag-icon-lv"),
					array("contry-name" => "Austria", "contry-flag" => "flag-icon flag-icon-at"),
					array("contry-name" => "Belarus", "contry-flag" => "flag-icon flag-icon-by"),
					array("contry-name" => "Thailand", "contry-flag" => "flag-icon flag-icon-th"),
					array("contry-name" => "Saudi Arabia", "contry-flag" => "flag-icon flag-icon-sa"),
					array("contry-name" => "Mexico", "contry-flag" => "flag-icon flag-icon-mx"),
					array("contry-name" => "Taiwan", "contry-flag" => "flag-icon flag-icon-tw"),
					array("contry-name" => "Spain", "contry-flag" => "flag-icon flag-icon-es"),
					array("contry-name" => "Iran", "contry-flag" => "flag-icon flag-icon-ir"),
					array("contry-name" => "Algeria", "contry-flag" => "flag-icon flag-icon-dz"),
					array("contry-name" => "Slovenia", "contry-flag" => "flag-icon flag-icon-si"),
					array("contry-name" => "Bangladesh", "contry-flag" => "flag-icon flag-icon-bd"),
					array("contry-name" => "Senegal", "contry-flag" => "flag-icon flag-icon-sn"),
					array("contry-name" => "Turkey", "contry-flag" => "flag-icon flag-icon-tr"),
					array("contry-name" => "Czech Republic", "contry-flag" => "flag-icon flag-icon-cz"),
					array("contry-name" => "Sri Lanka", "contry-flag" => "flag-icon flag-icon-lk"),
					array("contry-name" => "Peru", "contry-flag" => "flag-icon flag-icon-pe"),
					array("contry-name" => "Pakistan", "contry-flag" => "flag-icon flag-icon-pk"),
					array("contry-name" => "New Zealand", "contry-flag" => "flag-icon flag-icon-nz"),
					array("contry-name" => "Guinea", "contry-flag" => "flag-icon flag-icon-gn"),
					array("contry-name" => "Mali", "contry-flag" => "flag-icon flag-icon-ml"),
					array("contry-name" => "Venezuela", "contry-flag" => "flag-icon flag-icon-ve"),
					array("contry-name" => "Ethiopia", "contry-flag" => "flag-icon flag-icon-et"),
					array("contry-name" => "Mongolia", "contry-flag" => "flag-icon flag-icon-mn"),
					array("contry-name" => "Brazil", "contry-flag" => "flag-icon flag-icon-br"),
					array("contry-name" => "Afghanistan", "contry-flag" => "flag-icon flag-icon-af"),
					array("contry-name" => "Uganda", "contry-flag" => "flag-icon flag-icon-ug"),
					array("contry-name" => "Angola", "contry-flag" => "flag-icon flag-icon-ao"),
					array("contry-name" => "Cyprus", "contry-flag" => "flag-icon flag-icon-cy"),
					array("contry-name" => "France", "contry-flag" => "flag-icon flag-icon-fr"),
					array("contry-name" => "New Guinea", "contry-flag" => "flag-icon flag-icon-pg"),
					array("contry-name" => "Mozambique", "contry-flag" => "flag-icon flag-icon-mz"),
					array("contry-name" => "Nepal", "contry-flag" => "flag-icon flag-icon-np"),
					array("contry-name" => "Belgium", "contry-flag" => "flag-icon flag-icon-be"),
					array("contry-name" => "Bulgaria", "contry-flag" => "flag-icon flag-icon-bg"),
					array("contry-name" => "Hungary", "contry-flag" => "flag-icon flag-icon-hu"),
					array("contry-name" => "Moldova", "contry-flag" => "flag-icon flag-icon-md"),
					array("contry-name" => "Italy", "contry-flag" => "flag-icon flag-icon-it"),
					array("contry-name" => "Paraguay", "contry-flag" => "flag-icon flag-icon-py"),
					array("contry-name" => "Honduras", "contry-flag" => "flag-icon flag-icon-hn"),
					array("contry-name" => "Tunisia", "contry-flag" => "flag-icon flag-icon-tn"),
					array("contry-name" => "Nicaragua", "contry-flag" => "flag-icon flag-icon-ni"),
					array("contry-name" => "Timor-Leste", "contry-flag" => "flag-icon flag-icon-tl"),
					array("contry-name" => "Bolivia", "contry-flag" => "flag-icon flag-icon-bo"),
					array("contry-name" => "Costa Rica", "contry-flag" => "flag-icon flag-icon-cr"),
					array("contry-name" => "Guatemala", "contry-flag" => "flag-icon flag-icon-gt"),
					array("contry-name" => "UNITED ARAB EMIRATES", "contry-flag" => "flag-icon flag-icon-ae"),
					array("contry-name" => "Zimbabwe", "contry-flag" => "flag-icon flag-icon-zw"),
					array("contry-name" => "Puerto Rico", "contry-flag" => "flag-icon flag-icon-pr"),
					array("contry-name" => "Sudan", "contry-flag" => "flag-icon flag-icon-sd"),
					array("contry-name" => "Togo", "contry-flag" => "flag-icon flag-icon-tg"),
					array("contry-name" => "Kuwait", "contry-flag" => "flag-icon flag-icon-kw"),
					array("contry-name" => "El Salvador", "contry-flag" => "flag-icon flag-icon-sv"),
					array("contry-name" => "Libya", "contry-flag" => "flag-icon flag-icon-ly"),
					array("contry-name" => "Jamaica", "contry-flag" => "flag-icon flag-icon-jm"),
					array("contry-name" => "Trinidad and Tobago", "contry-flag" => "flag-icon flag-icon-tt"),
					array("contry-name" => "Ecuador", "contry-flag" => "flag-icon flag-icon-ec"),
					array("contry-name" => "Swaziland", "contry-flag" => "flag-icon flag-icon-sz"),
					array("contry-name" => "Oman", "contry-flag" => "flag-icon flag-icon-om"),
					array("contry-name" => "Bosnia and Herzegovina", "contry-flag" => "flag-icon flag-icon-ba"),
					array("contry-name" => "Dominican Republic", "contry-flag" => "flag-icon flag-icon-do"),
					array("contry-name" => "Qatar", "contry-flag" => "flag-icon flag-icon-qa"),
					array("contry-name" => "Cuba", "contry-flag" => "flag-icon flag-icon-cu"),
					array("contry-name" => "Panama", "contry-flag" => "flag-icon flag-icon-pa"),
					array("contry-name" => "Mauritania", "contry-flag" => "flag-icon flag-icon-mr"),
					array("contry-name" => "Sierra Leone", "contry-flag" => "flag-icon flag-icon-sl"),
					array("contry-name" => "Jordan", "contry-flag" => "flag-icon flag-icon-jo"),
					array("contry-name" => "Portugal", "contry-flag" => "flag-icon flag-icon-pt"),
					array("contry-name" => "Barbados", "contry-flag" => "flag-icon flag-icon-bb"),
					array("contry-name" => "Burundi", "contry-flag" => "flag-icon flag-icon-bi"),
					array("contry-name" => "Benin", "contry-flag" => "flag-icon flag-icon-bj"),
					array("contry-name" => "Brunei", "contry-flag" => "flag-icon flag-icon-bn"),
					array("contry-name" => "Bahamas", "contry-flag" => "flag-icon flag-icon-bs"),
					array("contry-name" => "Botswana", "contry-flag" => "flag-icon flag-icon-bw"),
					array("contry-name" => "Belize", "contry-flag" => "flag-icon flag-icon-bz"),
					array("contry-name" => "CAR", "contry-flag" => "flag-icon flag-icon-cf"),
					array("contry-name" => "Dominica", "contry-flag" => "flag-icon flag-icon-dm"),
					array("contry-name" => "Grenada", "contry-flag" => "flag-icon flag-icon-gd"),
					array("contry-name" => "Georgia", "contry-flag" => "flag-icon flag-icon-ge"),
					array("contry-name" => "Greece", "contry-flag" => "flag-icon flag-icon-gr"),
					array("contry-name" => "Guinea-Bissau", "contry-flag" => "flag-icon flag-icon-gw"),
					array("contry-name" => "Guyana", "contry-flag" => "flag-icon flag-icon-gy"),
					array("contry-name" => "Iceland", "contry-flag" => "flag-icon flag-icon-is"),
					array("contry-name" => "Comoros", "contry-flag" => "flag-icon flag-icon-km"),
					array("contry-name" => "Saint Kitts and Nevis", "contry-flag" => "flag-icon flag-icon-kn"),
					array("contry-name" => "Liberia", "contry-flag" => "flag-icon flag-icon-lr"),
					array("contry-name" => "Lesotho", "contry-flag" => "flag-icon flag-icon-ls"),
					array("contry-name" => "Malawi", "contry-flag" => "flag-icon flag-icon-mw"),
					array("contry-name" => "Namibia", "contry-flag" => "flag-icon flag-icon-na"),
					array("contry-name" => "Niger", "contry-flag" => "flag-icon flag-icon-ne"),
					array("contry-name" => "Rwanda", "contry-flag" => "flag-icon flag-icon-rw"),
					array("contry-name" => "Slovakia", "contry-flag" => "flag-icon flag-icon-sk"),
					array("contry-name" => "Suriname", "contry-flag" => "flag-icon flag-icon-sr"),
					array("contry-name" => "Tajikistan", "contry-flag" => "flag-icon flag-icon-tj"),
					array("contry-name" => "Monaco", "contry-flag" => "flag-icon flag-icon-mc"),
					array("contry-name" => "Bahrain", "contry-flag" => "flag-icon flag-icon-bh"),
					array("contry-name" => "Reunion", "contry-flag" => "flag-icon flag-icon-re"),
					array("contry-name" => "Zambia", "contry-flag" => "flag-icon flag-icon-zm"),
					array("contry-name" => "Armenia", "contry-flag" => "flag-icon flag-icon-am"),
					array("contry-name" => "Somalia", "contry-flag" => "flag-icon flag-icon-so"),
					array("contry-name" => "Congo", "contry-flag" => "flag-icon flag-icon-cg"),
					array("contry-name" => "Chile", "contry-flag" => "flag-icon flag-icon-cl"),
					array("contry-name" => "Burkina Faso", "contry-flag" => "flag-icon flag-icon-bf"),
					array("contry-name" => "Lebanon", "contry-flag" => "flag-icon flag-icon-lb"),
					array("contry-name" => "Gabon", "contry-flag" => "flag-icon flag-icon-ga"),
					array("contry-name" => "Albania", "contry-flag" => "flag-icon flag-icon-al"),
					array("contry-name" => "Uruguay", "contry-flag" => "flag-icon flag-icon-uy"),
					array("contry-name" => "Mauritius", "contry-flag" => "flag-icon flag-icon-mu"),
					array("contry-name" => "Bhutan", "contry-flag" => "flag-icon flag-icon-bt"),
					array("contry-name" => "Maldives", "contry-flag" => "flag-icon flag-icon-mv"),
					array("contry-name" => "Guadeloupe", "contry-flag" => "flag-icon flag-icon-gp"),
					array("contry-name" => "Turkmenistan", "contry-flag" => "flag-icon flag-icon-tm"),
					array("contry-name" => "French Guiana", "contry-flag" => "flag-icon flag-icon-gf"),
					array("contry-name" => "Finland", "contry-flag" => "flag-icon flag-icon-fi"),
					array("contry-name" => "Saint Lucia", "contry-flag" => "flag-icon flag-icon-lc"),
					array("contry-name" => "Luxembourg", "contry-flag" => "flag-icon flag-icon-lu"),
					array("contry-name" => "Saint Pierre and Miquelon", "contry-flag" => "flag-icon flag-icon-vc"),
					array("contry-name" => "Equatorial Guinea", "contry-flag" => "flag-icon flag-icon-gq"),
					array("contry-name" => "Djibouti", "contry-flag" => "flag-icon flag-icon-dj"),
					array("contry-name" => "Saint Kitts and Nevis", "contry-flag" => "flag-icon flag-icon-ag"),
					array("contry-name" => "Cayman Islands", "contry-flag" => "flag-icon flag-icon-ky"),
					array("contry-name" => "Montenegro", "contry-flag" => "flag-icon flag-icon-me"),
					array("contry-name" => "Denmark", "contry-flag" => "flag-icon flag-icon-dk"),
					array("contry-name" => "Switzerland", "contry-flag" => "flag-icon flag-icon-ch"),
					array("contry-name" => "Norway", "contry-flag" => "flag-icon flag-icon-no"),
					array("contry-name" => "Australia", "contry-flag" => "flag-icon flag-icon-au"),
					array("contry-name" => "Eritrea", "contry-flag" => "flag-icon flag-icon-er"),
					array("contry-name" => "South Sudan", "contry-flag" => "flag-icon flag-icon-ss"),
					array("contry-name" => "Sao Tome and Principe", "contry-flag" => "flag-icon flag-icon-st"),
					array("contry-name" => "Aruba", "contry-flag" => "flag-icon flag-icon-aw"),
					array("contry-name" => "Montserrat", "contry-flag" => "flag-icon flag-icon-ms"),
					array("contry-name" => "Anguilla", "contry-flag" => "flag-icon flag-icon-ai"),
					array("contry-name" => "Northern Macedonia", "contry-flag" => "flag-icon flag-icon-mk"),
					array("contry-name" => "Republic of Seychelles", "contry-flag" => "flag-icon flag-icon-sc"),
					array("contry-name" => "New Caledonia", "contry-flag" => "flag-icon flag-icon-nc"),
					array("contry-name" => "Cape Verde", "contry-flag" => "flag-icon flag-icon-cv"),
					array("contry-name" => "USA (Real)", "contry-flag" => "flag-icon flag-icon-vi"),
					array("contry-name" => "South Korea", "contry-flag" => "flag-icon flag-icon-kr"),
					array("contry-name" => "Fiji", "contry-flag" => "flag-icon flag-icon-fj"),
					array("contry-name" => "Bermuda", "contry-flag" => "flag-icon flag-icon-bm"),
				);
			foreach ($country_arr as $k => $country_arr) {
				if ($k == 0) {
					$activeClss = 'active-country';
				} else {
					$activeClss = '';
				}
				$html .= '<div class="col-lg-6 col-12 container-box">
                            <div class="country-box ' . $activeClss . '" data-country-id="0"
                              data-country-icon="' . $country_arr["contry-flag"] . '">
                              <input type="radio" class="choice-country-input" name="country" value="0" data-value="0"
                                data-country-icon="flag-icon flag-icon-ru">

                              <a href="javascript:;"
                                class="add-to-wishlist js-add-wishlist-country" title="' . $country_arr["contry-name"] . '"
                                data-country-id="0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 18.35">
                                  <lineargradient id="svg-gradient">
                                    <stop class="main-stop" offset="0%"></stop>
                                    <stop class="alt-stop" offset="100%"></stop>
                                  </lineargradient>
                                  <path
                                    d="M14.5,0A6,6,0,0,0,10,2.09,6,6,0,0,0,5.5,0,5.5,5.5,0,0,0,0,5.5C0,9.67,4.91,13.71,6.28,15,7.86,16.46,10,18.35,10,18.35s2.14-1.89,3.72-3.36C15.09,13.71,20,9.67,20,5.5A5.5,5.5,0,0,0,14.5,0Z">
                                  </path>
                                </svg>
                              </a>
                              <div class="info-country">
                                <span class="' . $country_arr["contry-flag"] . '"></span>
                                <p class="contry-name">' . $country_arr["contry-name"] . '</p>
                              </div>
                            </div>
                          </div>';
			}

			echo  $html;
		}

		function  services_list($services_shortcode = '')
		{

			$services = array(
				'vk' => 'Вконтакте',
				'ok' => 'Ok.ru',
				'wa' => 'Whatsapp',
				'vi' => 'Viber',
				'tg' => 'Telegram',
				'wb' => 'WeChat',
				'go' => 'Google,youtube,Gmail',
				'av' => 'avito',
				'fb' => 'facebook',
				'tw' => 'Twitter',
				'ub' => 'Uber',
				'qw' => 'Киви',
				'gt' => 'Gett',
				'sn' => 'OLX',
				'ig' => 'Instagram',
				'ss' => 'Hezzl',
				'ym' => 'Юла',
				'ma' => 'Mail.ru',
				'mm' => 'Microsoft',
				'uk' => 'Airbnb',
				'me' => 'Line msg',
				'mb' => 'Yahoo',
				'we' => 'ДругВокруг',
				'bd' => 'X5ID',
				'kp' => 'HQ Trivia',
				'dt' => 'Delivery Club',
				'ya' => 'Яндекс',
				'mt' => 'Steam',
				'oi' => 'Tinder',
				'fd' => 'Mamba',
				'zz' => 'Dent',
				'kt' => 'KakaoTalk',
				'pm' => 'AOL',
				'tn' => 'LinkedIN',
				'qq' => 'Tencent QQ',
				'mg' => 'Магнит',
				'pf' => 'pof.com',
				'yl' => 'Yalla',
				'kl' => 'kolesa.kz',
				'po' => 'premium.one',
				'nv' => 'Naver',
				'nf' => 'Netflix',
				'iq' => 'icq',
				'ob' => 'Onlinerby',
				'kb' => 'kufarby',
				'im' => 'Imo',
				'mc' => 'Michat',
				'ds' => 'Discord',
				'vv' => 'Seosprint',
				'ji' => 'Monobank',
				'lf' => 'TikTok/Douyin',
				'hu' => 'Ukrnet',
				'wg' => 'Skout',
				'rz' => 'EasyPay',
				'vf' => 'Q12 Trivia',
				'ny' => 'Pyro Music',
				'rr' => 'Wolt',
				'fe' => 'CliQQ',
				'la' => 'ssoidnet',
				'zh' => 'Zoho',
				'gp' => 'Ticketmaster',
				'am' => 'Amazon',
				'ly' => 'Olacabs',
				'tc' => 'Rambler',
				'dp' => 'ProtonMail',
				'pg' => 'NRJ Music Awards',
				'yf' => 'Citymobil',
				'op' => 'MIRATORG',
				'fx' => 'PGbonus',
				'qr' => 'MEGA',
				'yk' => 'СпортМастер',
				'ls' => 'Careem',
				'bl' => 'BIGO LIVE',
				'mu' => 'MyMusicTaste',
				'fu' => 'Snapchat',
				'bf' => 'Keybase',
				'sg' => 'OZON',
				'uu' => 'Wildberries',
				'ua' => 'BlaBlaCar',
				'ab' => 'Alibaba',
				'iv' => 'Inboxlv',
				'zy' => 'Nttgame',
				'gd' => 'Surveytime',
				'fy' => 'Mylove',
				'ce' => 'mosru',
				'tl' => 'Truecaller',
				'hm' => 'Globus',
				'tx' => 'Bolt',
				'ka' => 'Shopee',
				'pl' => 'Перекресток',
				'ip' => 'Burger King',
				'cm' => 'Prom',
				'hw' => 'AliPay',
				'de' => 'Karusel',
				'jc' => 'IVI',
				'rl' => 'inDriver',
				'df' => 'Happn',
				'ui' => 'RuTube',
				'up' => 'Magnolia',
				'nz' => 'Foodpanda',
				'kf' => 'Weibo',
				'ri' => 'BillMill',
				'cc' => 'Quipp',
				'lr' => 'Okta',
				'za' => 'JDcom',
				'da' => 'MTS CashBack',
				'ug' => 'Fiqsy',
				'sq' => 'KuCoinPlay',
				'zr' => 'Papara',
				'xv' => 'Wish',
				'cx' => 'Icrypex',
				'cw' => 'PaddyPower',
				'li' => 'Baidu',
				'dz' => 'Dominos Pizza',
				'xz' => 'paycell',
				'rd' => 'Lenta',
				'qb' => 'Payberry',
				'hz' => 'Drom',
				'gl' => 'GlobalTel',
				'zk' => 'Deliveroo',
				'ia' => 'Socios',
				'xl' => 'Wmaraci',
				'yi' => 'Yemeksepeti',
				'ew' => 'Nike',
				'ae' => 'myGLO',
				'gb' => 'YouStar',
				'cy' => 'РСА',
				'qm' => 'RosaKhutor',
				'dh' => 'eBay',
				'yb' => 'Квартплата+',
				'qe' => 'GG',
				'yw' => 'Grindr',
				'uz' => 'OffGamers',
				'gx' => 'Hepsiburadacom',
				're' => 'Coinbase',
				'tj' => 'dbrUA',
				'ts' => 'PayPal',
				'rt' => 'hily',
				'sf' => 'SneakersnStuff',
				'sv' => 'Dostavista',
				'qi' => '23red',
				'bz' => 'Blizzard',
				'db' => 'ezbuy',
				'vw' => 'CoinField',
				'zl' => 'Airtel',
				'wf' => 'YandexGo',
				'lw' => 'MrGreen',
				'co' => 'Rediffmail',
				'ey' => 'miloan',
				'ge' => 'Paytm',
				'os' => 'Dhani',
				'ql' => 'CMTcuzdan',
				'cq' => 'Mercado',
				'xk' => 'DiDi',
				'py' => 'Monese',
				'rv' => 'Kotak811',
				'jl' => 'Hopi',
				'pr' => 'Trendyol',
				'pu' => 'Justdating',
				'dk' => 'Pairs',
				'fm' => 'Touchance',
				'ph' => 'SnappFood',
				'sw' => 'NCsoft',
				'nr' => 'Tosla',
				'hy' => 'Ininal',
				'tr' => 'Paysend',
				'pq' => 'CDkeys',
				'ff' => 'AVON',
				'sd' => 'dodopizza',
				'ry' => 'McDonalds',
				'le' => 'E bike Gewinnspiel',
				'hr' => 'JKF',
				'qa' => 'MyFishka',
				'wc' => 'Craigslist',
				'kw' => 'Foody',
				'jg' => 'Grab',
				'mj' => 'Zalo',
				'eu' => 'LiveScore',
				'll' => '888casino',
				'ed' => 'Gamer',
				'pp' => 'Huya',
				'th' => 'WestStein',
				'xr' => 'Tango',
				'iz' => 'Global24',
				'tk' => 'МВидео',
				'rx' => 'Sheerid',
				'ki' => '99app',
				'my' => 'CAIXA',
				'zm' => 'OfferUp',
				'tq' => 'Swvl',
				'au' => 'Haraj',
				'ei' => 'Taksheel',
				'rp' => 'hamrahaval',
				'pa' => 'Gamekit',
				'fs' => 'Şikayet var',
				'ul' => 'Getir',
				'cf' => 'irancell',
				'bt' => 'Alfa',
				'ud' => 'Disney Hotstar',
				'qu' => 'Agroinform',
				'un' => 'humblebundle',
				'rm' => 'Faberlic',
				'uo' => 'CafeBazaar',
				'ti' => 'cryptocom',
				'nk' => 'Gittigidiyor',
				'jm' => 'mzadqatar',
				'lp' => 'Algida',
				'si' => 'Cita Previa',
				'fj' => 'Potato Chat',
				'pt' => 'Bitaqaty',
				'qc' => 'Праймериз 2020',
				'yo' => 'Amasia',
				've' => 'Dream11',
				'qh' => 'Oriflame',
				'iu' => 'Bykea',
				'ib' => 'Immowelt',
				'zv' => 'Digikala',
				'jb' => 'Wing Money',
				'vn' => 'Yaay',
				'wn' => 'GameArena',
				'bj' => 'Вита экспресс',
				'st' => 'Ашан',
				'ev' => 'Picpay',
				'qn' => 'Blued',
				'cd' => 'SpotHit',
				'vo' => 'Brand20ua',
				'il' => 'IQOS',
				'dx' => 'Powerkredite',
				'el' => 'Bisu',
				'dn' => 'Paxful',
				'lk' => 'PurePlatfrom',
				'vc' => 'Banqi',
				'wj' => '1хbet',
				'wk' => 'Mobile01',
				'jj' => 'Aitu',
				'an' => 'Adidas',
				'jr' => 'Самокат',
				'nb' => 'Верный',
				'gv' => 'Humta',
				'dw' => 'Divar',
				'gj' => 'Carousell',
				'hc' => 'MOMO',
				'uf' => 'Eneba',
				'kn' => 'Verse',
				'qd' => 'Taobao',
				'hn' => '1688',
				'zf' => 'OnTaxi',
				'gi' => 'Hotline',
				'uc' => 'Tatneft',
				'mn' => 'RRSA',
				'ak' => 'Douyu',
				'cp' => 'Uklon',
				'qo' => 'Moneylion',
				'wx' => 'Apple',
				'et' => 'Clubhouse',
				'px' => 'Nifty',
				'jh' => 'PingPong',
				'lb' => 'Mailru Group',
				'md' => 'Банки',
				'lt' => 'BitClout',
				'sk' => 'Skroutz',
				'oh' => 'MapleSEA',
				'km' => 'Rozetka',
				'af' => 'GalaxyWin',
				'tt' => 'Ziglu',
				'jf' => 'Likee',
				'az' => 'CityBase',
				'yn' => 'Allegro',
				'wl' => 'YouGotaGift',
				'dl' => 'Lazada',
				'gc' => 'TradingView',
				'cn' => 'Fiverr',
				'ou' => 'Gabi',
				'vp' => 'Kwai',
				'rj' => 'Детский мир',
				'uh' => 'Yubo',
				'es' => 'iQIYI',
				'be' => 'СберМегаМаркет',
				'aq' => 'Glovo',
				'pd' => 'IFood',
				'zw' => 'Quack',
				'gm' => 'Mocospace',
				'fi' => 'Dundle',
				'hg' => 'Switips',
				'qz' => 'Faceit',
				'gz' => 'LYKA',
				'jq' => 'Paysafecard',
				'ue' => 'Onet',
				'xf' => 'LightChat',
				'bp' => 'GoFundMe',
				'vy' => 'Meta',
				'ea' => 'JamesDelivery',
				'hj' => 'Столото',
				'sh' => 'ВкусВилл',
				'vg' => 'ShellBox',
				'qf' => 'RedBook',
				'nq' => 'Trip',
				'ww' => 'BIP',
				'ke' => 'Эльдорадо',
				'qj' => 'Whoosh',
				'ol' => 'KazanExpress',
				'tm' => 'Akulaku',
				'ra' => 'KeyPay',
				'xj' => 'СберМаркет',
				'vd' => 'Betfair',
				'ni' => 'Gojek',
				'mr' => 'Fastmail',
				'hx' => 'AliExpress',
				'bv' => 'Metro',
				'sj' => 'HandyPick',
				'td' => 'ChaingeFinance',
				'dm' => 'Iwplay',
				'xs' => 'GroupMe',
				'kz' => 'NimoTV',
				'nu' => 'Stripe',
				'kr' => 'Eyecon',
				'pz' => 'Lidl',
				'hb' => 'Twitch',
				'xe' => 'GalaxyChat',
				'io' => 'ЗдравСити',
				'ad' => 'Iti',
				'zg' => 'Setel',
				'ij' => 'Revolut',
				'sl' => 'СберАптека',
				'wp' => '163СOM',
				'en' => 'Hermes',
				'zo' => 'Kaggle',
				'vx' => 'HeyBox',
				'hl' => 'Band',
				'lq' => 'Potato',
				'uj' => 'СhampionСasino',
				'ga' => 'Roposo',
				'bo' => 'Wise',
				'fz' => 'KFC',
				'vm' => 'OkCupid',
				'ch' => 'Pocket52',
				'yp' => 'Payzapp',
				'cs' => 'AgriDevelop',
				'yg' => 'CourseHero',
				'lj' => 'Santander',
				'oz' => 'Poshmark',
				'wh' => 'TanTan',
				'wt' => 'IZI',
				'og' => 'Okko',
				'xq' => 'MPL',
				'xh' => 'OVO',
				'kc' => 'Vinted',
				'hk' => '4Fun',
				'yz' => 'Около',
				'eo' => 'Sizeer',
				'ft' => 'Букмекерские',
				'tf' => 'Noon',
				'lv' => 'Megogo',
				'dy' => 'Zomato',
				'lx' => 'DewuPoison',
				'nn' => 'Giftcloud',
				'zs' => 'Bilibili',
				'kx' => 'Vivo',
				'ee' => 'Twilio',
				'tp' => 'IndiaGold',
				'ku' => 'RoyalWin',
				'ca' => 'SuperS',
				'va' => 'SportGully',
				'ej' => 'MrQ',
				'yu' => 'Xiaomi',
				'np' => 'Siply',
				'uy' => 'Meliuz',
				'sb' => 'Lamoda',
				'zd' => 'Zilch',
				'zn' => 'Biedronka',
				'je' => 'Nanovest',
				'gw' => 'CallApp',
				'qk' => 'Bit',
				'xi' => 'InFund',
				'ju' => 'Indomaret',
				'dv' => 'NoBroker',
				'mk' => 'LongHu',
				'iy' => 'FoodHub',
				'ys' => 'ZCity',
				'ax' => 'CrefisaMais',
				'aw' => 'Taikang',
				'ne' => 'Coindcx',
				'fr' => 'Dana',
				'mf' => 'Weidian',
				'he' => 'Mewt',
				'od' => 'FWDMAX',
				'gu' => 'Fora',
				'xd' => 'Tokopedia',
				'gf' => 'GoogleVoice',
				'yy' => 'Venmo',
				'dg' => 'Mercari',
				'oc' => 'DealShare',
				'sm' => 'YoWin',
				'aj' => 'OneAset',
				'ta' => 'Wink',
				'cj' => 'Dotz',
				'vz' => 'Hinge',
				'yv' => 'IPLwin',
				'xy' => 'Depop',
				'bn' => 'Alfagift',
				'ik' => 'GuruBets',
				'rk' => 'Fotka',
				'vj' => 'Stormgain',
				'sz' => 'Pivko24',
				'cr' => 'TenChat',
				'oq' => 'Vlife',
				'of' => 'Urent',
				'gk' => 'AptekaRU',
				'bb' => 'LazyPay',
				'mh' => 'Ашан',
				'hq' => 'Magicbricks',
				'fw' => '99acres',
				'cu' => '炙热星河',
				'mi' => 'Zupee',
				'zi' => 'LoveLocal',
				'aa' => 'Probo',
				'rf' => 'Akudo',
				'so' => 'RummyWealth',
				'zq' => 'IndiaPlays',
				'xt' => 'Flipkart',
				'ln' => 'Grofers',
				'us' => 'IRCTC',
				'kg' => 'FreeChargeApp',
				'gh' => 'GyFTR',
				'lh' => '24betting',
				'wo' => 'Parkplus',
				'rg' => 'Porbet',
				'cz' => 'Getmega',
				'tu' => 'Lyft',
				'ih' => 'TeenPattiStarpro',
				'rh' => 'Ace2Three',
				'xb' => 'RummyOla',
				'fc' => 'PharmEasy',
				'hi' => 'JungleeRummy',
				'fl' => 'RummyLoot',
				'oy' => 'CashFly',
				'jx' => 'Swiggy',
				'rc' => 'Skype',
				'eg' => 'ContactSys',
				'ba' => 'Expressmoney',
				'oe' => 'Codashop',
				'gs' => 'SamsungShop',
				'ml' => 'ApostaGanha',
				'mq' => 'GMNG',
				'jt' => 'TurkiyePetrolleri',
				'sp' => 'HappyFresh',
				'sa' => 'AGIBANK',
				'rb' => 'Tick',
				'mo' => 'Bumble',
				'do' => 'Leboncoin',
				'ac' => 'DoorDash',
				'uv' => 'BinBin',
				'ru' => 'HOP',
				'wq' => 'Leboncoin1',
				'yj' => 'eWallet',
				'jz' => 'Kaya',
				'su' => 'LOCO',
				'ur' => 'MyDailyCash',
				'xu' => 'RecargaPay',
				'ot' => 'Любой другой',
			);

			if (!empty($services_shortcode)) {
				return $services[$services_shortcode];
			}

			return $services;
		}

		function  get_services_ajax($country)
		{
			
			$get_services = services_list();

			$country = $country ? $country : 'Russia';
			
			
			if (_sms('sms_api')) {
				
				$result = post('https://smshub.org/stubs/handler_api.php?api_key=' . _sms('sms_api') . '&action=getNumbersStatus&country=' . get_country_code($country));
				$result = json_decode($result);
				
				foreach ($result as $k => $res) {


					$short = explode('_', $k);
					// var_dump($k, $short[0]);

					$i++;
					if ($i > 80) {
						break;
					}
					// var_dump($service, $short[0] == $service);

					if (!empty($get_services[$short[0]])) {

						// $return_code[] = $k2;
						$price_quantity = get_number_price($country, $short[0]);

						if ($price_quantity['quantity'] > 0) {

							$return_code .= '<div class="service-box is-serached" data-id="' . $short[0] . '"> <div data-value="' . $short[0] . '" class="input-service"></div> <div class="service"> <p class="service-heart bg-disabled_like js-add-wishlist-service " data-box="' . $short[0] . '"></p> <p class="service-icon"> <img class="service-icon-img" src="' . get_template_directory_uri() . '/assets/img/sms/' . $short[0] . '.png" > </p> <p class="service-name"> <span class="service-name-label">' . $get_services[$short[0]] . '</span> </p> </div> <div class="more-num"> <div class="number-box"> <p class="number" data-quantity="">Qty: ' . $price_quantity['quantity'] . '</p> </div> <div class="cost-box" data-cost=""> <p class="cost"> <span>' . $price_quantity['price'] . ' $</span> <span class="img"><img src="' . get_template_directory_uri() . '/assets/img/sms/buc.png" alt="alt text"></span> </p> </div> </div> </div>';
						
						}
					}
				}

				return $return_code;
			}
		}



		//搜索全部服务和价格
		function  get_services_home_page()
		{
			$country = 'Russia';
			$result = post('https://smshub.org/stubs/handler_api.php?api_key=' . _sms('sms_api') . '&action=getPrices&country=' . get_country_code($country));
			$result = json_decode($result);
			foreach ($result as $country) {

				foreach ($country as $servicesName => $servicesContent) {
					$cheapest_price = '';
					$price_quantity = '';
					foreach ($servicesContent as $price => $num) {
						if (!$cheapest_price) {
							$cheapest_price = $price;
							$price_quantity = $num;
						}

						if ($cheapest_price > $price) {
							$cheapest_price = $price;
							$price_quantity = $num;
						}
					}

					$return_code .= '<div class="service-box is-serached" data-id="' . $servicesName . '"> <div data-value="' . $servicesName . '" class="input-service"></div> <div class="service"> <p class="service-heart bg-disabled_like js-add-wishlist-service " data-box="' . $servicesName . '"></p> <p class="service-icon"> <img class="service-icon-img" src="' . get_template_directory_uri() . '/assets/img/sms/' . $servicesName . '.png" > </p> <p class="service-name"> <span class="service-name-label">' . services_list($servicesName) . '</span> </p> </div> <div class="more-num"> <div class="number-box"> <p class="number" data-quantity="">Qty: ' . $price_quantity . '</p> </div> <div class="cost-box" data-cost=""> <p class="cost"> 
					<span>' . round($cheapest_price * 0.025,2) . ' $</span> <span class="img"><img src="' . get_template_directory_uri() . '/assets/img/sms/buc.png" alt="alt text"></span> </p> </div> </div> </div>';
				}
				return $return_code;
			}
		}



		function get_number_price($country, $service)
		{

			$result = post('https://smshub.org/stubs/handler_api.php?api_key=' . _sms('sms_api') . '&action=getPrices&service=' . $service . '&country=' . get_country_code($country));


			$result = json_decode($result);

			foreach ($result as $country) {
				foreach ($country as $services) {
					foreach ($services as $price => $num) {

						if (!$cheapest_price) {
							$cheapest_price = $price;
							$price_quantity = $num;
						}

						if ($cheapest_price > $price) {
							$cheapest_price = $price;
							$price_quantity = $num;
						}
					}
				}
			}
			//调整价格并返回结果
			return ['price' => round($cheapest_price * 0.025,2), 'quantity' => $price_quantity];
		}




		function create_api_secret()
		{
			global $current_user;
			$user_id     = $current_user->ID; //用户ID
			if (!$user_id) {
				wp_redirect(wp_login_url());
				exit;
			}

			$key = bin2hex(random_bytes(32));

			$args = array(
				'meta_key' => 'user_api_key',
				'value'     => $key,
				'compare'   => 'LIKE'
			);

			$user_query = new WP_User_Query($args);

			if (!empty($user_query)) {

				$key = create_api_secret();
			}

			return $key;
		}


		function copy_png()
		{
			$png = array(
				'fx',
				'gk',
				'dt',
				'ce',
				'jr',
				'cu',
				'io',
				'ba',
				'mi',
				'lx',
				'og',
				'of',
				'ot',
				'rl',
				'rc',
				'qq',
				'sd',
				'xj',
				'vj',
				'yl',
				'yz',
			);
			foreach ($png as $k => $v) {
				$res = copy("https://smshub.org/assets/ico/" . $v . '0.png', get_template_directory_uri() . '/assets/img/sms/' . $v . 'png');
			}
		}
		// add_action('init', 'copy_png');

		function get_operator_service_data()
		{

			// 
			$operator = $_POST['operator'];
			$country = $_POST['country'];
			if (!empty($operator) && !empty($country)) {
				$result = post('https://smshub.org/stubs/handler_api.php?api_key=' . _sms('sms_api') . '&action=getNumbersStatus&operator=' . $operator . '&country=' . get_country_code($country));
				// $result = post('https://smshub.org/stubs/handler_api.php?api_key=' . _sms('sms_api') . '&action=getNumbersStatus&operator=' . str_replace(' ', '', $_POST['operator']) . '&country=' . $_POST['country']);


				$result = json_decode($result);

				$services = array(
					'vk' => 'Вконтакте',
					'ok' => 'Ok.ru',
					'wa' => 'Whatsapp',
					'vi' => 'Viber',
					'tg' => 'Telegram',
					'wb' => 'WeChat',
					'go' => 'Google,youtube,Gmail',
					'av' => 'avito',
					'fb' => 'facebook',
					'tw' => 'Twitter',
					'ub' => 'Uber',
					'qw' => 'Киви',
					'gt' => 'Gett',
					'sn' => 'OLX',
					'ig' => 'Instagram',
					'ss' => 'Hezzl',
					'ym' => 'Юла',
					'ma' => 'Mail.ru',
					'mm' => 'Microsoft',
					'uk' => 'Airbnb',
					'me' => 'Line msg',
					'mb' => 'Yahoo',
					'we' => 'ДругВокруг',
					'bd' => 'X5ID',
					'kp' => 'HQ Trivia',
					'dt' => 'Delivery Club',
					'ya' => 'Яндекс',
					'mt' => 'Steam',
					'oi' => 'Tinder',
					'fd' => 'Mamba',
					'zz' => 'Dent',
					'kt' => 'KakaoTalk',
					'pm' => 'AOL',
					'tn' => 'LinkedIN',
					'qq' => 'Tencent QQ',
					'mg' => 'Магнит',
					'pf' => 'pof.com',
					'yl' => 'Yalla',
					'kl' => 'kolesa.kz',
					'po' => 'premium.one',
					'nv' => 'Naver',
					'nf' => 'Netflix',
					'iq' => 'icq',
					'ob' => 'Onlinerby',
					'kb' => 'kufarby',
					'im' => 'Imo',
					'mc' => 'Michat',
					'ds' => 'Discord',
					'vv' => 'Seosprint',
					'ji' => 'Monobank',
					'lf' => 'TikTok/Douyin',
					'hu' => 'Ukrnet',
					'wg' => 'Skout',
					'rz' => 'EasyPay',
					'vf' => 'Q12 Trivia',
					'ny' => 'Pyro Music',
					'rr' => 'Wolt',
					'fe' => 'CliQQ',
					'la' => 'ssoidnet',
					'zh' => 'Zoho',
					'gp' => 'Ticketmaster',
					'am' => 'Amazon',
					'ly' => 'Olacabs',
					'tc' => 'Rambler',
					'dp' => 'ProtonMail',
					'pg' => 'NRJ Music Awards',
					'yf' => 'Citymobil',
					'op' => 'MIRATORG',
					'fx' => 'PGbonus',
					'qr' => 'MEGA',
					'yk' => 'СпортМастер',
					'ls' => 'Careem',
					'bl' => 'BIGO LIVE',
					'mu' => 'MyMusicTaste',
					'fu' => 'Snapchat',
					'bf' => 'Keybase',
					'sg' => 'OZON',
					'uu' => 'Wildberries',
					'ua' => 'BlaBlaCar',
					'ab' => 'Alibaba',
					'iv' => 'Inboxlv',
					'zy' => 'Nttgame',
					'gd' => 'Surveytime',
					'fy' => 'Mylove',
					'ce' => 'mosru',
					'tl' => 'Truecaller',
					'hm' => 'Globus',
					'tx' => 'Bolt',
					'ka' => 'Shopee',
					'pl' => 'Перекресток',
					'ip' => 'Burger King',
					'cm' => 'Prom',
					'hw' => 'AliPay',
					'de' => 'Karusel',
					'jc' => 'IVI',
					'rl' => 'inDriver',
					'df' => 'Happn',
					'ui' => 'RuTube',
					'up' => 'Magnolia',
					'nz' => 'Foodpanda',
					'kf' => 'Weibo',
					'ri' => 'BillMill',
					'cc' => 'Quipp',
					'lr' => 'Okta',
					'za' => 'JDcom',
					'da' => 'MTS CashBack',
					'ug' => 'Fiqsy',
					'sq' => 'KuCoinPlay',
					'zr' => 'Papara',
					'xv' => 'Wish',
					'cx' => 'Icrypex',
					'cw' => 'PaddyPower',
					'li' => 'Baidu',
					'dz' => 'Dominos Pizza',
					'xz' => 'paycell',
					'rd' => 'Lenta',
					'qb' => 'Payberry',
					'hz' => 'Drom',
					'gl' => 'GlobalTel',
					'zk' => 'Deliveroo',
					'ia' => 'Socios',
					'xl' => 'Wmaraci',
					'yi' => 'Yemeksepeti',
					'ew' => 'Nike',
					'ae' => 'myGLO',
					'gb' => 'YouStar',
					'cy' => 'РСА',
					'qm' => 'RosaKhutor',
					'dh' => 'eBay',
					'yb' => 'Квартплата+',
					'qe' => 'GG',
					'yw' => 'Grindr',
					'uz' => 'OffGamers',
					'gx' => 'Hepsiburadacom',
					're' => 'Coinbase',
					'tj' => 'dbrUA',
					'ts' => 'PayPal',
					'rt' => 'hily',
					'sf' => 'SneakersnStuff',
					'sv' => 'Dostavista',
					'qi' => '23red',
					'bz' => 'Blizzard',
					'db' => 'ezbuy',
					'vw' => 'CoinField',
					'zl' => 'Airtel',
					'wf' => 'YandexGo',
					'lw' => 'MrGreen',
					'co' => 'Rediffmail',
					'ey' => 'miloan',
					'ge' => 'Paytm',
					'os' => 'Dhani',
					'ql' => 'CMTcuzdan',
					'cq' => 'Mercado',
					'xk' => 'DiDi',
					'py' => 'Monese',
					'rv' => 'Kotak811',
					'jl' => 'Hopi',
					'pr' => 'Trendyol',
					'pu' => 'Justdating',
					'dk' => 'Pairs',
					'fm' => 'Touchance',
					'ph' => 'SnappFood',
					'sw' => 'NCsoft',
					'nr' => 'Tosla',
					'hy' => 'Ininal',
					'tr' => 'Paysend',
					'pq' => 'CDkeys',
					'ff' => 'AVON',
					'sd' => 'dodopizza',
					'ry' => 'McDonalds',
					'le' => 'E bike Gewinnspiel',
					'hr' => 'JKF',
					'qa' => 'MyFishka',
					'wc' => 'Craigslist',
					'kw' => 'Foody',
					'jg' => 'Grab',
					'mj' => 'Zalo',
					'eu' => 'LiveScore',
					'll' => '888casino',
					'ed' => 'Gamer',
					'pp' => 'Huya',
					'th' => 'WestStein',
					'xr' => 'Tango',
					'iz' => 'Global24',
					'tk' => 'МВидео',
					'rx' => 'Sheerid',
					'ki' => '99app',
					'my' => 'CAIXA',
					'zm' => 'OfferUp',
					'tq' => 'Swvl',
					'au' => 'Haraj',
					'ei' => 'Taksheel',
					'rp' => 'hamrahaval',
					'pa' => 'Gamekit',
					'fs' => 'Şikayet var',
					'ul' => 'Getir',
					'cf' => 'irancell',
					'bt' => 'Alfa',
					'ud' => 'Disney Hotstar',
					'qu' => 'Agroinform',
					'un' => 'humblebundle',
					'rm' => 'Faberlic',
					'uo' => 'CafeBazaar',
					'ti' => 'cryptocom',
					'nk' => 'Gittigidiyor',
					'jm' => 'mzadqatar',
					'lp' => 'Algida',
					'si' => 'Cita Previa',
					'fj' => 'Potato Chat',
					'pt' => 'Bitaqaty',
					'qc' => 'Праймериз 2020',
					'yo' => 'Amasia',
					've' => 'Dream11',
					'qh' => 'Oriflame',
					'iu' => 'Bykea',
					'ib' => 'Immowelt',
					'zv' => 'Digikala',
					'jb' => 'Wing Money',
					'vn' => 'Yaay',
					'wn' => 'GameArena',
					'bj' => 'Вита экспресс',
					'st' => 'Ашан',
					'ev' => 'Picpay',
					'qn' => 'Blued',
					'cd' => 'SpotHit',
					'vo' => 'Brand20ua',
					'il' => 'IQOS',
					'dx' => 'Powerkredite',
					'el' => 'Bisu',
					'dn' => 'Paxful',
					'lk' => 'PurePlatfrom',
					'vc' => 'Banqi',
					'wj' => '1хbet',
					'wk' => 'Mobile01',
					'jj' => 'Aitu',
					'an' => 'Adidas',
					'jr' => 'Самокат',
					'nb' => 'Верный',
					'gv' => 'Humta',
					'dw' => 'Divar',
					'gj' => 'Carousell',
					'hc' => 'MOMO',
					'uf' => 'Eneba',
					'kn' => 'Verse',
					'qd' => 'Taobao',
					'hn' => '1688',
					'zf' => 'OnTaxi',
					'gi' => 'Hotline',
					'uc' => 'Tatneft',
					'mn' => 'RRSA',
					'ak' => 'Douyu',
					'cp' => 'Uklon',
					'qo' => 'Moneylion',
					'wx' => 'Apple',
					'et' => 'Clubhouse',
					'px' => 'Nifty',
					'jh' => 'PingPong',
					'lb' => 'Mailru Group',
					'md' => 'Банки',
					'lt' => 'BitClout',
					'sk' => 'Skroutz',
					'oh' => 'MapleSEA',
					'km' => 'Rozetka',
					'af' => 'GalaxyWin',
					'tt' => 'Ziglu',
					'jf' => 'Likee',
					'az' => 'CityBase',
					'yn' => 'Allegro',
					'wl' => 'YouGotaGift',
					'dl' => 'Lazada',
					'gc' => 'TradingView',
					'cn' => 'Fiverr',
					'ou' => 'Gabi',
					'vp' => 'Kwai',
					'rj' => 'Детский мир',
					'uh' => 'Yubo',
					'es' => 'iQIYI',
					'be' => 'СберМегаМаркет',
					'aq' => 'Glovo',
					'pd' => 'IFood',
					'zw' => 'Quack',
					'gm' => 'Mocospace',
					'fi' => 'Dundle',
					'hg' => 'Switips',
					'qz' => 'Faceit',
					'gz' => 'LYKA',
					'jq' => 'Paysafecard',
					'ue' => 'Onet',
					'xf' => 'LightChat',
					'bp' => 'GoFundMe',
					'vy' => 'Meta',
					'ea' => 'JamesDelivery',
					'hj' => 'Столото',
					'sh' => 'ВкусВилл',
					'vg' => 'ShellBox',
					'qf' => 'RedBook',
					'nq' => 'Trip',
					'ww' => 'BIP',
					'ke' => 'Эльдорадо',
					'qj' => 'Whoosh',
					'ol' => 'KazanExpress',
					'tm' => 'Akulaku',
					'ra' => 'KeyPay',
					'xj' => 'СберМаркет',
					'vd' => 'Betfair',
					'ni' => 'Gojek',
					'mr' => 'Fastmail',
					'hx' => 'AliExpress',
					'bv' => 'Metro',
					'sj' => 'HandyPick',
					'td' => 'ChaingeFinance',
					'dm' => 'Iwplay',
					'xs' => 'GroupMe',
					'kz' => 'NimoTV',
					'nu' => 'Stripe',
					'kr' => 'Eyecon',
					'pz' => 'Lidl',
					'hb' => 'Twitch',
					'xe' => 'GalaxyChat',
					'io' => 'ЗдравСити',
					'ad' => 'Iti',
					'zg' => 'Setel',
					'ij' => 'Revolut',
					'sl' => 'СберАптека',
					'wp' => '163СOM',
					'en' => 'Hermes',
					'zo' => 'Kaggle',
					'vx' => 'HeyBox',
					'hl' => 'Band',
					'lq' => 'Potato',
					'uj' => 'СhampionСasino',
					'ga' => 'Roposo',
					'bo' => 'Wise',
					'fz' => 'KFC',
					'vm' => 'OkCupid',
					'ch' => 'Pocket52',
					'yp' => 'Payzapp',
					'cs' => 'AgriDevelop',
					'yg' => 'CourseHero',
					'lj' => 'Santander',
					'oz' => 'Poshmark',
					'wh' => 'TanTan',
					'wt' => 'IZI',
					'og' => 'Okko',
					'xq' => 'MPL',
					'xh' => 'OVO',
					'kc' => 'Vinted',
					'hk' => '4Fun',
					'yz' => 'Около',
					'eo' => 'Sizeer',
					'ft' => 'Букмекерские',
					'tf' => 'Noon',
					'lv' => 'Megogo',
					'dy' => 'Zomato',
					'lx' => 'DewuPoison',
					'nn' => 'Giftcloud',
					'zs' => 'Bilibili',
					'kx' => 'Vivo',
					'ee' => 'Twilio',
					'tp' => 'IndiaGold',
					'ku' => 'RoyalWin',
					'ca' => 'SuperS',
					'va' => 'SportGully',
					'ej' => 'MrQ',
					'yu' => 'Xiaomi',
					'np' => 'Siply',
					'uy' => 'Meliuz',
					'sb' => 'Lamoda',
					'zd' => 'Zilch',
					'zn' => 'Biedronka',
					'je' => 'Nanovest',
					'gw' => 'CallApp',
					'qk' => 'Bit',
					'xi' => 'InFund',
					'ju' => 'Indomaret',
					'dv' => 'NoBroker',
					'mk' => 'LongHu',
					'iy' => 'FoodHub',
					'ys' => 'ZCity',
					'ax' => 'CrefisaMais',
					'aw' => 'Taikang',
					'ne' => 'Coindcx',
					'fr' => 'Dana',
					'mf' => 'Weidian',
					'he' => 'Mewt',
					'od' => 'FWDMAX',
					'gu' => 'Fora',
					'xd' => 'Tokopedia',
					'gf' => 'GoogleVoice',
					'yy' => 'Venmo',
					'dg' => 'Mercari',
					'oc' => 'DealShare',
					'sm' => 'YoWin',
					'aj' => 'OneAset',
					'ta' => 'Wink',
					'cj' => 'Dotz',
					'vz' => 'Hinge',
					'yv' => 'IPLwin',
					'xy' => 'Depop',
					'bn' => 'Alfagift',
					'ik' => 'GuruBets',
					'rk' => 'Fotka',
					'vj' => 'Stormgain',
					'sz' => 'Pivko24',
					'cr' => 'TenChat',
					'oq' => 'Vlife',
					'of' => 'Urent',
					'gk' => 'AptekaRU',
					'bb' => 'LazyPay',
					'mh' => 'Ашан',
					'hq' => 'Magicbricks',
					'fw' => '99acres',
					'cu' => '炙热星河',
					'mi' => 'Zupee',
					'zi' => 'LoveLocal',
					'aa' => 'Probo',
					'rf' => 'Akudo',
					'so' => 'RummyWealth',
					'zq' => 'IndiaPlays',
					'xt' => 'Flipkart',
					'ln' => 'Grofers',
					'us' => 'IRCTC',
					'kg' => 'FreeChargeApp',
					'gh' => 'GyFTR',
					'lh' => '24betting',
					'wo' => 'Parkplus',
					'rg' => 'Porbet',
					'cz' => 'Getmega',
					'tu' => 'Lyft',
					'ih' => 'TeenPattiStarpro',
					'rh' => 'Ace2Three',
					'xb' => 'RummyOla',
					'fc' => 'PharmEasy',
					'hi' => 'JungleeRummy',
					'fl' => 'RummyLoot',
					'oy' => 'CashFly',
					'jx' => 'Swiggy',
					'rc' => 'Skype',
					'eg' => 'ContactSys',
					'ba' => 'Expressmoney',
					'oe' => 'Codashop',
					'gs' => 'SamsungShop',
					'ml' => 'ApostaGanha',
					'mq' => 'GMNG',
					'jt' => 'TurkiyePetrolleri',
					'sp' => 'HappyFresh',
					'sa' => 'AGIBANK',
					'rb' => 'Tick',
					'mo' => 'Bumble',
					'do' => 'Leboncoin',
					'ac' => 'DoorDash',
					'uv' => 'BinBin',
					'ru' => 'HOP',
					'wq' => 'Leboncoin1',
					'yj' => 'eWallet',
					'jz' => 'Kaya',
					'su' => 'LOCO',
					'ur' => 'MyDailyCash',
					'xu' => 'RecargaPay',
					'ot' => 'Любой другой',
				);


				foreach ($result as $k => $res) {


					$short = explode('_', $k);
					// var_dump($k, $short[0]);

					$i++;
					if ($i > 80) {
						break;
					}
					// var_dump($service, $short[0] == $service);
					// var_dump($short[0], $get_services[$short[0]]);

					if (!empty($services[$short[0]])) {

						// $return_code[] = $k2;
						$price_quantity = get_number_price($country, $short[0]);


						if ($price_quantity['quantity'] > 0) {
							$return_code .= '
								<div class="service-box is-serached" data-id="' . $short[0] . '">
                              <div data-value="' . $short[0] . '"  class="input-service"></div>

                              <div class="service">
                                <p class="service-heart bg-disabled_like js-add-wishlist-service " data-box="' . $short[0] . '"></p>
                                <p class="service-icon">
                                  <img class="service-icon-img" src="' . get_template_directory_uri() . '/assets/img/sms/' . $short[0] . '.png" >
                                </p>
                                <p class="service-name">
                                  <span class="service-name-label">' . $services[$short[0]] . '</span>

                                </p>

                              </div>
							  
                              <div class="more-num">
                                <div class="number-box">
                                  <p class="number" data-quantity="">Qty: ' . $price_quantity['quantity'] . '</p>
                                </div>
                                <div class="cost-box" data-cost="">
                                  <p class="cost">
                                    <span>' . $price_quantity['price'] . ' $</span> <span class="img"><img src="' . get_template_directory_uri() . '/assets/img/sms/buc.png" alt="alt text"></span>
                                  </p>
                                </div>
                              </div>


                            </div>';
						}
					}
				}
				echo json_encode($return_code);
				exit;
			}

			echo 'error';
			exit;
		}
		add_action('wp_ajax_get_operator_service_data', 'get_operator_service_data');
		add_action('wp_ajax_nopriv_get_operator_service_data', 'get_operator_service_data');


		function get_number_and_code()
		{

			$services_data = array(
				'vk' => 'Вконтакте',
				'ok' => 'Ok.ru',
				'wa' => 'Whatsapp',
				'vi' => 'Viber',
				'tg' => 'Telegram',
				'wb' => 'WeChat',
				'go' => 'Google,youtube,Gmail',
				'av' => 'avito',
				'fb' => 'facebook',
				'tw' => 'Twitter',
				'ub' => 'Uber',
				'qw' => 'Киви',
				'gt' => 'Gett',
				'sn' => 'OLX',
				'ig' => 'Instagram',
				'ss' => 'Hezzl',
				'ym' => 'Юла',
				'ma' => 'Mail.ru',
				'mm' => 'Microsoft',
				'uk' => 'Airbnb',
				'me' => 'Line msg',
				'mb' => 'Yahoo',
				'we' => 'ДругВокруг',
				'bd' => 'X5ID',
				'kp' => 'HQ Trivia',
				'dt' => 'Delivery Club',
				'ya' => 'Яндекс',
				'mt' => 'Steam',
				'oi' => 'Tinder',
				'fd' => 'Mamba',
				'zz' => 'Dent',
				'kt' => 'KakaoTalk',
				'pm' => 'AOL',
				'tn' => 'LinkedIN',
				'qq' => 'Tencent QQ',
				'mg' => 'Магнит',
				'pf' => 'pof.com',
				'yl' => 'Yalla',
				'kl' => 'kolesa.kz',
				'po' => 'premium.one',
				'nv' => 'Naver',
				'nf' => 'Netflix',
				'iq' => 'icq',
				'ob' => 'Onlinerby',
				'kb' => 'kufarby',
				'im' => 'Imo',
				'mc' => 'Michat',
				'ds' => 'Discord',
				'vv' => 'Seosprint',
				'ji' => 'Monobank',
				'lf' => 'TikTok/Douyin',
				'hu' => 'Ukrnet',
				'wg' => 'Skout',
				'rz' => 'EasyPay',
				'vf' => 'Q12 Trivia',
				'ny' => 'Pyro Music',
				'rr' => 'Wolt',
				'fe' => 'CliQQ',
				'la' => 'ssoidnet',
				'zh' => 'Zoho',
				'gp' => 'Ticketmaster',
				'am' => 'Amazon',
				'ly' => 'Olacabs',
				'tc' => 'Rambler',
				'dp' => 'ProtonMail',
				'pg' => 'NRJ Music Awards',
				'yf' => 'Citymobil',
				'op' => 'MIRATORG',
				'fx' => 'PGbonus',
				'qr' => 'MEGA',
				'yk' => 'СпортМастер',
				'ls' => 'Careem',
				'bl' => 'BIGO LIVE',
				'mu' => 'MyMusicTaste',
				'fu' => 'Snapchat',
				'bf' => 'Keybase',
				'sg' => 'OZON',
				'uu' => 'Wildberries',
				'ua' => 'BlaBlaCar',
				'ab' => 'Alibaba',
				'iv' => 'Inboxlv',
				'zy' => 'Nttgame',
				'gd' => 'Surveytime',
				'fy' => 'Mylove',
				'ce' => 'mosru',
				'tl' => 'Truecaller',
				'hm' => 'Globus',
				'tx' => 'Bolt',
				'ka' => 'Shopee',
				'pl' => 'Перекресток',
				'ip' => 'Burger King',
				'cm' => 'Prom',
				'hw' => 'AliPay',
				'de' => 'Karusel',
				'jc' => 'IVI',
				'rl' => 'inDriver',
				'df' => 'Happn',
				'ui' => 'RuTube',
				'up' => 'Magnolia',
				'nz' => 'Foodpanda',
				'kf' => 'Weibo',
				'ri' => 'BillMill',
				'cc' => 'Quipp',
				'lr' => 'Okta',
				'za' => 'JDcom',
				'da' => 'MTS CashBack',
				'ug' => 'Fiqsy',
				'sq' => 'KuCoinPlay',
				'zr' => 'Papara',
				'xv' => 'Wish',
				'cx' => 'Icrypex',
				'cw' => 'PaddyPower',
				'li' => 'Baidu',
				'dz' => 'Dominos Pizza',
				'xz' => 'paycell',
				'rd' => 'Lenta',
				'qb' => 'Payberry',
				'hz' => 'Drom',
				'gl' => 'GlobalTel',
				'zk' => 'Deliveroo',
				'ia' => 'Socios',
				'xl' => 'Wmaraci',
				'yi' => 'Yemeksepeti',
				'ew' => 'Nike',
				'ae' => 'myGLO',
				'gb' => 'YouStar',
				'cy' => 'РСА',
				'qm' => 'RosaKhutor',
				'dh' => 'eBay',
				'yb' => 'Квартплата+',
				'qe' => 'GG',
				'yw' => 'Grindr',
				'uz' => 'OffGamers',
				'gx' => 'Hepsiburadacom',
				're' => 'Coinbase',
				'tj' => 'dbrUA',
				'ts' => 'PayPal',
				'rt' => 'hily',
				'sf' => 'SneakersnStuff',
				'sv' => 'Dostavista',
				'qi' => '23red',
				'bz' => 'Blizzard',
				'db' => 'ezbuy',
				'vw' => 'CoinField',
				'zl' => 'Airtel',
				'wf' => 'YandexGo',
				'lw' => 'MrGreen',
				'co' => 'Rediffmail',
				'ey' => 'miloan',
				'ge' => 'Paytm',
				'os' => 'Dhani',
				'ql' => 'CMTcuzdan',
				'cq' => 'Mercado',
				'xk' => 'DiDi',
				'py' => 'Monese',
				'rv' => 'Kotak811',
				'jl' => 'Hopi',
				'pr' => 'Trendyol',
				'pu' => 'Justdating',
				'dk' => 'Pairs',
				'fm' => 'Touchance',
				'ph' => 'SnappFood',
				'sw' => 'NCsoft',
				'nr' => 'Tosla',
				'hy' => 'Ininal',
				'tr' => 'Paysend',
				'pq' => 'CDkeys',
				'ff' => 'AVON',
				'sd' => 'dodopizza',
				'ry' => 'McDonalds',
				'le' => 'E bike Gewinnspiel',
				'hr' => 'JKF',
				'qa' => 'MyFishka',
				'wc' => 'Craigslist',
				'kw' => 'Foody',
				'jg' => 'Grab',
				'mj' => 'Zalo',
				'eu' => 'LiveScore',
				'll' => '888casino',
				'ed' => 'Gamer',
				'pp' => 'Huya',
				'th' => 'WestStein',
				'xr' => 'Tango',
				'iz' => 'Global24',
				'tk' => 'МВидео',
				'rx' => 'Sheerid',
				'ki' => '99app',
				'my' => 'CAIXA',
				'zm' => 'OfferUp',
				'tq' => 'Swvl',
				'au' => 'Haraj',
				'ei' => 'Taksheel',
				'rp' => 'hamrahaval',
				'pa' => 'Gamekit',
				'fs' => 'Şikayet var',
				'ul' => 'Getir',
				'cf' => 'irancell',
				'bt' => 'Alfa',
				'ud' => 'Disney Hotstar',
				'qu' => 'Agroinform',
				'un' => 'humblebundle',
				'rm' => 'Faberlic',
				'uo' => 'CafeBazaar',
				'ti' => 'cryptocom',
				'nk' => 'Gittigidiyor',
				'jm' => 'mzadqatar',
				'lp' => 'Algida',
				'si' => 'Cita Previa',
				'fj' => 'Potato Chat',
				'pt' => 'Bitaqaty',
				'qc' => 'Праймериз 2020',
				'yo' => 'Amasia',
				've' => 'Dream11',
				'qh' => 'Oriflame',
				'iu' => 'Bykea',
				'ib' => 'Immowelt',
				'zv' => 'Digikala',
				'jb' => 'Wing Money',
				'vn' => 'Yaay',
				'wn' => 'GameArena',
				'bj' => 'Вита экспресс',
				'st' => 'Ашан',
				'ev' => 'Picpay',
				'qn' => 'Blued',
				'cd' => 'SpotHit',
				'vo' => 'Brand20ua',
				'il' => 'IQOS',
				'dx' => 'Powerkredite',
				'el' => 'Bisu',
				'dn' => 'Paxful',
				'lk' => 'PurePlatfrom',
				'vc' => 'Banqi',
				'wj' => '1хbet',
				'wk' => 'Mobile01',
				'jj' => 'Aitu',
				'an' => 'Adidas',
				'jr' => 'Самокат',
				'nb' => 'Верный',
				'gv' => 'Humta',
				'dw' => 'Divar',
				'gj' => 'Carousell',
				'hc' => 'MOMO',
				'uf' => 'Eneba',
				'kn' => 'Verse',
				'qd' => 'Taobao',
				'hn' => '1688',
				'zf' => 'OnTaxi',
				'gi' => 'Hotline',
				'uc' => 'Tatneft',
				'mn' => 'RRSA',
				'ak' => 'Douyu',
				'cp' => 'Uklon',
				'qo' => 'Moneylion',
				'wx' => 'Apple',
				'et' => 'Clubhouse',
				'px' => 'Nifty',
				'jh' => 'PingPong',
				'lb' => 'Mailru Group',
				'md' => 'Банки',
				'lt' => 'BitClout',
				'sk' => 'Skroutz',
				'oh' => 'MapleSEA',
				'km' => 'Rozetka',
				'af' => 'GalaxyWin',
				'tt' => 'Ziglu',
				'jf' => 'Likee',
				'az' => 'CityBase',
				'yn' => 'Allegro',
				'wl' => 'YouGotaGift',
				'dl' => 'Lazada',
				'gc' => 'TradingView',
				'cn' => 'Fiverr',
				'ou' => 'Gabi',
				'vp' => 'Kwai',
				'rj' => 'Детский мир',
				'uh' => 'Yubo',
				'es' => 'iQIYI',
				'be' => 'СберМегаМаркет',
				'aq' => 'Glovo',
				'pd' => 'IFood',
				'zw' => 'Quack',
				'gm' => 'Mocospace',
				'fi' => 'Dundle',
				'hg' => 'Switips',
				'qz' => 'Faceit',
				'gz' => 'LYKA',
				'jq' => 'Paysafecard',
				'ue' => 'Onet',
				'xf' => 'LightChat',
				'bp' => 'GoFundMe',
				'vy' => 'Meta',
				'ea' => 'JamesDelivery',
				'hj' => 'Столото',
				'sh' => 'ВкусВилл',
				'vg' => 'ShellBox',
				'qf' => 'RedBook',
				'nq' => 'Trip',
				'ww' => 'BIP',
				'ke' => 'Эльдорадо',
				'qj' => 'Whoosh',
				'ol' => 'KazanExpress',
				'tm' => 'Akulaku',
				'ra' => 'KeyPay',
				'xj' => 'СберМаркет',
				'vd' => 'Betfair',
				'ni' => 'Gojek',
				'mr' => 'Fastmail',
				'hx' => 'AliExpress',
				'bv' => 'Metro',
				'sj' => 'HandyPick',
				'td' => 'ChaingeFinance',
				'dm' => 'Iwplay',
				'xs' => 'GroupMe',
				'kz' => 'NimoTV',
				'nu' => 'Stripe',
				'kr' => 'Eyecon',
				'pz' => 'Lidl',
				'hb' => 'Twitch',
				'xe' => 'GalaxyChat',
				'io' => 'ЗдравСити',
				'ad' => 'Iti',
				'zg' => 'Setel',
				'ij' => 'Revolut',
				'sl' => 'СберАптека',
				'wp' => '163СOM',
				'en' => 'Hermes',
				'zo' => 'Kaggle',
				'vx' => 'HeyBox',
				'hl' => 'Band',
				'lq' => 'Potato',
				'uj' => 'СhampionСasino',
				'ga' => 'Roposo',
				'bo' => 'Wise',
				'fz' => 'KFC',
				'vm' => 'OkCupid',
				'ch' => 'Pocket52',
				'yp' => 'Payzapp',
				'cs' => 'AgriDevelop',
				'yg' => 'CourseHero',
				'lj' => 'Santander',
				'oz' => 'Poshmark',
				'wh' => 'TanTan',
				'wt' => 'IZI',
				'og' => 'Okko',
				'xq' => 'MPL',
				'xh' => 'OVO',
				'kc' => 'Vinted',
				'hk' => '4Fun',
				'yz' => 'Около',
				'eo' => 'Sizeer',
				'ft' => 'Букмекерские',
				'tf' => 'Noon',
				'lv' => 'Megogo',
				'dy' => 'Zomato',
				'lx' => 'DewuPoison',
				'nn' => 'Giftcloud',
				'zs' => 'Bilibili',
				'kx' => 'Vivo',
				'ee' => 'Twilio',
				'tp' => 'IndiaGold',
				'ku' => 'RoyalWin',
				'ca' => 'SuperS',
				'va' => 'SportGully',
				'ej' => 'MrQ',
				'yu' => 'Xiaomi',
				'np' => 'Siply',
				'uy' => 'Meliuz',
				'sb' => 'Lamoda',
				'zd' => 'Zilch',
				'zn' => 'Biedronka',
				'je' => 'Nanovest',
				'gw' => 'CallApp',
				'qk' => 'Bit',
				'xi' => 'InFund',
				'ju' => 'Indomaret',
				'dv' => 'NoBroker',
				'mk' => 'LongHu',
				'iy' => 'FoodHub',
				'ys' => 'ZCity',
				'ax' => 'CrefisaMais',
				'aw' => 'Taikang',
				'ne' => 'Coindcx',
				'fr' => 'Dana',
				'mf' => 'Weidian',
				'he' => 'Mewt',
				'od' => 'FWDMAX',
				'gu' => 'Fora',
				'xd' => 'Tokopedia',
				'gf' => 'GoogleVoice',
				'yy' => 'Venmo',
				'dg' => 'Mercari',
				'oc' => 'DealShare',
				'sm' => 'YoWin',
				'aj' => 'OneAset',
				'ta' => 'Wink',
				'cj' => 'Dotz',
				'vz' => 'Hinge',
				'yv' => 'IPLwin',
				'xy' => 'Depop',
				'bn' => 'Alfagift',
				'ik' => 'GuruBets',
				'rk' => 'Fotka',
				'vj' => 'Stormgain',
				'sz' => 'Pivko24',
				'cr' => 'TenChat',
				'oq' => 'Vlife',
				'of' => 'Urent',
				'gk' => 'AptekaRU',
				'bb' => 'LazyPay',
				'mh' => 'Ашан',
				'hq' => 'Magicbricks',
				'fw' => '99acres',
				'cu' => '炙热星河',
				'mi' => 'Zupee',
				'zi' => 'LoveLocal',
				'aa' => 'Probo',
				'rf' => 'Akudo',
				'so' => 'RummyWealth',
				'zq' => 'IndiaPlays',
				'xt' => 'Flipkart',
				'ln' => 'Grofers',
				'us' => 'IRCTC',
				'kg' => 'FreeChargeApp',
				'gh' => 'GyFTR',
				'lh' => '24betting',
				'wo' => 'Parkplus',
				'rg' => 'Porbet',
				'cz' => 'Getmega',
				'tu' => 'Lyft',
				'ih' => 'TeenPattiStarpro',
				'rh' => 'Ace2Three',
				'xb' => 'RummyOla',
				'fc' => 'PharmEasy',
				'hi' => 'JungleeRummy',
				'fl' => 'RummyLoot',
				'oy' => 'CashFly',
				'jx' => 'Swiggy',
				'rc' => 'Skype',
				'eg' => 'ContactSys',
				'ba' => 'Expressmoney',
				'oe' => 'Codashop',
				'gs' => 'SamsungShop',
				'ml' => 'ApostaGanha',
				'mq' => 'GMNG',
				'jt' => 'TurkiyePetrolleri',
				'sp' => 'HappyFresh',
				'sa' => 'AGIBANK',
				'rb' => 'Tick',
				'mo' => 'Bumble',
				'do' => 'Leboncoin',
				'ac' => 'DoorDash',
				'uv' => 'BinBin',
				'ru' => 'HOP',
				'wq' => 'Leboncoin1',
				'yj' => 'eWallet',
				'jz' => 'Kaya',
				'su' => 'LOCO',
				'ur' => 'MyDailyCash',
				'xu' => 'RecargaPay',
				'ot' => 'Любой другой',
			);
			$service = $_POST['service'];
			$country = $_POST['country'];
			$operator = $_POST['operator'];


			$res = post('https://smshub.org/stubs/handler_api.php?api_key=' . _sms('sms_api') . '&action=getNumber&service=' . $service . '&operator=' . $operator . '&country=' . get_country_code($country));
			$result = explode(':', $res);


			if (!empty($result[2])) {


				$return_code = '<div class="block" data-number-id="' . $result[1] . '">
				<div class="block__country number-col">
					<span class="title">Country</span>
					<span class="' . country_arr($country) . '"></span>
				</div>

				<div class="block__service number-col">
					<span class="title">Service</span>
					<span>
						' . $services_data[$service] . '
						<span class="operator">
							random
						</span>
					</span>
				</div>

				<div class="block__number number-col">
					<span class="title">Number</span>
					<div class="block__number--val">
						<span>' . $result[2] . '</span>
						<span id="number-plus-' . $result[1] . '" class="font-size-0" style="position: fixed; left: -999px;">' . $result[2] . '</span>
						<span id="number-minus-' . $result[1] . '" class="font-size-0" style="position: fixed; left: -999px;">' . $result[2] . '</span>
					</div>

					
				</div>

				<div class="block__status number-col" style="width:28%">
					<span class="title">Status</span>
					<span data-status-id="' . $result[1] . '">
						Send the code to the received number
					</span>
				</div>

				<div class="block__code number-col">
					<span class="title">Code from SMS</span>
					<div data-message-id="' . $result[1] . '" data-wait-message-id="' . $result[1] . '">
						<div class="block__code--animation">
							<div class="spinner">
								<div class="bounce1"></div>
								<div class="bounce2"></div>
								<div class="bounce3"></div>
								<div class="bounce4"></div>
								<div class="bounce5"></div>
								<div class="bounce6"></div>
								<div class="bounce7"></div>
								<div class="bounce8"></div>
								<div class="bounce9"></div>
								<div class="bounce10"></div>
								<div class="bounce11"></div>
								<div class="bounce12"></div>
								<div class="bounce13"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="block__time number-col">
					<span class="title">Left</span>
					<span>
						<span data-time-id="' . $result[1] . '">19</span> min
					</span>
				</div>

				<div class="block__action number-col" data-cancel-content-id="' . $result[1] . '">
					<div class="mb-lg-2">
						<button class="cancel" data-cancel-id="' . $result[1] . '">
							<img src="' . get_template_directory_uri() . '/assets/img/sms/cancel-icon.svg" alt="icon">Cancel
							<span class="d-lg-none">order</span>
						</button>
					</div>
					<div>
						<button class="repeat" data-repeat-id="' . $result[1] . '" data-repeat-country="0"
							data-repeat-country-icon="flag-icon flag-icon-ru" data-repeat-operator="any" data-repeat-service="lb">
							<img src="' . get_template_directory_uri() . '/assets/img/sms/repeat-icon.svg" alt="icon">Repeat order
						</button>
					</div>
				</div>

				
			</div>';


				echo json_encode($return_code);
				exit;
			}
			echo json_encode(array('message' => $res));
			exit;
		}

		add_action('wp_ajax_get_number', 'get_number_and_code');
		add_action('wp_ajax_nopriv_get_number', 'get_number_and_code');

		function get_number_code()
		{
			global $current_user;
			$user_id     = $current_user->ID; //用户ID

			if (!is_user_logged_in()) {
				wp_redirect(home_url('/wp-login.php'));
			}
			$url = "https://ireceivesms.online/wp-json/wsfw-route/v1/wallet/" . $user_id . "?consumer_key=" . _sms('wallet_consumer_key') . "&consumer_secret=" . _sms('wallet_consumer_secret');

			$user_blance = json_decode(file_get_contents($url, true), true);

			if ($user_blance <= 0) {
				$res = array(
					'msg' => "Your balance has Little money, please recharge in time!",
					'success' => 'false',
					'recharge' => 1
				);
				echo json_encode($res);
				exit;
			}



			$number_status = post('https://smshub.org/stubs/handler_api.php?api_key=' . _sms('sms_api') . '&action=getStatus&id=' . $_POST['id']);
			$status_content = explode(':', $number_status);

			if ($status_content[0] == 'STATUS_OK') {

				$user_blance_res = post($url . '&amount=1&action=debit&transaction_detail=getsmscode&payment_method=wallet&note=ID_' . $_POST['id'] . '_code_' . $status_content[1]); //扣除用户金额
				
				if (json($user_blance_res)->response = 'success') {
					$res = array(
						'msg' => "STATUS_OK",
						'success' => 'true',
						'code' => $status_content[1],
					);
				}
			} else {
				$res = array(
					'msg' => "No message has been received yet, please try again later.",
					'success' => 'false'
				);
			}
			echo json_encode($res);
			exit;
			var_dump($number_status);
			exit;
			echo json_encode(array('code' => 1, 'status' => 1));
			exit;
		}
		add_action('wp_ajax_get_number_code', 'get_number_code');
		add_action('wp_ajax_nopriv_get_number_code', 'get_number_code');
