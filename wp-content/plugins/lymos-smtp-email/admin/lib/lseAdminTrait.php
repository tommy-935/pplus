<?php
namespace lymosSmtpEmail\admin\lib;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class lseAdminTrait{

	public function opened(){
		if(! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_kses_post(wp_unslash($_REQUEST['_wpnonce'])), LYMOS_SMTP_NONCE ) ){
            return wp_send_json(['status' => 0, 'data' => __('send failed', 'lymos-smtp-email')]);
        }
		$key = addslashes(isset($_POST['key']) ? wp_kses_post(wp_unslash($_POST['key'])) : ''); 

        if(! $key){
            return wp_send_json(['status' => 0, 'data' => __('Params Error', 'lymos-smtp-email')]);
        }
		
		$this->db->update($this->db->prefix . 'lymoswp_email_record', ['opened' => 'Yes'], ['key' => $key]);
        return wp_send_json(['status' => 0, 'data' => __('success', 'lymos-smtp-email')]);
    }

}