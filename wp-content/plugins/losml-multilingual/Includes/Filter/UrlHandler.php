<?php
namespace Losml\Includes\Filter;

/**
 * URL handling class for multilingual support.
 */
class UrlHandler {

    protected $losml;

    public function __construct($losml){
        $this->losml = $losml;
        $this->addHooks();
    }

    public function addHooks(){
        add_filter( 'plugins_url', [$this, 'pluginUrlFilter'], 99, 3 );
        add_filter( 'option_siteurl', array( $this, 'pluginUrlFilter' ) );
        add_filter( 'content_url', array( $this, 'pluginUrlFilter' ) );
        add_filter( 'home_url', array( $this, 'homeUrlFilter' ), 99, 4 );
        add_filter('losml_filter_admin_href', [$this, 'filterAdminBarEditUrl']);
        add_filter('losml_multi_lang_prefix', [$this, 'filter_multi_lang_prefix']);
        add_filter('losml_multi_param', [$this, 'filterAdminMenuUrl']);
        add_filter( 'redirect_post_location', [$this, 'filterEditPostUrl'], 10, 2);
    }

    /**
     * Filters the term edit link to add language parameter.
     */
    public function termEditLinkFilter($location, $term_id, $taxonomy, $object_type){
        if (isset($_REQUEST['losml'])) {
            if ($_REQUEST['losml'] !== 'en') {
                return $location . '&losml=' . sanitize_text_field($_REQUEST['losml']);
            }
        }
        return $location;
    }

    /**
     * Adds the language prefix to URLs.
     */
    public function filterMultiLangPrefix($url){
        $lang = isset($_REQUEST['losml']) ? sanitize_text_field($_REQUEST['losml']) : '';
        if ($lang && $lang !== 'en') {
            return '/' . $lang . '/' . $url;
        }

        if (strpos($_SERVER['REQUEST_URI'], '/' . LOSML_MULTI_LANGUAGE_CODE . '/') === false) {
            return $url;
        }

        return '/' . LOSML_MULTI_LANGUAGE_CODE . $url;
    }

    /**
     * Filters the admin bar edit URL.
     */
    public function filterAdminBarEditUrl($url){
        if (strpos($_SERVER['REQUEST_URI'], '/' . LOSML_MULTI_LANGUAGE_CODE . '/') === false) {
            return $url;
        }
        return $url . '&losml=' . LOSML_MULTI_LANGUAGE_CODE;
    }

    /**
     * Filters the edit post URL.
     */
    public function filterEditPostUrl($url, $id = 0){
        if (!isset($_POST['_wp_http_referer'])) {
            return $url;
        }

        $info = parse_url($_POST['_wp_http_referer']);
        if (!isset($info['query'])) {
            return $url;
        }

        $query_arr = explode('&', $info['query']);
        $lang = 'en';

        foreach ($query_arr as $v) {
            if (strpos($v, 'losml=') !== false) {
                $lang = explode('=', $v)[1];
            }
        }

        if ($lang === 'en' || !$lang) {
            return $url;
        }

        return (strpos($url, '?') !== false) ? $url . '&losml=' . $lang : $url . '?losml=' . $lang;
    }

    /**
     * Filters admin menu URL.
     */
    public function filterAdminMenuUrl($url, $id = 0){
        if (LOSML_MULTI_LANGUAGE_CODE == 'en' || strpos($_SERVER['REQUEST_URI'], '/' . LOSML_MULTI_LANGUAGE_CODE . '/') !== false) {
            return $url;
        }

        return (strpos($url, '?') !== false) ? $url . '&losml=' . LOSML_MULTI_LANGUAGE_CODE : $url . '?losml=' . LOSML_MULTI_LANGUAGE_CODE;
    }

    /**
     * Filters admin URL.
     */
    public function adminUrlFilter($url, $path, $blog_id){
        if (isset($_GET['losml']) && $_GET['losml'] !== 'en' && strpos($url, 'losml=' . $_GET['losml']) === false && strpos($url, 'menu-item-settings') === false && strpos($url, '?') !== false) {
            $url .= '&losml=' . $_GET['losml'];
        }
        return $url;
    }

    /**
     * Filters plugin URL.
     */
    public function pluginUrlFilter($url, $path = '', $plugin = ''){
        $url_info = parse_url($url);

        if (defined('WP_CONTENT_URL') && strpos(WP_CONTENT_URL, 'static') !== false) {
            if (strpos(WP_CONTENT_URL, $url_info['host']) !== false) {
                return $url;
            }
        }

        if (strpos($url, 'wp-content') !== false || strpos($url, 'wp-admin') !== false) {
            return $url;
        }

        $lang = '';
        $locale = get_locale();
        if (defined('LOSML_MULTI_LANGUAGE_CODE') && LOSML_MULTI_LANGUAGE_CODE !== $locale && strpos($_SERVER['REQUEST_URI'], '/wp-admin/') === false) {
            $lang = '/' . LOSML_MULTI_LANGUAGE_CODE;
        }

        if (!$lang && isset($_GET['losml']) && $_GET['losml'] !== $locale && strpos($url, '/wp-admin/') === false && $url_info['path'] && strpos($url, '/' . $_GET['losml'] . '/') === false) {
            $lang = '/' . $_GET['losml'];
        }

        $host = $_SERVER['HTTP_HOST'];
        $url = $url_info['scheme'] . '://' . $host . $lang . (isset($url_info['path']) ? $url_info['path'] : '');
        if (isset($url_info['query'])) {
            $url .= '?' . $url_info['query'];
        }

        return $url;
    }

    /**
     * Filters the home URL.
     */
    public function homeUrlFilter($url, $path, $orig_scheme, $blog_id){
        return ($orig_scheme === 'relative') ? $url : $this->pluginUrlFilter($url);
    }

    /**
     * Filters the term link.
     */
    public function termLinkFilter($termlink, $term, $taxonomy){
        return $this->pluginUrlFilter($termlink);
    }

    /**
     * Filters the post type link.
     */
    public function postTypeLinkFilter($post_link, $post, $leavename, $sample){
        return $this->pluginUrlFilter($post_link);
    }

    /**
     * Filters the site URL.
     */
    public function siteUrlFilter($url, $path, $scheme, $blog_id){
        return $url;
    }

    /**
     * Filters the page link.
     */
    public function pageLinkFilter($link, $post_id = 0, $sample = ''){
        return $this->pluginUrlFilter($link);
    }

    /**
     * Filters sample link.
     */
    public function sampleLinkFilter($permalink, $post_id = 0, $title = '', $name = '', $post = []){
        if (is_array($permalink)) {
            $permalink[0] = $this->pluginUrlFilter($permalink[0]);
        } else {
            $permalink = $this->pluginUrlFilter($permalink);
        }

        return $permalink;
    }

    /**
     * Translates the URL based on language.
     */
    public function transUrl($url, $langid = 0, $code = ''){
        if (is_object($url)) {
            return $url;
        }

        $url_info = parse_url($url);
        $host = '';
        $locale = get_locale();
        foreach ($this->losml->active_language_data as $obj) {
            if ($obj->code === $locale) {
                $host = $obj->host;
                break;
            }
        }

        if ($host) {
            if ($code && $code !== $locale) {
                if (strpos($url, 'post.php') !== false) {
                    $url_info['query'] = isset($url_info['query']) ? $url_info['query'] . '&losml=' . $code : 'losml=' . $code;
                } else {
                    if (!isset($url_info['path']) || !$url_info['path']) {
                        $url_info['path'] = '/' . $code . '/';
                    } else {
                        $url_info['path'] = '/' . $code . $url_info['path'];
                    }
                }
            }

            $url = $url_info['scheme'] . '://' . $host . $url_info['path'];
            if (isset($url_info['query'])) {
                $url .= '?' . $url_info['query'];
            }
        }

        return $url;
    }

    /**
     * Filters the cart URL to include language.
     */
    public function filterCartUrl($url){
        if (strpos($url, '/' . LOSML_MULTI_LANGUAGE_CODE . '/') === false) {
            $url_info = parse_url($url);
            $url = $url_info['scheme'] . '://' . $url_info['host'] . '/' . LOSML_MULTI_LANGUAGE_CODE . $url_info['path'];
        }
        return $url;
    }

    /**
     * Retrieves the URL for the queried element.
     */
    public function getElementUrl(){
        global $wp_query;

        if (!$wp_query->queried_object) {
            return [];
        }

        $element_id = 0;
        $element_type = '';
        $class_name = get_class($wp_query->queried_object);

        switch ($class_name) {
            case 'WP_Term':
                $element_id = $wp_query->queried_object->term_id;
                $element_type = 'tax_product_cat';
                break;
            default:
                $element_id = $wp_query->queried_object->ID;
                $element_type = 'post_' . $wp_query->queried_object->post_type;
                break;
        }

        $element_id = intval($element_id);
        $prefix = $this->losml->wpdb->prefix;

        // Use prepare for SQL queries to prevent SQL injection
        $sql = $this->losml->wpdb->prepare(
            "SELECT c.element_id, b.code, c.element_type, c.language_id, b.host
             FROM {$prefix}losml_strings AS a
             LEFT JOIN {$prefix}losml_strings AS c ON a.source_element_id = c.source_element_id
             LEFT JOIN {$prefix}losml_langs AS b ON c.language_id = b.id
             WHERE b.is_enabled = 1 AND a.element_id = %d AND a.element_type = %s AND c.element_type = %s",
            $element_id,
            $element_type,
            $element_type
        );

        $ret = $this->losml->wpdb->get_results($sql);

        // Check if it's the homepage
        $is_home_page = ($_SERVER['REQUEST_URI'] === '/' || strpos($_SERVER['REQUEST_URI'], '/?') === 0);
        $data = [];

        foreach ($ret as $obj) {
            if ($obj->element_id == $element_id) {
                continue;
            }

            $url = $is_home_page ? $this->losml->getUrlSchema() . '://' . $obj->host : '';
            switch ($obj->element_type) {
                case 'tax_product_cat':
                    $url = get_term_link(intval($obj->element_id));
                    break;
                case 'post_product':
                    $url = get_the_permalink($obj->element_id);
                    break;
                default:
                    $url = get_page_link($obj->element_id);
                    break;
            }

            // Remove language code from URLs
            $url = str_replace('/' . LOSML_MULTI_LANGUAGE_CODE . '/', '/', $url);

            $data[$obj->code] = $this->transUrl($url, $obj->language_id, $obj->code);
        }

        return $data;
    }

}
