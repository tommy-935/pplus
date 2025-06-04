<?php
namespace Losml\Extends\Location;

class Ip
{
    public function getCountryByIp($ip_address){
		$country_code = '';
		$database = dirname(RUNTIME_PATH) . '/extend/geolite2/GeoLite2-Country.mmdb';
		if (file_exists( $database ) ) {
			$country_code = $this->getCountryIso( $ip_address, $database );
		}

		return $country_code;
	}

    public function getCountryIso( $ip_address, $database ) {

		$basedir = dirname(RUNTIME_PATH) . '/extend/geolite2/';
		require_once $basedir . 'Reader/Decoder.php';
		require_once $basedir . 'Reader/InvalidDatabaseException.php';
		require_once $basedir . 'Reader/Metadata.php';
		require_once $basedir . 'Reader/Util.php';
		require_once $basedir . 'Reader.php';
		$iso_code = '';
		try {
			$reader = new \MaxMind\Db\Reader( $database ); // phpcs:ignore PHPCompatibility.PHP.NewLanguageConstructs.t_ns_separatorFound
			$data   = $reader->get( $ip_address );
			if ( isset( $data['country']['iso_code'] ) ) {
				$iso_code = $data['country']['iso_code'];
			}

			$reader->close();
		} catch ( \Exception $e ) {
		}
		return $iso_code;
	}

	
}