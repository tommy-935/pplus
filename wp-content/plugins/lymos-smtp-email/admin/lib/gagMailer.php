<?php
namespace lymosSmtpEmail\admin\lib;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class lseGagMailer extends \stdClass {

	public function Send() {
		return true;
	}
}