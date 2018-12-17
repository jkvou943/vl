<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpImap;
use Illuminate\Support\Facades\Input;
use App\Asin;
use PDO;
use DB;
use Log;

class GetAsin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:asin {after} {before}';

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
		$array['method']='getAsin';
		ksort($array);
		$authstr = "";
		foreach ($array as $k => $v) {
			$authstr = $authstr.$k.$v;
		}
		$authstr=$authstr.$appsecret;
		$sign = strtoupper(sha1($authstr));
		$res = file_get_contents('http://116.6.105.153:18003/rfc_site.php?appid='.$appkey.'&method=getAsin&date_start='.$date_start.'&date_end='.$date_end.'&sign='.$sign);
		$result = json_decode($res,true);
		
		if(!array_get($result,'data')) die();
		$asinList = array_get($result,'data');

		foreach($asinList as $asin){

			if(array_get($asin,'ZDELETE')=='X'){
				Asin::where('asin', trim(array_get($asin,'ASIN')))->where('site', 'www.'.trim(array_get($asin,'SITE')))->where('sellersku', trim(array_get($asin,'SELLER_SKU')))->delete();
				DB::table('asin_seller_count')->where('asin', trim(array_get($asin,'ASIN')))->where('site', 'www.'.trim(array_get($asin,'SITE')))->update(array('updated_at'=>date('Y-m-d H:i:s'),'status'=>'X'));
				continue;
			} 
			Asin::updateOrCreate(
			[
				'asin' => trim(array_get($asin,'ASIN','')),
				'site' => 'www.'.trim(array_get($asin,'SITE','')),
				'sellersku'=> trim(array_get($asin,'SELLER_SKU',''))],[
				'item_no' => trim(array_get($asin,'MATNR','')),
				'seller' => trim(array_get($asin,'SELLER','')),
				'item_group' => trim(array_get($asin,'MATKL','')),
				'status' => trim(array_get($asin,'ZSTATUS','')),
				'item_model' => trim(array_get($asin,'MODEL','')),
				'bg' => trim(array_get($asin,'ZBGROUP','')),
				'bu' => trim(array_get($asin,'ZBUNIT','')),
				'store' => trim(array_get($asin,'STORE','')),
				'brand' => trim(array_get($asin,'BRAND','')),
				'brand_line' => trim(array_get($asin,'WGBEZ','')),
				'sap_seller_id' => trim(array_get($asin,'VKGRP','')),
				'sap_site_id' => trim(array_get($asin,'VKBUR','')),
				'sap_store_id' => trim(array_get($asin,'KUNNR','')),
				'sap_warehouse_id' => trim(array_get($asin,'LGORT','')),
				'sap_factory_id' => trim(array_get($asin,'WERKS','')),
				'sap_shipment_id' => trim(array_get($asin,'SDABW','')),
				'asin_last_update_date'=> date('Y-m-d H:i:s')
			]);
			
			if( array_get($asin,'ZSTATUS')=='A' || array_get($asin,'ZSTATUS')=='B'){
				$exists = DB::table('asin_seller_count')->where('asin', trim(array_get($asin,'ASIN')))->where('site', 'www.'.trim(array_get($asin,'SITE')))->first();
				if($exists) {
					DB::table('asin_seller_count')->where('asin', trim(array_get($asin,'ASIN')))->where('site', 'www.'.trim(array_get($asin,'SITE')))->update(array('updated_at'=>date('Y-m-d H:i:s'),'status'=>array_get($asin,'ZSTATUS'),'seller'=>array_get($asin,'SELLER')));
				}else{
					DB::table('asin_seller_count')->insert(
						array(
							'site' => 'www.'.trim(array_get($asin,'SITE')),
							'asin' => trim(array_get($asin,'ASIN')),
							'created_at'=>date('Y-m-d H:i:s'),
							'updated_at'=>date('Y-m-d H:i:s'),
							'status'=>array_get($asin,'ZSTATUS'),
							'seller'=>array_get($asin,'SELLER'),
						)
					);
				}
			}
			
			
    	}
	}

}
