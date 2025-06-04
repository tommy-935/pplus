<?php
namespace Losml\Includes\Langs;

/**
 * Language Class
 */
class Language {

    public $losml;
    const LOSML_ADMIN_LANG_ACTION = 'hufiurDJFJFUJS$5899-=fjkf';
    public $ignore_type = [
        'shop_order',
        'shop_coupon',
        'videos',
        'gift_card',
        'ufaq',
        'sidebar',
        'oembed_cache',
    ];

    public function __construct($losml) {
        $this->losml = $losml;
    }

    /**
     * Language Filter for Admin Pages
     */
    public function languageFilter() {
        global $wp_query;

        if (isset($wp_query->query['post_type']) && in_array($wp_query->query['post_type'], $this->ignore_type)) {
            return;
        }

        $html = '<li class="losml-subsubsub"><ul>';

        $ret = array_column($this->losml->active_language_data, null, 'id');
        $query_string = $_SERVER['QUERY_STRING'];
        $script_name = $_SERVER['SCRIPT_NAME'];
        $request_schema = $this->losml->getUrlSchema();
        $count_data = $this->getLanguageCount();

        if ($script_name == '/wp-admin/nav-menus.php') {
            $query_string = '';
        }
        $query_string = preg_replace('/&?losml=[a-z]{2}/', '', $query_string);

        $host = $_SERVER['HTTP_HOST'];
        $data = [];
        if ($ret) {
            foreach ($ret as $rs) {
                if (LOSML_MULTI_LANGUAGE_ID == $rs->id) {
                    $data[] = '<li>' . $rs->name . ' (' . ($count_data[$rs->id] ? $count_data[$rs->id] : 0) . ') &nbsp;| &nbsp;</li>';
                } else {
                    $param_str = $query_string ? $query_string . '&losml=' . $rs->code : 'losml=' . $rs->code;
                    $url = $request_schema . '://' . $host . $script_name . ($param_str ? '?' . $param_str : '');
                    $data[] = '<li><a href="' . $url . '">' . $rs->name . '</a> (' . ($count_data[$rs->id] ? $count_data[$rs->id] : 0) . ') &nbsp;| &nbsp;</li>';
                }
            }
            $html .= implode('', $data);
            $html .= '</ul>';
            echo $html;
        }
    }

    /**
     * Get Post Count for each language
     */
    public function getLanguageCount() {
        global $pagenow;

        if (isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'product_cat') {
            $post_type = 'tax_product_cat';
        } else if ($pagenow == 'nav-menus.php') {
            $post_type = 'tax_nav_menu';
        } else {
            if (isset($_GET['taxonomy']) && $_GET['taxonomy']) {
                $post_type = addslashes('tax_' . $_GET['taxonomy']);
            } else {
                $post_type = addslashes('post_' . $_GET['post_type']);
            }
        }

        $sql = '';
        if ($post_type == 'post_product') {
            $sql = $this->losml->wpdb->prepare(
                'SELECT language_id, COUNT(*) AS count 
                 FROM ' . $this->losml->wpdb->prefix . 'losml_strings AS a 
                 LEFT JOIN ' . $this->losml->wpdb->prefix . 'posts AS b 
                 ON a.element_id = b.ID AND b.post_type = "product" 
                 WHERE b.ID IS NOT NULL AND a.element_type = %s 
                 GROUP BY a.language_id',
                $post_type
            );
        } else {
            $sql = $this->losml->wpdb->prepare(
                'SELECT language_id, COUNT(*) AS count 
                 FROM ' . $this->losml->wpdb->prefix . 'losml_strings 
                 WHERE element_type = %s 
                 GROUP BY language_id',
                $post_type
            );
        }

        $ret = $this->losml->wpdb->get_results($sql);

        if ($ret !== false) {
            return array_column($ret, 'count', 'language_id');
        }
        return [];
    }

    /**
     * Set Language Constants
     */
    public function setLanguage() {

        if (!defined('LOSML_MULTI_LANGUAGE_CODE')) {
            $lang = $this->getLanguage();

            define('LOSML_MULTI_LANGUAGE_CODE', $lang->code);
            define('LOSML_MULTI_LANGUAGE_ID', $lang->id);
            define('LOSML_MULTI_DOMAIN', $lang->host);
            define('ICL_LANGUAGE_CODE', LOSML_MULTI_LANGUAGE_CODE); // Compatibility with WPML
        }
    }

    /**
     * Get Active Language
     */
    public function getLanguage() {
        $lang_data = $this->getActiveLanguage();

        $this->losml->active_language_data = $lang_data;
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $losml = isset($_REQUEST['losml']) ? $_REQUEST['losml'] : '';

        if (!$losml && isset($_GET['wc-ajax']) && isset($_SERVER['HTTP_REFERER'])) {
            preg_match('/\/([a-z]{2})\//', $_SERVER['HTTP_REFERER'], $mats);
            if (isset($mats[1])) {
                $losml = $mats[1];
            }
        }
        $locale = get_locale();
        foreach ($lang_data as $obj) {
            if (strpos($uri, '/' . $obj->code . '/') === 0) {
                return $obj;
            }
            if ($losml && $losml == $obj->code) {
                return $obj;
            }
            if($locale == $obj->code){
                return $obj;
            }
        }

        return $lang_data[0];
    }

    /**
     * Language List in Admin Panel
     */
    public function languageList() {
        $list = $this->getActiveLanguage(0);
        include LOSML_BASE_PATH . '/Backend/Views/Langs/list.php';
    }

    public function dashboard() {
        $list = $this->getActiveLanguage(0);
        include LOSML_BASE_PATH . '/Backend/Views/Dashboard/dashboard.php';
    }

    /**
     * Show Add New Language Form
     */
    public function showAddNewLanguage() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $data = $this->losml->language->getLanguageById($id);
        $nonce = wp_create_nonce(self::LOSML_ADMIN_LANG_ACTION . $id);
        include LOSML_BASE_PATH . '/Backend/Views/Langs/new.php';
    }

    /**
     * Get Active Languages
     */
    public function getActiveLanguage($is_enabled = 1) {
        $sql = 'SELECT id, code, name, host, is_enabled FROM ' . $this->losml->wpdb->prefix . 'losml_langs WHERE 1=1';
        if ($is_enabled) {
            $sql .= ' AND is_enabled = %d';
        }
        $sql = $this->losml->wpdb->prepare($sql, $is_enabled);
        $ret = $this->losml->wpdb->get_results($sql);
        return $ret;
    }

    /**
     * Get Language by ID
     */
    public function getLanguageById($id) {
        if (!$id) {
            return [];
        }

        $sql = $this->losml->wpdb->prepare(
            'SELECT id, code, name, host, is_enabled 
             FROM ' . $this->losml->wpdb->prefix . 'losml_langs 
             WHERE id = %d 
             LIMIT 1',
            $id
        );

        $ret = $this->losml->wpdb->get_results($sql);
        return isset($ret[0]) ? $ret[0] : [];
    }

    /**
     * Add or Update Language
     */
    public function addLanguage() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = 0;
        $data = ['error' => ''];
        $nonce = $_POST['nonce'];

        if (!wp_verify_nonce($nonce, self::LOSML_ADMIN_LANG_ACTION . $id)) {
            $data['error'] = 'Invalid Request';
            return $this->losml->ajaxReturn($status, $data);
        }

        $code = addslashes(trim($_POST['code']));
        $name = addslashes(trim($_POST['name']));
        $host = addslashes(trim($_POST['host']));
        $is_enabled = intval($_POST['is_enabled']);
        $userid = get_current_user_id();
        $date = date('Y-m-d H:i:s');

        if (!$code || !$name || !$host) {
            $data['error'] = 'Fields cannot be empty';
            return $this->losml->ajaxReturn($status, $data);
        }

        if ($id) {
            $sql = $this->losml->wpdb->prepare(
                'UPDATE ' . $this->losml->wpdb->prefix . 'losml_langs 
                 SET code = %s, name = %s, host = %s, updated_by = %d, updated_date = %s, is_enabled = %d 
                 WHERE id = %d',
                $code, $name, $host, $userid, $date, $is_enabled, $id
            );
        } else {
            $sql = $this->losml->wpdb->prepare(
                'INSERT INTO ' . $this->losml->wpdb->prefix . 'losml_langs 
                 (code, name, host, is_enabled, added_by, added_date) 
                 VALUES (%s, %s, %s, %d, %d, %s)',
                $code, $name, $host, $is_enabled, $userid, $date
            );
        }

        if ($this->losml->wpdb->query($sql) !== false) {
            $status = 1;
        } else {
            $data['error'] = 'Save Failed';
        }

        return $this->losml->ajaxReturn($status, $data);
    }
}
