<?php
namespace App\Service\SyncCommand\Amazon\Report;

use App\Services\ApiSync\Sync;
use App\Services\Api\Amazon\Report;
use App\SDK\Amazon\MWS;
use App\ApiRecord;
use App\AmazonFinance;

// 亚马逊账期明细轮询
class Request extends Sync {

	const SYNC_FORCE = true; // 强制同步模式 开启或关闭 开启后将忽略上一条record的状态和所有时间限制
	const SYNC_TYPE = 'amazon:requestReport'; // record类型

	const TIME_ZONE = 'UTC'; // 时区
	const TIME_INIT = '2017-04-01 00:00:00'; // 期初时间
	const TIME_STEP = 6; // 时间步长 分钟
	const TIME_ALLOWED = 0; // 截止时间限制 如果结束时间距当前时间少于30分钟则程序停止 强制同步情况除外



	/**
	 * 初始化
	 * */
	public function syncInit ($ecNo, $data=null):bool
	{
		parent::syncInit($ecNo, $data);
		self::setStartTime();
		return true;
	}


	public $reportId;

	/**
	 * 发起api请求 获取结果
	 * */
	public function syncStart ():bool
	{
		$api = new Report();
		$res = $api->requestReport(self::getEcid());

		if ( $res) {
			$this->reportId = $api->response('dat.RequestReportResult.ReportRequestInfo.ReportRequestId');
		} else {
			$dat = $api->response();
			self::setData($dat);
			$this->setDebug($dat);
		}

		return $res;
	}



	/**
	 * 存储数据 同步结束
	 * */
	public function syncEnd():bool
	{
		return true;
	}


}