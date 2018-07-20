<?php
namespace App\Service\SyncCommand;

use Carbon\Carbon;
use App\Model\Api\ApiRecord;

trait Common {


	/*
    |--------------------------------------------------------------------------
    | 部件I： 记录初始化
    |--------------------------------------------------------------------------
	|
    */

	private static $apiRecord;

	/**
	 * @param
	 * @return true if success, null if cannot continue, false if last record is error.
	 * */
	public function syncInit ($ecId, $data=null):bool
	{
		// 获取record
		self::init_record(static::SYNC_TYPE, $ecId, $data);

		// 设置开始时间
		self::$apiRecord->start_time = self::$apiRecord->end_time ?: static::TIME_INIT;

		// 设置结束时间
		self::countEndTime(static::TIME_STEP);

		// record接续
		if ( @static::SYNC_FORCE ) {
			// 强制更新

		} elseif ( self::$apiRecord->id&&!self::$apiRecord->isRenewable()) {
			// 记录异常
			$this->setDebug('Record初始化失败：最新一条Record记录未完成，type='.static::SYNC_TYPE.'， id='.self::$apiRecord->id );
			return false;

		} elseif (self::isTimeAllowed(static::TIME_ZONE, static::TIME_ALLOWED)) {


		} else {
			// 超出限制时间
			/*
			$this->setDebug('新Record超出限制时间。 '.json_encode([
					self::$apiRecord->toArray(),
					self::nowTime()
				])
			);
			*/
			return false;
		}

		return true;

	}


	/**
	 * 克隆记录
	 * */
	protected static function cloneRecord ()
	{
		if (self::$apiRecord) {
			return clone self::$apiRecord;
		}
	}

	/**
	 * step1 获取记录
	 * */
	private static function init_record ($type, $ecId, $data)
	{
		$where = [
			'type' => $type,
			'ec_id' => $ecId,
		];
		$data && $where['data'] = $data;
		self::$apiRecord = ApiRecord::where($where)->orderBy('id', 'desc')->first();

		if (!self::$apiRecord ) {
			self::$apiRecord = new ApiRecord([
				'type' => $type,
				'ec_id' => $ecId,
				'data' => $data,
			]);
		}
	}






	/**
	 * step2 设置record的初始时间
	 * */
	protected static function setStartTime ($time=null)
	{
		self::$apiRecord->start_time = $time ?: (Carbon::now(static::TIME_ZONE))->toDateTimeString();
	}





	/**
	 * step3 设置record的结束时间
	 * */
	protected static function setEndTime ($time=null)
	{
		self::$apiRecord->end_time = $time ?: (Carbon::now(static::TIME_ZONE))->toDateTimeString();
	}


	protected static function countEndTime ($step=0)
	{
		if (!self::$apiRecord->start_time) return;
		self::$apiRecord->end_time = (Carbon::createFromFormat('Y-m-d H:i:s', self::$apiRecord->start_time))->addMinutes($step ?: static::TIME_STEP)->toDateTimeString();
		return true;
	}



	/**
	 * step3.1 重置record的时间
	 * @param $time datetime格式的时间
	 * @return boolean
	 * */


	/**
	 * step3.2 获取起始时间
	 * */
	protected static function getStartTime ()
	{
		return self::$apiRecord->start_time;
	}


	protected static function getStartTimestamp()
	{
		return Carbon::createFromFormat('Y-m-d H:i:s', self::$apiRecord->start_time, static::TIME_ZONE)->getTimestamp();
	}

	/**
	 * step3.3 获取结束时间时间
	 * */
	protected static function getEndTime ()
	{
		return self::$apiRecord->end_time;
	}


	protected static function getEndTimestamp()
	{
		return Carbon::createFromFormat('Y-m-d H:i:s', self::$apiRecord->end_time, static::TIME_ZONE)->getTimestamp();
	}



	/**
	 * step3.4 获取ec_id
	 * */
	protected static function getEcid ()
	{
		return self::$apiRecord->ec_id;
	}



	/**
	 * step3.5 设置data数据
	 * @param $dat.
	 * @return true|null
	 * */
	protected static function setData ($dat)
	{
		self::$apiRecord->data = is_array($dat) ? json_encode($dat) : $dat ;
	}


	protected static function getData ()
	{
		return self::$apiRecord->data;
	}


	/**
	 * step4 判断轮询在时间上是否允许接续
	 * */
	protected static function isTimeAllowed ($tz, $limit ) {
		return self::getEndTimestamp()<=self::nowTimestamp() ;
	}


	protected static function nowTime ()
	{
		return Carbon::now(static::TIME_ZONE)->addMinutes(static::TIME_ALLOWED*-1)->toDateTimeString();
	}

	protected static function nowTimestamp ()
	{
		return time()-static::TIME_ALLOWED*60;
	}


	/**
	 * step5 增加新轮询记录
	 * */
	private static function renew_record () {
		self::$apiRecord = new ApiRecord(self::$apiRecord->attributesToArray());
		self::$apiRecord->status = null;
		self::$apiRecord->save();
	}


	/**
	 * 删除record
	 * */
	protected static function deleteRecord()
	{
		// 清除当前record
		@self::$apiRecord->id && self::$apiRecord->delete();
	}



	/**
	 * 清除record记录
	 * */
	protected static function clearHistory()
	{
		if (defined('static::SYNC_CLEAN') && static::SYNC_CLEAN) {
			if (self::$apiRecord->id) {
				$where = [
					'type' => self::$apiRecord->type,
					'ec_id' => self::$apiRecord->ec_id,
					'status' => ApiRecord::STATUS_END,
				];
				self::$apiRecord->data && $where['data'] = self::$apiRecord->data;
				return ApiRecord::where($where)->where('id', '<', self::$apiRecord->id)->delete();
			}
		}
	}



	/*
    |--------------------------------------------------------------------------
    | 部件II： API请求
    |--------------------------------------------------------------------------
	|
    */

	/**
	 * api请求结束
	 * */
	private static function request_done ($dat=null) {
		self::$apiRecord->setStatusStart()->save();
	}



	/*
    |--------------------------------------------------------------------------
    | 部件III： 保存数据、更新记录、流程结束
    |--------------------------------------------------------------------------
	|
    */

	/**
	 * 记录状态设置为“流程结束”
	 * */
	private static function end_record () {
		self::$apiRecord->setStatusEnd()->save();
	}



}