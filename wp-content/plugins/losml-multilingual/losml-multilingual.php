<?php
/*
Plugin Name: Losml Multilingual
Description: Multilingual plugin for WordPress
Version: 1.0
Author: None
License: GPLv2
*/

namespace Losml;
use Losml\Includes\Filter\QueryFilter;
use Losml\Includes\Strings\StringHandler;
use Losml\Includes\Cache\Cache;
use Losml\Includes\Filter\MenuManager;
use Losml\Includes\Filter\PostManager;
use Losml\Includes\Filter\TermHandler;
use Losml\Includes\Langs\Language;
use Losml\Includes\Filter\UrlHandler;
use Losml\Backend\Lib\Menu\BackendMenu;
use Losml\Includes\Sso\SsoLogin;
use Losml\Backend\Lib\Hooks\Hooks;
use Losml\Includes\initData\LangData;
use Losml\Config\Config;
use Losml\Backend\Actions\Settings;


if (!defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/autoload.php';
\Losml_Autoloader::registerNamespace('Losml', __DIR__);
// \Losml_Autoloader::registerNamespace('Lib', __DIR__ . '/Lib');
\Losml_Autoloader::register();

class losmlMultilingual
{
    private static $instance = null;

    const VERSION = '1.0.1';

    public $wpdb;
    protected $admin;
    public $language;
    public $filter;
    public $post;
    public $url;
    public $terms;
    public $string;
    public $sso;
    public $menus;
    public $backend_menu;
    public $cache;
    public $active_language_data;
    public $trans_elementid_data = [];
    public $config;
    public $plugin;
    public $settings;

    private function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->defineConstants();
        $this->boot();
    }

    /**
     * Singleton access
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Define plugin constants
     */
    private function defineConstants()
    {
        define('LOSML_BASE_PATH', ABSPATH . 'wp-content/plugins/losml-multilingual');
        define('LOSML_PATH', '/wp-content/plugins/losml-multilingual');
        define('LOSML_NONCE', 'losml-multilingual');
        $this->plugin = plugin_basename( __FILE__ );
    }

    /**
     * Bootstrap plugin
     */
    private function boot()
    {

        register_activation_hook(__FILE__, [$this, 'activate']);
		// register_activation_hook(LOSML_PATH, [$this, 'addSchedule']);
        register_deactivation_hook(__FILE__, [$this, 'deactivation']);

        $this->config = new Config;
        $this->language = new Language($this);
        $this->filter = new QueryFilter($this);
        $this->post = new PostManager($this);
        $this->url = new UrlHandler($this);
        $this->terms = new TermHandler($this);
        $this->menus = new MenuManager($this);
        $this->cache = new Cache($this);
        $this->string = new StringHandler($this);
        $this->settings = new Settings($this);

        add_action('plugins_loaded', [$this->language, 'setLanguage'], 1);
        add_action('plugins_loaded', [$this, 'initSSOLogin'], 2);
        // ('losml_init_after', [$this->language, 'setLanguage'], 1);

        if (is_admin()) {
            $this->registerAdminHooks();
        } else {
            $this->registerFrontendHooks();
        }
    }

    public function addCustomSchedules($schedules){
       
    }

    public function deactivation(){
		// wp_clear_scheduled_hook('dospider_action');
	}

    public function activate(){
        $init = get_option('losml_init');
        if($init){
           // debug return ; 
        }
		$this->_createTable();
        $this->_initData();
        add_option('losml_init', true);
	}

    private function _initData(){
        $this->_initLangData();
        do_action('losml_init_after');
    }

    private function _initLangData(){
        $lang_data = new LangData($this);
        $lang_data->init();
    }

    private function _createTable(){
        $sql1 = 'CREATE TABLE if not exists `' . $this->wpdb->prefix . "losml_langs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(12) NOT NULL DEFAULT '' COMMENT 'language code',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT 'language name',
  `host` varchar(50) NOT NULL DEFAULT '' COMMENT 'domain',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1.enable 2.disabled',
  `added_by` int NOT NULL DEFAULT '0',
  `added_date` date DEFAULT NULL,
  `updated_by` int NOT NULL DEFAULT '0',
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `host` (`host`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB;";
        $sql2 = 'CREATE TABLE if not exists `' . $this->wpdb->prefix . "losml_translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `element_type` varchar(60) NOT NULL DEFAULT 'post_post' COMMENT 'content type',
  `element_id` int NOT NULL DEFAULT '0' COMMENT 'element id',
  `source_element_id` int NOT NULL DEFAULT '0' COMMENT 'source element id',
  `language_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `multi_type_id` (`element_type`,`element_id`) USING BTREE,
  KEY `source_element_id` (`source_element_id`) USING BTREE,
  KEY `language_id` (`language_id`) USING BTREE,
  KEY `element_type` (`element_type`) USING BTREE
) ENGINE=InnoDB;";

        $sql3 = 'CREATE TABLE if not exists `' . $this->wpdb->prefix . "losml_strings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `domain` varchar(60) NOT NULL DEFAULT '' COMMENT 'domain',
  `text` text default null,
  `md5_key` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `md5_key` (`md5_key`) USING BTREE,
  KEY `domain` (`domain`) USING BTREE
) ENGINE=InnoDB;";

        $sql4 = 'CREATE TABLE if not exists `' . $this->wpdb->prefix . "losml_string_translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `string_id` int NOT NULL DEFAULT '0' COMMENT 'string id',
  `language_id` int NOT NULL DEFAULT '0' COMMENT 'language_id',
  `trans_text` text default null,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1.enabled 2.disabled',
  `added_by` int NOT NULL DEFAULT '0',
  `added_date` date DEFAULT NULL,
  `updated_by` int NOT NULL DEFAULT '0',
  `updated_date` date DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `string_lang_id` (`string_id`,`language_id`) USING BTREE,
  KEY `string_id` (`string_id`) USING BTREE,
  KEY `language_id` (`language_id`) USING BTREE,
  KEY `is_enabled` (`is_enabled`,`language_id`) USING BTREE
) ENGINE=InnoDB;";

		// $this->wpdb->query($sql); // CRSF
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);
        dbDelta($sql4);
    }

    /**
     * Initialize SSO login
     */
    public function initSSOLogin()
    {
        $this->sso = new SsoLogin($this);
        $this->sso->init();
    }

    /**
     * Setup frontend hooks
     */
    private function registerFrontendHooks()
    {
        $widget = new \widget($this);

        add_filter('wp_nav_menu_args', [$this->menus, 'navMenuArgsFilter']);
        add_filter('losml_get_element_url', [$this->url, 'getElementUrl']);
        add_filter('widget_display_callback', [$widget, 'displayFilter'], -PHP_INT_MAX, 1);
        add_filter('losml_get_blocks_filter', [$this->post, 'getBlocksFilter']);

        // SEO Sitemaps
        add_filter('wpseo_typecount_join', [$this->filter, 'wpseoTypecountJoin']);
        add_filter('wpseo_typecount_where', [$this->filter, 'wpseoTypecountWhere']);
        add_filter('wpseo_posts_join', [$this->filter, 'wpseoPostJoin']);
        add_filter('wpseo_posts_where', [$this->filter, 'wpseoPostWhere']);
    }

    /**
     * Setup admin panel hooks
     */
    private function registerAdminHooks()
    {
        $this->backend_menu = new BackendMenu($this);
        new Hooks($this);

        add_action('admin_menu', [$this->backend_menu, 'losmlAdminMenu']);
        add_filter('get_user_option_nav_menu_recently_edited', [$this->menus, 'getRecentlyEditMenuId'], 2, 3);

        add_action('wp_ajax_make_duplicate_post', [$this->post, 'makeDuplicatePost']);
        add_action('wp_insert_post', [$this->post, 'afterInsertPost']);

        wp_enqueue_style('losml_multilingual', LOSML_PATH . '/assets/css/multilingual.css', [], self::VERSION);
        wp_enqueue_script('losml_multilingual', LOSML_PATH . '/assets/js/multilingual.js', ['jquery'], self::VERSION);

        $this->post->maybeSetupPostEdit();
        $this->registerAdminFilters();
    }

    /**
     * Admin related filters
     */
    private function registerAdminFilters()
    {
        add_filter('page_link', [$this->url, 'pageLinkFilter'], 99, 3);
        add_filter('post_type_link', [$this->url, 'postTypeLinkFilter'], 99, 4);
        add_filter('admin_url', [$this->url, 'adminUrlFilter'], 99, 3);
        add_filter('rocket_clean_domain_urls', [$this->cache, 'getRocketUrls'], 2, 2);
    }

    /**
     * Transaction helpers
     */
    public function transBegin() { return $this->wpdb->query('START TRANSACTION'); }
    public function transRollback() { return $this->wpdb->query('ROLLBACK'); }
    public function transCommit() { return $this->wpdb->query('COMMIT'); }

    /**
     * Get translated element ID
     */
    public function getTransElementId($element_id, $element_type, $language_id = 0)
    {
        if (isset($this->trans_elementid_data[$element_id])) {
            return $this->trans_elementid_data[$element_id];
        }

        if (!$language_id) {
            $language_id = LOSML_MULTI_LANGUAGE_ID;
        }

        $sql = $this->wpdb->prepare(
            "SELECT b.element_id 
             FROM {$this->wpdb->prefix}losml_strings a 
             LEFT JOIN {$this->wpdb->prefix}losml_strings b 
             ON a.source_element_id = b.source_element_id 
             WHERE a.element_id = %d 
             AND a.element_type = %s 
             AND b.language_id = %d LIMIT 1",
            $element_id,
            $element_type,
            $language_id
        );

        $ret = $this->wpdb->get_var($sql);
        $this->trans_elementid_data[$element_id] = $ret;

        return $ret;
    }

    public function isFunctionInCallStack($method){
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach($stack as $rs){
            if(isset($rs['function']) && $method == $rs['function']){
                return true;
            }
        }
        return false;
    }

    /**
     * Ajax return standard
     */
    public function ajaxReturn($status, $data)
    {
        wp_send_json(['status' => $status, 'data' => $data]);
    }
}

$losml_class = losmlMultilingual::getInstance();
