<?php
return array(
	'test' => [
		'url' => 'https://api.apaylater.net/v2/'
	],
	'production' => [
		'url' => 'https://api.apaylater.com/v2/'
	],
	'currency_available' => ['SGD', 'HKD', 'MYR'],
	'country_available' => ['SG', 'HK', 'MY'],
	'sg' => array(
		'country_name' 		=> 'Singapore',
		'country_abbr'		=> 'SG',
		'currency_name'		=> array(
			'en' => 'Singapore Dollar',
		),
		'currency_code'		=> 'SGD',
		'currency_symbol'	=> '$',
		'language_file' 	=> array(
			'en' => 'English',
		),
		'minimum_spend' 	=> '10',
		'atome_url' 		=> 'https://www.atome.sg',
		'int_factor'		=> 100,
	),
	'hk' => array(
		'country_name' 		=> 'HongKong',
		'country_abbr'		=> 'HK',
		'currency_name'		=> array(
			'en' => 'HongKong Dollar',
			'zh' => '港幣',
		),
		'currency_code'		=> 'HKD',
		'currency_symbol'	=> '$',
		'language_file' 	=> array(
			'en' => 'English_HongKong',
			'zh' => 'Chinese_HongKong'
		),
		'minimum_spend' 	=> '100',
		'atome_url' 		=> 'https://www.atome.hk',
		'int_factor'		=> 100
	),
	'my' => array(
		'country_name' 		=> 'Malaysia',
		'country_abbr'		=> 'MY',
		'currency_name'		=> array(
			'en' => 'Malaysia Ringgit',
		),
		'currency_code'		=> 'MYR',
		'currency_symbol'	=> '$',
		'language_file' 	=> array(
			'en' => 'English',
		),
		'minimum_spend' 	=> '50',
		'atome_url' 		=> 'https://www.atome.my',
		'int_factor'		=> 100
	),
	'id' => array(
		'country_name' 		=> 'Indonesia',
		'country_abbr'		=> 'IDN',
		'currency_name'		=> array(
			'en' => 'Indonesia Rupiah',
		),
		'currency_code'		=> 'IDR',
		'currency_symbol'	=> 'Rp',
		'language_file' 	=> array(
			'en' => 'English_Indonesia',
			'id' => 'Bahasa_Indonesia',
		),
		'minimum_spend' 	=> '100000',
		'atome_url' 		=> 'https://www.atome.id',
		'int_factor'		=> 1
	),
	'vn' => array(
		'country_name' 		=> 'Vietnam',
		'country_abbr'		=> 'VN',
		'currency_name'		=> array(
			'en' => 'Vietnamese Dong',
		),
		'currency_code'		=> 'VND',
		'currency_symbol'	=> '₫',
		'language_file' 	=> array(
			'en' => 'English',
			'vi' => 'Vietnamese_Vietnam',
		),
		'minimum_spend' 	=> '200000',
		'atome_url' 		=> 'https://www.atome.vn',
		'int_factor'		=> 1
	),
	'th' => array(
		'country_name' 		=> 'Thailand',
		'country_abbr'		=> 'TH',
		'currency_name'		=> array(
			'en' => 'Thai Baht',
		),
		'currency_code'		=> 'THB',
		'currency_symbol'	=> '฿',
		'language_file' 	=> array(
			'en' => 'English',
			'th' => 'Thai_Thailand',
		),
		'minimum_spend' 	=> '100',
		'atome_url' 		=> 'https://www.atometh.com',
		'int_factor'		=> 100
	),
	'tw' => array(
		'country_name' 		=> 'TaiWan',
		'country_abbr'		=> 'TW',
		'currency_name'		=> array(
			'en' => 'New Taiwan dollar',
			'zh' => '新台幣',
		),
		'currency_code'		=> 'TWD',
		'currency_symbol'	=> '$',
		'language_file' 	=> array(
			'en' => 'English_Taiwan',
			'zh' => 'Chinese_Taiwan',
		),
		'minimum_spend' 	=> '40',
		'atome_url' 		=> 'https://www.atome.tw',
		'int_factor'		=> 1
	),
	'ph' => array(
		'country_name' 		=> 'Philippines',
		'country_abbr'		=> 'PH',
		'currency_name'		=> array(
			'en' => 'Philippine Peso',
		),
		'currency_code'		=> 'PHP',
		'currency_symbol'	=> '₱',
		'language_file' 	=> array(
			'en' => 'English',
		),
		'minimum_spend' 	=> '80',
		'atome_url' 		=> 'https://www.atome.ph',
		'int_factor'		=> 100
	),
);