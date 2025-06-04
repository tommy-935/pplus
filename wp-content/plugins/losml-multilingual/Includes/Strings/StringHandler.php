<?php
namespace Losml\Includes\Strings;

/**
 * Class for handling string translations
 */
class StringHandler {

    public $losml;
    public $strings = false;
    public $has_inserted_key = []; // Keys already inserted
    public $prefix;

    public function __construct($losml) {
        $this->losml = $losml;
        $this->prefix = $this->losml->wpdb->prefix;
        $this->addHooks();
    }

    public function addHooks() {
        add_action('wp_ajax_save_losml_string_translations', [$this, 'saveStringTrans']);
        add_filter( 'gettext', [$this, 'stringFilter'], 99, 3 );
    }

    /**
     * String filtering
     */
    public function stringFilter($translation, $text, $domain) {
        if (!defined('LOSML_MULTI_LANGUAGE_ID')) {
            return $translation;
        }

        $cache_key = 'losml_lang_strings-' . LOSML_MULTI_LANGUAGE_ID;
        if ($this->strings === false) {
            $cache = wp_cache_get($cache_key);
            if (!$cache) {
                $this->getStrings();
                wp_cache_set($cache_key, json_encode($this->strings));
            } else {
                $this->strings = json_decode($cache, true);
            }
        }

        $key = md5($domain . $text);

        if (isset($this->strings[$key])) {
            if (trim($this->strings[$key]['trans_text'])) {
                return $this->strings[$key]['trans_text'];
            } else {
                return $text;
            }
        } else {
            if (!isset($this->has_inserted_key[$key])) {
                $ret = $this->addString($key, $text, $domain);
                if ($ret) {
                    wp_cache_delete($cache_key);
                }
            }
        }

        return $translation;
    }

    /**
     * Get strings from the database
     */
    public function getStrings() {
        try{
            $sql = $this->losml->wpdb->prepare(
                'SELECT a.md5_key, b.trans_text FROM ' . $this->prefix . 'losml_strings AS a 
                LEFT JOIN ' . $this->prefix . 'losml_string_translations AS b 
                ON a.id = b.string_id AND b.is_enabled = 1 
                AND b.language_id = %d', 
                LOSML_MULTI_LANGUAGE_ID
            );
            $ret = $this->losml->wpdb->get_results($sql, ARRAY_A);
            if ($ret === false || !empty($this->losml->wpdb->last_error)) {
                throw new \Exception('Database query failed: ' . $this->losml->wpdb->last_error);
            }
            $data = [];
            foreach ($ret as $obj) {
                $data[$obj['md5_key']] = $obj;
            }
            $this->strings = $data;
        }catch (\Throwable $e) {
            throw new \Exception('Failed to get strings from the database');
        }
    }

    /**
     * Add a new string to the database
     */
    public function addString($key, $text, $domain) {
        $sql = $this->losml->wpdb->prepare(
            'INSERT IGNORE INTO ' . $this->prefix . 'losml_strings (domain, `text`, md5_key) 
            VALUES (%s, %s, %s)',
            $domain, $text, $key
        );
        $ret = $this->losml->wpdb->query($sql);
        $this->has_inserted_key[$key] = 1;
        return $ret;
    }

    /**
     * String translation management page
     */
    public function stringTrans() {
        $condition = '';
        $domain = sanitize_text_field($_GET['domain']);
        $keyword = sanitize_text_field($_GET['keyword']);

        if ($domain) {
            $condition .= ' AND domain = %s';
        }

        if ($keyword && substr($keyword, 0, 1) === '%') {
            $condition .= ' AND text LIKE %s';
        } else if ($keyword) {
            $condition .= ' AND text = %s';
        }
        $condition .= ' ORDER BY id DESC LIMIT 20';

        $list = $this->getStringList($condition, $domain, $keyword);
        $ids = array_column($list, 'id');
        $where_trans = ' AND string_id IN (' . implode(',', $ids) . ')';
        $trans_data_temp = $this->getStringTrans($where_trans);

        $trans_data = $this->dealTransData($trans_data_temp);
        $lang_data = $this->losml->active_language_data;
        $domain_list = $this->getDomainList();

        include LOSML_BASE_PATH . '/Backend/Views/Langs/stringTranslation.php';
    }

    /**
     * Save translation
     */
    public function saveStringTrans() {
        $status = 0;
        $data = ['error' => ''];

        $id = intval($_POST['id']);
        $string_id = intval($_POST['string_id']);
        $lang_id = intval($_POST['lang_id']);
        $trans_text = sanitize_text_field($_POST['trans_text']);
        $userid = get_current_user_id();

        if (!$string_id || !$userid || !$lang_id) {
            $data['error'] = 'Invalid parameters!';
            return $this->losml->ajaxReturn($status, $data);
        }

        $date = current_time('mysql');
        if ($id) {
            $sql = $this->losml->wpdb->prepare(
                'UPDATE ' . $this->prefix . 'losml_string_translations 
                SET trans_text = %s, updated_by = %d, updated_date = %s 
                WHERE id = %d AND string_id = %d',
                $trans_text, $userid, $date, $id, $string_id
            );
        } else {
            $sql = $this->losml->wpdb->prepare(
                'INSERT INTO ' . $this->prefix . 'losml_string_translations 
                (string_id, language_id, trans_text, is_enabled, added_by, added_date) 
                VALUES (%d, %d, %s, 1, %d, %s)',
                $string_id, $lang_id, $trans_text, $userid, $date
            );
        }
        $ret = $this->losml->wpdb->query($sql);

        if ($ret === false) {
            $data['error'] = 'Failed to save!';
            return $this->losml->ajaxReturn($status, $data);
        }

        $status = 1;
        $cache_key = 'losml_lang_strings-' . $lang_id;
        wp_cache_delete($cache_key);

        return $this->losml->ajaxReturn($status, $data);
    }

    /**
     * Process translation data
     */
    public function dealTransData($trans_data) {
        $data = [];
        foreach ($trans_data as $obj) {
            $data[$obj->language_id][$obj->string_id] = $obj;
        }
        return $data;
    }

    /**
     * Get string translations
     */
    public function getStringTrans($condition = '') {
        $sql = 'SELECT id, language_id, string_id, trans_text 
                FROM ' . $this->prefix . 'losml_strings_translations 
                WHERE 1=1 ' . $condition;
        return $this->losml->wpdb->get_results($sql);
    }

    /**
     * Get list of strings
     */
    public function getStringList($condition = '', $domain = '', $keyword = '') {
        $sql = $this->losml->wpdb->prepare(
            'SELECT id, domain, text FROM ' . $this->prefix . 'losml_strings 
            WHERE 1=1 ' . $condition,
            $domain, $keyword
        );
        return $this->losml->wpdb->get_results($sql);
    }

    /**
     * Get list of domains
     */
    public function getDomainList() {
        $sql = 'SELECT DISTINCT domain FROM ' . $this->prefix . 'losml_strings';
        return $this->losml->wpdb->get_results($sql);
    }
}
