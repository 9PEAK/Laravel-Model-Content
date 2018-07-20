<?php

	/**
    |--------------------------------------------------------------------------
    | amazon 亚马逊数据解析
    |--------------------------------------------------------------------------
    |
	|
	| 备注： 字段名称完全遵循亚马逊接口和官方文档的设置。
	|
    */
return [


	'mws' => [
		'US' => [
			'access_key' => env('MWS_US'),
			'secret_key' => 'EHzZ5BkA7iGimunPoK3GXGp+ELG26+UuR0Pn61Wl',
			'application_name' => 'NIKKY HOME',
			'application_version' => 1.0,
			'seller_id' => 'A2TXXDJHIV03DN',
		],

		'UK' => [
			'access_key' => env('MWS_UK'),
			'secret_key' => '7rqP2NcaNeQbhLg8+TnzTG+ESY57Fsw8wcgZUwrb',
			'application_name' => 'NIKKY HOME',
			'application_version' => 1.0,
			'seller_id' => 'A1Q37S3V245YBI',
		],
	]





];