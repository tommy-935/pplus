<?php
namespace lymosSmtpEmail\admin\lib;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Hooks {

	public function __construct() {
		add_action('wp_ajax_checkLicense', [$this, 'saveLicense']);
	}

	protected function saveLicense() {
		require_once __DIR__ . '/../license/checkLicense.php';
		$license = new \Lopss\License\checkLicense();
		$license = $license->check();
		if($license['status'] == 'valid'){
			// echo json respose 
			
		}else{

		}
	}


}