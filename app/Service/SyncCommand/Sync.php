<?php
namespace App\Service\SyncCommand;

use Illuminate\Support\Facades\DB;
use App\Contracts\Debug\Debug;

abstract class Sync {

	use Common, Debug;

	function __construct()
	{

		// 必填参数检测

		if ( !defined('static::SYNC_TYPE')) {
			$this->setDebug('记录类型“SYNC_TYPE”未设置。');
			return;
		}
		if ( !defined('static::TIME_ZONE')) {
			$this->setDebug(static::class.'： 时区“TIME_ZONE”未设置。');
			return;
		}
		if ( !defined('static::TIME_INIT')) {
			$this->setDebug('初始时间“TIME_INIT”未设置。');
			return;
		}
		if ( !defined('static::TIME_STEP')) {
			$this->setDebug('初始时间和结束时间间隔“TIME_STEP”未设置。');
			return;
		}
		if ( !defined('static::TIME_ALLOWED')) {
			$this->setDebug('安全时间设置“TIME_ALLOWED”未设置。');
			return;
		}

	}



	abstract function syncStart():bool;



	/**
	 * 执行同步业务 最终调用方法
	 * */
	final public function handle ($ecId, $data=null):bool {

		#1 检测初始化过程中是否异常
		if ( $this->isDebug()) {
			return false;
		}


		#2 轮询初始化
		if (!$this->syncInit($ecId, $data)) {
			return false;
		}
		self::renew_record();


		#3 组织参数 发起api请求
		if ( !$this->syncStart()) {
			return false;
		}
		self::request_done();
//\Log::info(self::$apiRecord);

		#4 存储数据
		DB::transaction( function (){
			if (!$this->syncEnd()) {
				throw new \Exception('The last step error!');
			}
			self::end_record();
			// 清除历史记录
			self::clearHistory();

		});

		return true;
	}
	abstract function syncEnd ():bool;



}