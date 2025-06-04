<?php

namespace Losml\Includes\initData;

if(!defined('ABSPATH')){
    exit;
}
class LangData
{
    public $losml;
    public $default_lang_id = 1;
    
    public function __construct($losml) {
        $this->losml = $losml;
    }
    public function init()
    {
        $this->_initLangData();
        $this->_initPostsData();
        $this->_initTermsData();
    }

    protected function _initLangData()
    {
        $table = $this->losml->wpdb->prefix . 'losml_langs';
        $lang = get_locale();
        // found lang already exists use prepare
        $sql = "select id from $table where code = %s";
        $lang_id = $this->losml->wpdb->get_var($this->losml->wpdb->prepare($sql, $lang));
        if($lang_id){
            $this->default_lang_id = $lang_id;
            return;
        }

        $host = $_SERVER['HTTP_HOST'];
        $this->losml->wpdb->insert($table, array(
            'host' => $host,
            'code' => $lang,
            'name' => $lang,
            'is_enabled' => 1,
            'added_by' => get_current_user_id(),
            'added_date' => date('Y-m-d H:i:s')
        ));
        $this->default_lang_id = $this->losml->wpdb->insert_id;
    }

    protected function _initPostsData()
    {
        $table = $this->losml->wpdb->prefix . 'losml_translations';
        $paged = 1;
        $size = 100;
        $lang_id = $this->default_lang_id;
        while(true){
            $sql = 'select ID, post_type from ' . $this->losml->wpdb->posts . ' where post_type in ("' . implode('","', $this->losml->config->post_types) . '") limit ' . ($paged - 1) * $size . ',' . $size;
            $temp = $this->losml->wpdb->get_results($sql, ARRAY_A);
            if(!$temp){
                break;
            }
            $values = [];
            $placeholders = [];
            foreach($temp as $row){
                $placeholders[] = '(%s, %s, %d, %s)'; // name:string, age:int
                $values[] = $row['ID'];
                $values[] = $row['ID'];
                $values[] = $lang_id;
                $values[] = 'post_' . $row['post_type'];
            }
        
            $sql = "INSERT IGNORE INTO $table (element_id, source_element_id, language_id, element_type) VALUES " . implode(', ', $placeholders);
            $this->losml->wpdb->query($this->losml->wpdb->prepare($sql, ...$values));               
            $paged++;
        }
    }

    protected function _initTermsData()
    {
        $prefix = $this->losml->wpdb->prefix; 
        $table = $prefix . 'losml_translations';
        $paged = 1;
        $size = 100;
        $lang_id = $this->default_lang_id;
        while(true){
            $sql = 'select a.term_id, b.taxonomy from ' . $prefix . 'terms as a left join 
            ' . $prefix  . 'term_taxonomy as b on b.term_id = a.term_id where b.taxonomy in ("' . implode('","', $this->losml->config->term_types) . '") limit ' . ($paged - 1) * $size . ',' . $size;
            $temp = $this->losml->wpdb->get_results($sql, ARRAY_A);

            if(!$temp){
                break;
            }
            $values = [];
            $placeholders = [];
            foreach($temp as $row){
                $placeholders[] = '(%s, %s, %d, %s)'; // name:string, age:int
                $values[] = $row['term_id'];
                $values[] = $row['term_id'];
                $values[] = $lang_id;
                $values[] = 'tax_' . $row['taxonomy'];
            }
        
            $sql = "INSERT IGNORE INTO $table (element_id, source_element_id, language_id, element_type) VALUES " . implode(', ', $placeholders);
            $this->losml->wpdb->query($this->losml->wpdb->prepare($sql, ...$values));               
            $paged++;
        }
        
    }
}