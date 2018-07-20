<?php
namespace App\Services\ApiSync\Amazon\Finance;

use App\Services\ApiSync\Sync;
use App\Services\Api\Amazon\Finance as API;
use Carbon\Carbon;
use App\SDK\Amazon\Response as SDK;
//use App\SDK\Amazon\SD as MWS;
use App\AmazonFinance;
use App\Model\Api\ApiRecord;
use App\ApiCache;
use App\Job\Amazon\Finance\ApiSyncCache;

class RenewFinanceGroup extends Sync
{
	const SYNC_FORCE = true; // 强制同步模式 开启或关闭 开启后将忽略上一条record的状态和所有时间限制
	const SYNC_CLEAN = 'type,ec_id';
	const SYNC_TYPE = 'amz:renewFinanceGroup'; // record类型

	const TIME_ZONE = 'UTC'; // 时区
	const TIME_INIT = '2018-04-01 00:00:00'; // 期初时间
	const TIME_STEP = 0; // 时间步长 分钟
	const TIME_ALLOWED = 0; // 截止时间限制 如果结束时间距当前时间少于30分钟则程序停止 强制同步情况除外


	public function syncInit ($ecNo, $data=null):bool
	{

		parent::syncInit($ecNo);

		self::setStartTime(
			(Carbon::now(self::TIME_ZONE))->lastOfMonth()->addDays(-36)->startOfMonth()->toDateTimeString()
		);

		self::setEndTime();
//		\Log::info(self::getEndTime());

		return true;
	}


	private $api;

	/**
	 * 发起api请求 获取结果
	 * */
	public function syncStart ():bool
	{
		$this->api = new API();
		return $this->api->listFinancialGroup([
			'startTime' => self::getStartTime(),
			'marketplaceId' => self::getEcid(),
		]);
	}



	/**
	 * 存储数据 同步结束
	 * */
	public function syncEnd():bool
	{
		$dat = $this->api->response('dat');
		if ( $dat) {
			$sdk = new SDK();
			$sdk->setListFinancialEventGroupsResult($dat);
//			$this->setDebug($dat);
//			return false;


			# 处理数据
			$ecId = \App\Services\EC::get_id( 'amazon', self::getEcid());
			foreach ( $dat as $i=>&$finance ) {
				$finance = @[
					'finance_id' => $finance['FinancialEventGroupId'],
					'progress_status' => $finance['ProcessingStatus']=='Open' ? 1 : 0,
					'transfer_status' => isset($finance['FundTransferStatus']) ? $finance['FundTransferStatus'] : null,
					'trace_id' => isset($finance['TraceId']) ? $finance['TraceId'] : null,
					'account_tail' => isset($finance['AccountTail']) ? $finance['AccountTail'] : null,
					'transfer_time' => isset($finance['FundTransferDate']) ? $finance['FundTransferDate'] : null,
					'original_currency' => $finance['OriginalCurrency'],
					'original_amount' => $finance['OriginalAmount'],
					'converted_currency' => isset($finance['ConvertedCurrency']) ? $finance['ConvertedCurrency'] : null,
					'converted_amount' => isset($finance['ConvertedAmount']) ? $finance['ConvertedAmount'] : null,
					'start_time' => $finance['FinancialEventGroupStart'],
					'end_time' => $finance['FinancialEventGroupEnd'],
					'ec_id' => $ecId,
				];

				$qry = AmazonFinance::where( 'finance_id' , $finance['finance_id'])->first();

				if ( $qry ) {
					// 更新已有账期
					$qry->update($finance);
					unset($dat[$i]);
				} else {
					// 新账期 加入数据库
					AmazonFinance::insert($finance);
					ApiCache::replace([
						'id' => $finance['finance_id'],
						'type' => 'amazon-listFinancialEventGroup',
						'ec_id' => self::getEcid() ,
					]);
				}
			}
			unset($finance);

			# 更新缓存
			ApiCache::query()->update(['times' => 0 ]);

			# 新版 队列中更新缓存
			if ($dat) {
				foreach ($dat as &$finance) {
					$finance = [
						'type' => \App\Services\ApiSync\Amazon\Finance\RenewDetail::SYNC_TYPE ,
						'ec_id' => self::getEcid(),
						'data' => $finance['finance_id'],
						'start_time' => $finance['start_time'],
						'end_time' => $finance['start_time'],
						'status' => ApiRecord::STATUS_END
					];
				}
				ApiRecord::insert($dat);
			}


//			$record = self::cloneRecord();
			/*
			// 增加账期缓存
			dispatch( new ApiSyncCache($record, 'createInfoCache', array_column($dat, 'finance_id')));
			sleep(1); // 升级到5.5后使用链式队列
			// 刷新账期缓存
			dispatch( new ApiSyncCache($record, 'reflashInfo', array_column($dat, 'finance_id')));
			sleep(1); // 升级到5.5后使用链式队列

			*/
			// 创建账期财务明细轮询记录
//			dispatch( new ApiSyncCache($record, 'createDetailRecord', $dat));
//\Log::info('queue start');

			return true;
		}

		return false;

	}

}