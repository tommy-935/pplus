<?php
namespace lymosSmtpEmail\admin\lib;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class adminView{

    public $db;

    public function __construct(){
        global $wpdb;
		$this->db = $wpdb;
        $this->_addHooks();
    }

    private function _addHooks(){
        add_action( 'admin_menu', array( $this, 'adminMenu' ) );
    }

    public function adminMenu(){
        add_options_page( __( 'Lymos Smtp Email', 'lymos-smtp-email' ), __('Lymos Smtp Email', 'lymos-smtp-email'), 'manage_options', 'lymos_email_settings', [$this, 'lymosSmtpShowView'] );
    }

    public function lymosSmtpShowView(){
        require_once LYMOS_SMTP_DIR . '/admin/view/adminView.php';
    }

    public function ajaxSaveSmtp(){
        if(! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_kses_post(wp_unslash($_REQUEST['_wpnonce'])), LYMOS_SMTP_NONCE ) ){
            return wp_send_json(['status' => 0, 'data' => __('saved failed', 'lymos-smtp-email')]);
        }
        $lymos_from_email = addslashes(isset($_POST['lymos_from_email']) ? wp_kses_post(wp_unslash($_POST['lymos_from_email'])) : ''); 
        update_option('lymos_from_email', $lymos_from_email);
        $lymos_from_name = addslashes(isset($_POST['lymos_from_name']) ? wp_kses_post(wp_unslash($_POST['lymos_from_name'])) : ''); 
        update_option('lymos_from_name', $lymos_from_name);
        $lymos_bcc_email = addslashes(isset($_POST['lymos_bcc_email']) ? wp_kses_post(wp_unslash($_POST['lymos_bcc_email'])) : ''); 
        update_option('lymos_bcc_email', $lymos_bcc_email);
        $lymos_smtp_host = addslashes(isset($_POST['lymos_smtp_host']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_host'])) : ''); 
        update_option('lymos_smtp_host', $lymos_smtp_host);
        $lymos_smtp_port = addslashes(isset($_POST['lymos_smtp_port']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_port'])) : ''); 
        update_option('lymos_smtp_port', $lymos_smtp_port);
        $lymos_smtp_username = addslashes(isset($_POST['lymos_smtp_username']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_username'])) : ''); 
        update_option('lymos_smtp_username', $lymos_smtp_username);
        $lymos_smtp_password = addslashes(isset($_POST['lymos_smtp_password']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_password'])) : ''); 
        update_option('lymos_smtp_password', $lymos_smtp_password);

        $lymos_smtp_ssl = addslashes(isset($_POST['lymos_smtp_ssl']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_ssl'])) : ''); 
        $lymos_smtp_ssl = $lymos_smtp_ssl == 'on' ? 1 : 0;
        update_option('lymos_smtp_ssl', $lymos_smtp_ssl);
        $lymos_smtp_record = addslashes(isset($_POST['lymos_smtp_record']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_record'])) : ''); 
        $lymos_smtp_record = $lymos_smtp_record == 'on' ? 1 : 0;
        update_option('lymos_smtp_record', $lymos_smtp_record);

        $lymos_smtp_opened = addslashes(isset($_POST['lymos_smtp_opened']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_opened'])) : ''); 
        $lymos_smtp_opened = $lymos_smtp_opened == 'on' ? 1 : 0;
        update_option('lymos_smtp_opened', $lymos_smtp_opened);

        $lymos_smtp_auto_resend = addslashes(isset($_POST['lymos_smtp_auto_resend']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_auto_resend'])) : ''); 
        $lymos_smtp_auto_resend = $lymos_smtp_auto_resend == 'on' ? 1 : 0;
        update_option('lymos_smtp_auto_resend', $lymos_smtp_auto_resend);


        return wp_send_json(['status' => 1, 'data' => __('saved success', 'lymos-smtp-email')]);
    }

    public function getList(){
        if(! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_kses_post(wp_unslash($_REQUEST['_wpnonce'])), LYMOS_SMTP_NONCE ) ){
            return wp_send_json(['status' => 0, 'data' => __('get failed', 'lymos-smtp-email')]);
        }
        $page = intval(isset($_GET['page']) ? wp_kses_post(wp_unslash($_GET['page'])) : 0); 
        $pagesize = intval(isset($_GET['pagesize']) ? wp_kses_post(wp_unslash($_GET['pagesize'])) : 0); 
       
        $page = $page ? $page : 1;
        $pagesize = $pagesize ? $pagesize : 20;
        $where = '';
        $keyword = trim(isset($_GET['keyword']) ? wp_kses_post(wp_unslash($_GET['keyword'])) : 0); 
        if($keyword){
            $where .= ' and email like "%%s%" ';
        }
        $sql = 'select id, email, subject, added_date, body, status, opened from ' . $this->db->prefix . 'lymoswp_email_record where 1=1 ' . $where . ' order by id desc limit ' . ($page - 1) * $pagesize . ',' . $pagesize;
        $sql_count = 'select count(*) as count from ' . $this->db->prefix . 'lymoswp_email_record where 1=1 ' . $where;

        $sql_count_pre = $this->db->prepare($sql_count, $keyword);
        $count = $this->db->get_var($sql_count_pre);

        $sql_pre = $this->db->prepare($sql, $keyword);
        $data = $this->db->get_results($sql_pre, ARRAY_A);
        
        return wp_send_json(['status' => 1, 'data' => [
            'page' => $page,
            'pagesize' => $pagesize,
            'count' => $count,
            'list' => $data
        ]]);
    }

    
}