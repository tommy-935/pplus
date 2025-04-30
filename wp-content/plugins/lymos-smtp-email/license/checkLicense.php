<?php
namespace Lopss\License;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '../../license/licenseManager.php';
class checkLicense {

    public function check(){
        $license = new LicenseManager('lymos-smtp-email', '');
        if($license->isLicenseValid()){
            $licenseData = $license->getLicenseData();
            return [
                'status' => 'valid',
                'data' => $licenseData
            ];
        }else{
            return [
                'status' => 'invalid',
                'data' => []
            ];
        }
        
    }
}