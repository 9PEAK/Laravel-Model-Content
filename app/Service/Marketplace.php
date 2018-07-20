<?php
namespace App\Service ;

class Marketplace {

	const CONFIG = 'amazon';

	private static function config ($key=null)
	{
		return @config(self::CONFIG.(isset($key) ? '.'.$key : '') );
	}


	/**
	 * 获取站点配置数据
	 * @param $x null|string|int, int or string key of the site
	 * @return if the key is null, return all sites, otherwise signle site if the key exists.
	 * */
	static function get ($x=null) {
		if (isset($x)) {
			return is_int($x)||is_numeric($x) ? @array_values(self::{__FUNCTION__}())[$x] : @self::config(strtolower($x)) ;
		} else {
			return self::config();
		}
	}


}
