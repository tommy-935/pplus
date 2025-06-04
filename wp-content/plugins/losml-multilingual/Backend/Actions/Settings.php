<?php
namespace Losml\Backend\Actions;

class Settings {
    public $losml;
    public function __construct($losml){
        $this->losml = $losml;
    }

    public function saveSettings(){ 

        // save ajax post losml_domain_type to option
        /*
        if(! check_admin_referer('losml_form_action', LOSML_NONCE)){
            return $this->losml->ajaxReturn(false, 'Invalid request');
        }
            */
        if(! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_kses_post(wp_unslash($_REQUEST['_wpnonce'])), LOSML_NONCE ) ){
            return $this->losml->ajaxReturn(false, 'Invalid request');
        }
        if (! isset($_POST['losml_domain_type'])) {
            return $this->losml->ajaxReturn(false, 'Invalid request parameters');
        }
        $losml_domain_type = addslashes(isset($_POST['losml_domain_type']) ? wp_kses_post(wp_unslash($_POST['losml_domain_type'])) : '');
        update_option('losml_domain_type', $losml_domain_type);
        return $this->losml->ajaxReturn(true, 'Settings saved');
    }
}