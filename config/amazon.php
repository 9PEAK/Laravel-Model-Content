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

	'Non-Amazon' => [
		'id' => 0,
		'url' => 'non-amazon', // 非亚马逊
		'name' => 'Non-Amazon',
	],

	'US' => [
		'id' => 1,
		'url' => 'amazon.com', // 美国 1
		'name' => 'AmazonUS',
		'service_url' => 'https://mws.amazonservices.com/',
		'marketplace_id' => 'ATVPDKIKX0DER'
	],

	'UK' => [
		'id' => 2,
		'url' => 'amazon.co.uk', // 英国 2
		'name' => 'AmazonUK',
		'service_url' => 'https://mws-eu.amazonservices.com/',
		'marketplace_id' => 'A1F83G8C2ARO7P'
	],

	'DE' => [
		'id' => 3,
		'url' => 'amazon.de', // 德国 3
		'name' => 'AmazonDE',
		'service_url' => 'https://mws-eu.amazonservices.com/',
		'marketplace_id' => 'A1PA6795UKMFR9'
	],


	'FR' => [
		'id' => 4,
		'url' => 'amazon.fr', // 法国 4
		'name' => 'AmazonFR',
		'service_url' => 'https://mws-eu.amazonservices.com/',
		'marketplace_id' => 'A13V1IB3VIYZZH'
	],

	'IT' => [
		'id' => 5,
		'url' => 'amazon.it', // 意大利 5
		'name' => 'AmazonIT',
		'service_url' => 'https://mws-eu.amazonservices.com/',
		'marketplace_id' => 'APJ6JRA9NG5V4'
	],

	'ES' => [
		'id' => 6,
		'url' => 'amazon.es', // 西班牙 6
		'name' => 'AmazonES',
		'service_url' => 'https://mws-eu.amazonservices.com/',
		'marketplace_id' => 'A1RKKUPIHCS9HS'
	],

	'CA' => [ // 加拿大
		'id' => 'ca',
		'url' => 'amazon.ca',
		'name' => 'AmazonCA',
	],

	'IN' => [ // 印度
		'id' => 'in',
		'url' => 'amazon.in',
		'name' => 'AmazonIN',
	],

	'JP' => [
		'id' => 'jp',
		'url' => 'amazon.jp', // 日本
		'name' => 'AmazonJP',
	],
	'CN' => [
		'id' => 'cn',
		'url' => 'amazon.cn', // 中国
		'name' => 'AmazonCN',
	],

];