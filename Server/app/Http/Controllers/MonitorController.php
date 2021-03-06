<?php

namespace App\Http\Controllers;

use App\MonitorData;
use App\Pool;
use App\WalletSetting;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
	public function savePool(Request $request)
	{
		$result = ['result' => '', 'error_msg' => '', 'code' => 1];
		// 通过wallet查询数据
		$ret = Pool::where('open_id',$wallet)->first();
		if (empty($ret)){
			$ret = new Pool;
			$ret->open_id = $open_id;
			$ret->pool = "https://www.f2pool.com";
			$ret->coin = "eth";
			$ret->wallet = $request->input('wallet');
			$ret->balance = "{}";
			$ret->save();
		}else if($ret->wallet != $request->input('wallet')){
			// 更新时间
			$ret->wallet = $request->input('wallet');
			$ret->save();
		}
		return redirect('/');
	}
	public function saveWalletSetting(Request $request, $wallet)
	{
		$result = ['result' => '', 'error_msg' => '', 'code' => 1];
		// 通过wallet查询数据
		$ret = WalletSetting::where('wallet',$wallet)->first();
		if (empty($ret)){
			$ret = new WalletSetting;
			$ret->wallet = $wallet;
			$ret->email = $request->input('email');
			$ret->balance = "";
			$ret->last_paid_date = "";
			$ret->last_paid_balance = "";
			$ret->price = "{}";
			$ret->save();
		}else if($ret->email != $request->input('email')){
			// 更新时间
			$ret->email = $request->input('email');
			//$ret->updated_at = date('Y-m-d H:i:s');
			$ret->save();
		}
		return redirect('wallet/'.$wallet);
	}
	public function getMonitorData($wallet)
	{
		$monitor_data = MonitorData::where('wallet',$wallet)->get();
		$setting = WalletSetting::where('wallet',$wallet)->first();
		$data = ['wallet' => $wallet,
			'setting' => $setting,
			'price' => empty($setting)?"{}":json_decode($setting->price),
			'results' => $monitor_data,
		];
		return view('welcome',$data);
		//return response()->json($ret);
	}
	public function pushMonitorData($wallet,$miner)
    {
		$result = ['result' => '', 'error_msg' => '', 'code' => 1];
		// 通过wallet+miner查询数据
		$ret = MonitorData::where('wallet',$wallet)->where('miner',$miner)->first();
		if (empty($ret)){
			$ret = new MonitorData;
			$ret->wallet = $wallet;
			$ret->miner = $miner;
			$ret->save();
		}else{
			// 更新时间
			$ret->updated_at = date('Y-m-d H:i:s');
			$ret->save();
		}
		$result['result'] = 'success';
		return response()->json($result);
    }
}

