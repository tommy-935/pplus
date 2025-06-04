<?php
namespace Losml\Backend\Lib\Hooks;

class Hooks{

    public $losml;
    

    public function __construct($losml){
        $this->losml = $losml;
        $this->addHooks();
    }

    public function addHooks(){
        add_action('wp_ajax_losml_add_langs', [$this->losml->language, 'addLanguage']);
        add_action('edit_form_after_editor', [$this->losml->post, 'displayMetaBox'], 3, 1);
        add_action('wp_ajax_losml_sync', [$this->losml->post, 'losmlSync']);
        add_action('wp_ajax_losml_clear_product_cache', [$this->losml->post, 'losml_clear_product_cache']); 
        add_action('wp_ajax_losml_product_build_sql', [$this->losml->post, 'losml_product_build_sql']);
        add_action('wp_ajax_losml_sync_private', [$this->losml->post, 'losml_sync_private']);

        add_action('wp_ajax_losml_save_setting', [$this->losml->settings, 'saveSettings']);
    }

}
