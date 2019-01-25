<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpImap;
use Illuminate\Support\Facades\Input;
use App\Kunnr;
use App\User;
use App\Couponkunnr;
use PDO;
use DB;
use Log;

class GetShoudafang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:kunnr {after} {before}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		
        $after =  $this->argument('after');
		$before =  $this->argument('before');
        if(!$after) $after = '3';
		
		$date_start=date('Ymd',strtotime('-'.$after.' days'));		
		$date_end=date('Ymd',strtotime('-'.$before.' days'));	
		$appkey = 'site0001';
		$appsecret= 'testsite0001';
		//$date_start=date('Ymd',strtotime('-1000 days'));
		//$date_end=date('Ymd');
		$array['date_start']=$date_start;
		$array['appid']= $appkey;
		$array['method']='getKunnr';
		ksort($array);
		$authstr = "";
		foreach ($array as $k => $v) {
			$authstr = $authstr.$k.$v;
		}
		$authstr=$authstr.$appsecret;
		$sign = strtoupper(sha1($authstr));
		$res = file_get_contents('http://116.6.105.153:18003/rfc_site.php?appid='.$appkey.'&method='.$array['method'].'&date_start='.$date_start.'&date_end='.$date_end.'&sign='.$sign);
		$result = json_decode($res,true);
		
		if(!array_get($result,'data')) die();
		$asinList = array_get($result,'data');

		foreach($asinList as $asin){

			if(array_get($asin,'ZDELETE')=='X'){
				Kunnr::where('seller_id', trim(array_get($asin,'SELLERID')))->where('site', trim(array_get($asin,'VKBUR')))->delete();
				continue;
			} 
			Kunnr::updateOrCreate(
			[
				'seller_id' => trim(array_get($asin,'SELLERID','')),
				'site' => trim(array_get($asin,'VKBUR',''))
				],[
				'kunnr' => trim(array_get($asin,'KUNNR','')),
				'date'=> date('Y-m-d H:i:s')
			]);
			
    	}
		$users = User::Where('sap_seller_id','>',0)->orderBy('sap_seller_id','asc')->get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['sap_seller_id']] = $user['id'];
        }
		
		$datas= DB::connection('order')->table('finances_deal_event')->where('ImportToSap',0)->where('user_id',0)->where('PostedDate','>=','2018-12-01')->where('DealDescription','like','%-%')->get();
		foreach($datas as $data_s){
			$s_data_s = explode('-',$data_s->DealDescription);
			if(count($s_data_s)!=5) continue;
			if(!intval($s_data_s[2])) continue;
			if(!array_get($users_array,intval($s_data_s[2]))) continue;
			if(!array_get(matchSapSiteCode(),trim($s_data_s[3]))) continue;
			
			DB::connection('order')->table('finances_deal_event')->where('id',$data_s->Id)->update(
			[
				'user_id'=>array_get($users_array,intval($s_data_s[2])),
				'site_code'=>array_get(matchSapSiteCode(),trim($s_data_s[3])),
				'sku'=>trim($s_data_s[0])
			]
			);
		}
		
		$datas= DB::table('aws_report')->where('ImportToSap',0)->where('user_id',0)->where('date','>=','2018-12-01')->where('campaign_name','like','%-%')->get();
		foreach($datas as $data_s){
			$s_data_s = explode('-',$data_s->campaign_name);
			if(count($s_data_s)!=5) continue;
			if(!intval($s_data_s[2])) continue;
			if(!array_get($users_array,intval($s_data_s[2]))) continue;
			DB::table('aws_report')->where('id',$data_s->id)->update(
			[
				'user_id'=>array_get($users_array,intval($s_data_s[2])),
				'sku'=>trim($s_data_s[0])
			]
			);
		}
		
		$kunnrs = Kunnr::get()->toArray();
        $kunnr_array = array();
        foreach($kunnrs as $kunnr){
            $kunnr_array[$kunnr['seller_id'].$kunnr['site']] = $kunnr['kunnr'];
        }
		
		
		$datas= DB::connection('order')->table('finances_coupon_event')->where('ImportToSap',0)->where('user_id',0)->where('PostedDate','>=','2018-12-01')->get();
		foreach($datas as $data_s){
			$s_data_s = substr($data_s->SellerCouponDescription, -3);
			if(!array_get(matchSapSiteCode(),ltrim($s_data_s))) continue;
			$data_s->site_code = array_get(matchSapSiteCode(),ltrim($s_data_s));
			$kunnr = array_get($kunnr_array,trim($data_s->SellerId.$data_s->site_code));
			if(!$kunnr) continue;
			$exists = Couponkunnr::where('kunnr',$kunnr)->where('coupon_description',$data_s->SellerCouponDescription)->first();
			if(!$exists) continue;
			if(!array_get($users_array,intval($exists->sap_seller_id))) continue;
			DB::connection('order')->table('finances_coupon_event')->where('id',$data_s->Id)->update(
			[
				'user_id'=>array_get($users_array,intval($exists->sap_seller_id)),
				'site_code'=>array_get(matchSapSiteCode(),ltrim($s_data_s)),
				'sku'=>$exists->sku
			]
			);
		}
		
	}

}
