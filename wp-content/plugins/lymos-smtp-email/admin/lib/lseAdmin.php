<?php
namespace lymosSmtpEmail\admin\lib;
use Lopss\License\checkLicense;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once LYMOS_SMTP_PATH . 'license/checkLicense.php';
require_once LYMOS_SMTP_PATH . 'admin/lib/lseAdminTrait.php';
class lseAdmin{

	public $config = [];
	public $db;
	public $checkLicense;

	use lseAdminTrait;

    public function __construct(){
		global $wpdb;
		$this->db = $wpdb;
        $this->_addHooks();
		$this->checkLicense = new checkLicense;
    }

    private function _addHooks(){
        add_action( 'phpmailer_init', array( $this, 'initSmtp' ), 999 );
		add_action('wp_mail_succeeded', [$this, 'successAfter'], 999);
		add_action('wp_mail_failed', [$this, 'failedAfter'], 999);
		$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
    }

	public function fitlerEmailContent($data){
		if($this->c('lymos_smtp_opened') && $this->checkLicense->check()['status'] == 'valid'){
			$key = $this->successAfter($data, true);
			$data['message'] .= $data['message'] . $this->genTrackingCode($key);
			$data['had_inserted'] = $key;
		}
		return $data;
	}

	/* @param array $mail_data {
		*     An array containing the email recipient(s), subject, message, headers, and attachments.
		*
		*     @type string[] $to          Email addresses to send message.
		*     @type string   $subject     Email subject.
		*     @type string   $message     Message contents.
		*     @type string[] $headers     Additional headers.
		*     @type string[] $attachments Paths to files to attach.
		* }
	*/
	public function successAfter($mail_data, $is_return = false){
		if(! $this->c('lymos_smtp_record')){
			return ;
		}
		$key = wp_generate_uuid4();
		$data = [
			'email' => implode(',', $mail_data['to']),
			'key' => $key,
			'subject' => $mail_data['subject'],
			'body' => $mail_data['message'],
			'added_date' => gmdate('Y-m-d H:i:s')
		];
		if($is_return){
			$data['status'] = 'Wait';
		}
		global $wpdb;
		if(isset($mail_data['had_inserted'])){
			$data = ['status' => 'Success'];
			$wpdb->update($wpdb->prefix . 'lymoswp_email_record', $data, ['key' => $mail_data['had_inserted']]);
			return ;
		}
		$wpdb->insert($wpdb->prefix . 'lymoswp_email_record', $data); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		if($is_return){
			return $key;
		}
	}

	public function failedAfter($error_data){
		if(! $this->c('lymos_smtp_record')){
			return ;
		}
		$mail_data = $error_data->mail_data;
		$data = [
			'email' => implode(',', $mail_data['to']),
			'key' => wp_generate_uuid4(),
			'subject' => $mail_data['subject'],
			'body' => $mail_data['message'],
			'added_date' => gmdate('Y-m-d H:i:s'),
			'status' => 'Failed',
			'error_message' => $error_data->error->get_error_message()
		];
		global $wpdb;
		if(isset($mail_data['had_inserted'])){
			$data = ['status' => 'Failed'];
			$wpdb->update($wpdb->prefix . 'lymoswp_email_record', $data, ['key' => $mail_data['had_inserted']]);
			return ;
		}
		
		$wpdb->insert($wpdb->prefix . 'lymoswp_email_record', $data); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	public function c($key){
		return $this->getConfig($key);
	}

	public function getConfig($key){
		return get_option($key);
	}

	public function saveMessage(){
		if(! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_kses_post(wp_unslash($_REQUEST['_wpnonce'])), LYMOS_SMTP_NONCE ) ){
            return wp_send_json(['status' => 0, 'data' => __('send failed', 'lymos-smtp-email')]);
        }
		$lymos_smtp_to = addslashes(isset($_POST['lymos_smtp_to']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_to'])) : ''); 
		$lymos_smtp_subject = addslashes(isset($_POST['lymos_smtp_subject']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_subject'])) : ''); 
		$lymos_smtp_message = addslashes(isset($_POST['lymos_smtp_message']) ? wp_kses_post(wp_unslash($_POST['lymos_smtp_message'])) : ''); 

        if(! $lymos_smtp_to || ! $lymos_smtp_subject){
            return wp_send_json(['status' => 0, 'data' => __('To or Subject must be filled', 'lymos-smtp-email')]);
        }
		$res = $this->testMail($lymos_smtp_to, $lymos_smtp_subject, $lymos_smtp_message);
		if(isset($res['error']) && $res['error']){
			return wp_send_json(['status' => 1, 'data' => __('sended failed ', 'lymos-smtp-email') . $res['error']]);
		}
        return wp_send_json(['status' => 0, 'data' => __('sended success', 'lymos-smtp-email')]);
    }

	public function genTrackingCode($key){
		$html = '<div style="display: none;"><img src="' . home_url('wp-admin/admin-ajax.php?action=lymos_smtp_opened&key=' . $key . '&_wpnonce=' . wp_create_nonce(LYMOS_SMTP_NONCE) . '"></div>');
		return $html;
	}

	public function resend(){
		if(! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_kses_post(wp_unslash($_REQUEST['_wpnonce'])), LYMOS_SMTP_NONCE ) ){
            return wp_send_json(['status' => 0, 'data' => __('send failed', 'lymos-smtp-email')]);
        }
		$id = intval(isset($_POST['id']) ? wp_kses_post(wp_unslash($_POST['id'])) : ''); 

        if(! $id){
            return wp_send_json(['status' => 0, 'data' => __('Params Error', 'lymos-smtp-email')]);
        }
		$where = ' and id = %d';
		$sql = 'select id, email, subject, added_date, body, status from ' . $this->db->prefix . 'lymoswp_email_record where 1=1 ' . $where . ' limit 1';

        $sql_pre = $this->db->prepare($sql, $id);
        $data = $this->db->get_row($sql_pre, ARRAY_A);
		if(! $data){
			return wp_send_json(['status' => 0, 'data' => __('Params Error', 'lymos-smtp-email')]);
		}
		$res = $this->testMail($data['email'], $data['subject'], $data['body']);
		if(isset($res['error']) && $res['error']){
			return wp_send_json(['status' => 1, 'data' => __('sended failed ', 'lymos-smtp-email') . $res['error']]);
		}
		$this->db->update($this->db->prefix . 'lymoswp_email_record', ['status' => 'Success'], ['id' => $id]);
        return wp_send_json(['status' => 0, 'data' => __('sended success', 'lymos-smtp-email')]);
    }

    public function initSmtp( &$phpmailer ) {
		//check if SMTP credentials have been configured.
		if ( ! $this->credentialsConfigured() ) {
			return;
		}
		//check if Domain Check enabled
		$domain = $this->isDomainBlocked();
		if ( false !== $domain ) {
			//domain check failed
			//let's check if we have block all emails option enabled
			if (1 === $this->c('block_all_emails') ) {
				// it's enabled. Let's use gag mailer class that would prevent emails from being sent out.
				require_once './gagMailer.php';
				$phpmailer = new \lymosSmtpEmail\admin\lib\lseGagMailer();
			} else {
				// it's disabled. Let's write some info to the log
				$this->log(
					"\r\n------------------------------------------------------------------------------------------------------\r\n" .
						'Domain check failed: website domain (' . $domain . ") is not in allowed domains list.\r\n" .
						"SMTP settings won't be used.\r\n" .
						"------------------------------------------------------------------------------------------------------\r\n\r\n"
				);
			}
			return;
		}

		/* Set the mailer type as per config above, this overrides the already called isMail method */
		$phpmailer->IsSMTP();
		if (1 === $this->c('force_from_name_replace') ) {
			$from_name = $this->c('from_name_field');
		} else {
			$from_name = ! empty( $phpmailer->FromName ) ? $phpmailer->FromName : $this->c('from_name_field');
		}
		$from_email = $this->c('lymos_from_email');
		//set ReplyTo option if needed
		//this should be set before SetFrom, otherwise might be ignored
		if ( ! empty( $this->c('reply_to_email') ) ) {
			if (1 === $this->c('sub_mode') ) {
				if ( count( $phpmailer->getReplyToAddresses() ) >= 1 ) {
					// Substitute from_email_field with reply_to_email
					if ( array_key_exists( $this->c('from_email_field'), $phpmailer->getReplyToAddresses() ) ) {
						$reply_to_emails = $phpmailer->getReplyToAddresses();
						unset( $reply_to_emails[ $this->c('from_email_field') ] );
						$phpmailer->clearReplyTos();
						foreach ( $reply_to_emails as $reply_to_email => $reply_to_name ) {
							$phpmailer->AddReplyTo( $reply_to_email, $reply_to_name );
						}
						$phpmailer->AddReplyTo( $this->c('reply_to_email'), $from_name );
					}
				} else { // Reply-to array is empty so add reply_to_email
					$phpmailer->AddReplyTo( $this->c('reply_to_email'), $from_name );
				}
			} else { // Default behaviour
				$phpmailer->AddReplyTo( $this->c('reply_to_email'), $from_name );
			}
		}

		if ( ! empty( $this->c('lymos_bcc_email') ) ) {
				$bcc_emails = explode( ',', $this->c('lymos_bcc_email') );
			foreach ( $bcc_emails as $bcc_email ) {
						$bcc_email = trim( $bcc_email );
						$phpmailer->AddBcc( $bcc_email );
			}
		}

		// let's see if we have email ignore list populated
		if (! empty( $this->c('email_ignore_list') ) ) {
			$emails_arr  = explode( ',', $this->c('email_ignore_list') );
			$from        = $phpmailer->From;
			$match_found = false;
			foreach ( $emails_arr as $email ) {
				if ( strtolower( trim( $email ) ) === strtolower( trim( $from ) ) ) {
					$match_found = true;
					break;
				}
			}
			if ( $match_found ) {
				//we should not override From and Fromname
				$from_email = $phpmailer->From;
				$from_name  = $phpmailer->FromName;
			}
		}
		$phpmailer->From     = $from_email;
		$phpmailer->FromName = $from_name;
		$phpmailer->SetFrom( $phpmailer->From, $phpmailer->FromName );
		//This should set Return-Path header for servers that are not properly handling it, but needs testing first
		//$phpmailer->Sender	 = $phpmailer->From;
		/* Set the SMTPSecure value */
		/*
		if ( 'none' !== $this->c('smtp_settings')['type_encryption'] ) {
			$phpmailer->SMTPSecure = $this->c('smtp_settings')['type_encryption'];
		}
		*/
		if($this->c('lymos_smtp_ssl')){
			$phpmailer->SMTPSecure = 'ssl';
		}
		

		/* Set the other options */
		$phpmailer->Host = $this->c('lymos_smtp_host');
		$phpmailer->Port = $this->c('lymos_smtp_port');

		/* If we're using smtp auth, set the username & password */
		if ( 'yes' === $this->c('lymos_smtp_autentication') || true) {
			$phpmailer->SMTPAuth = true;
			$phpmailer->Username = $this->c('lymos_smtp_username');
			$phpmailer->Password = $this->c('lymos_smtp_password');
		}
		//PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
		$phpmailer->SMTPAutoTLS = false;
		

		if (false !== $this->c('lymos_smtp_insecure_ssl') || true) {
			// Insecure SSL option enabled
			$phpmailer->SMTPOptions = array(
				'ssl' => array(
					'verify_peer'       => false,
					'verify_peer_name'  => false,
					'allow_self_signed' => true,
				),
			);
		}

		//set reasonable timeout
		$phpmailer->Timeout = 10;
	}

    public function testMail($to_email, $subject, $message ) {
		$ret = array();
		if ( ! $this->credentialsConfigured() ) {
			return false;
		}

		global $wp_version;
		
		if ( version_compare( $wp_version, '5.4.99' ) > 0 ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
			$mail = new \PHPMailer\PHPMailer\PHPMailer( true );

		} else {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
			$mail = new \PHPMailer( true );
		}
		
		try {

			$charset       = get_bloginfo( 'charset' );
			$mail->CharSet = $charset;

			$from_name  = $this->c('lymos_from_name');
			$from_email = $this->c('lymos_from_email');

			$mail->IsSMTP();

			// send plain text test email
			$mail->ContentType = 'text/plain';
			$mail->IsHTML( false );

			/* If using smtp auth, set the username & password */
			if ( 'yes' === $this->c('lymos_from_autentication') || true) {
				$mail->SMTPAuth = true;
				$mail->Username = $this->c('lymos_smtp_username');
				$mail->Password = $this->c('lymos_smtp_password');
			}

			
			/* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
			$mail->SMTPAutoTLS = false;
			if($this->c('lymos_smtp_ssl')){
				$mail->SMTPSecure = 'ssl';
			}

			
			if (false !== $this->c('lymos_smtp_insecure_ssl') || true) {
				// Insecure SSL option enabled
				$mail->SMTPOptions = array(
					'ssl' => array(
						'verify_peer'       => false,
						'verify_peer_name'  => false,
						'allow_self_signed' => true,
					),
				);
			}
			

			/* Set the other options */
			$mail->Host = $this->c('lymos_smtp_host');
			$mail->Port = $this->c('lymos_smtp_port');

			//Add reply-to if set in settings.
			/*
			if ( ! empty( $this->c('reply_to_email') ) ) {
				$mail->AddReplyTo( $this->c('reply_to_email'), $from_name );
			}
			*/

			//Add BCC if set in settings.
			if ( ! empty( $this->c('lymos_bcc_email') ) ) {
				$bcc_emails = explode( ',', $this->c('lymos_bcc_email'));
				foreach ( $bcc_emails as $bcc_email ) {
					$bcc_email = trim( $bcc_email );
					$mail->AddBcc( $bcc_email );
				}
			}

			$mail->SetFrom( $from_email, $from_name );
			//This should set Return-Path header for servers that are not properly handling it, but needs testing first
			//$mail->Sender		 = $mail->From;
			$mail->Subject = $subject;
			$mail->Body    = $message;
			$mail->AddAddress( $to_email );
			global $lymos_smtp_debug_msg;
			$lymos_smtp_debug_msg         = '';
			$mail->Debugoutput = function ( $str, $level ) {
				global $lymos_smtp_debug_msg;
				$lymos_smtp_debug_msg .= $str;
			};
			$mail->SMTPDebug   = 1;
			//set reasonable timeout
			$mail->Timeout = 10;

			/* Send mail and return result */
			$rets = $mail->Send();
			if($rets){
				$mail_data = [
					'to' => [$to_email],
					'subject' => $subject,
					'message' => $message
				];
				$this->successAfter($mail_data);
			}
			$mail->ClearAddresses();
			$mail->ClearAllRecipients();
		} catch ( \Exception $e ) {
			$ret['error'] = $mail->ErrorInfo;
		} catch ( \Throwable $e ) {
			$ret['error'] = $mail->ErrorInfo;
		}


		$ret['debug_log'] = $lymos_smtp_debug_msg;

		return $ret;
	}

    public function isDomainBlocked() {
		
		return false;
	}

    public function credentialsConfigured() {
		$configured = true;
		if (empty($this->c('lymos_from_email'))) {
			$configured = false;
		}
		if (empty($this->c('lymos_from_name'))) {
			$configured = false;
		}
		return $configured;
	}

	public function isLicenseValid() {
		return $this->checkLicense->check();
	}

	public function autoSendFailedEmail(){
		// check License
		$license = $this->isLicenseValid();
		if($license['status'] == 'invalid'){
			return ;
		}

		if(! $this->c('lymos_smtp_auto_resend')){
			return;
		}
		$where = ' and status = %s and resend_times < 2';
		$sql = 'select id, email, subject, added_date, body, status, resend_times from ' . $this->db->prefix . 'lymoswp_email_record where 1=1 ' . $where . ' limit 100';

        $sql_pre = $this->db->prepare($sql, 'Failed');
        $data = $this->db->get_results($sql_pre, ARRAY_A);
		foreach ($data as $item) { 
			$this->db->update($this->db->prefix . 'lymoswp_email_record', ['status' => 'Sending'], ['id' => $item['id']]);
			$ret = $this->testMail($item['email'], $item['subject'], $item['body']);
			if($ret['error']){
				$this->db->update($this->db->prefix . 'lymoswp_email_record', ['resend_times' => $item['resend_times'] + 1, 'status' => 'Failed'], ['id' => $item['id']]);
			}else{
				$this->db->update($this->db->prefix . 'lymoswp_email_record', ['status' => 'Success'], ['id' => $item['id']]);
			}
		}

	}

}