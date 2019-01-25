<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Input;
use App\Sales28day;
use App\Asin;
use PDO;
use DB;
use Log;

class GetSales28day extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:dailysales {before}';

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
		$before =  abs(intval($this->argument('before')));
		while($before>=1){
			$date=date('Y-m-d',strtotime('-'.$before.' days'));
			$sales = DB::connection('order')->select('select sum(quantityordered) as sale,sellersku,marketplaceid from amazon_orders_item where AmazonOrderId in (select AmazonOrderId from amazon_orders where left(PurchaseDate,10)=:date)
 group by sellersku,marketplaceid',['date' => $date]);
			foreach($sales as $sale){
				Sales28day::updateOrCreate(
				[
					'seller_sku' => $sale->sellersku,
					'site_id' => array_get(matchMarketplaceSiteCode(),$sale->marketplaceid,''),
					'date'=> $date],[
					'qty' => intval($sale->sale)
				]);
			}
			$before--;
		}
		Sales28day::where('date','<',date('Y-m-d',strtotime('-30 days')))->delete();
		DB::update("update asin set sales_28_22 = IFNULL((select sum(qty) from sales_28_day where date BETWEEN '".date('Y-m-d',strtotime('-28 days'))."' and '".date('Y-m-d',strtotime('-22 days'))."'
and asin.sellersku=sales_28_day.seller_sku and asin.sap_site_id=sales_28_day.site_id),0),sales_21_15 = IFNULL((select sum(qty) from sales_28_day where date BETWEEN '".date('Y-m-d',strtotime('-21 days'))."' and '".date('Y-m-d',strtotime('-15 days'))."'
and asin.sellersku=sales_28_day.seller_sku and asin.sap_site_id=sales_28_day.site_id),0),sales_14_08 = IFNULL((select sum(qty) from sales_28_day where date BETWEEN '".date('Y-m-d',strtotime('-14 days'))."' and '".date('Y-m-d',strtotime('-8 days'))."'
and asin.sellersku=sales_28_day.seller_sku and asin.sap_site_id=sales_28_day.site_id),0),sales_07_01 = IFNULL((select sum(qty) from sales_28_day where date BETWEEN '".date('Y-m-d',strtotime('-7 days'))."' and '".date('Y-m-d',strtotime('-1 days'))."'
and asin.sellersku=sales_28_day.seller_sku and asin.sap_site_id=sales_28_day.site_id),0)");
		DB::table('sales_prediction')->truncate();
		$skus = DB::select("select item_no as sku,sap_site_id,min(item_group) as item_group,min(bg) as bg ,min(bu) as bu,min(sap_seller_id) as sap_seller_id,GROUP_CONCAT(sellersku) as seller_skus
,sum(sales_28_22) as sales_28_22
,sum(sales_21_15) as sales_21_15
,sum(sales_14_08) as sales_14_08
,sum(sales_07_01) as sales_07_01
from asin where(sales_28_22+sales_21_15+sales_14_08+sales_07_01)>0 group by item_no,sap_site_id");
		$skus_data =json_decode(json_encode($skus),true);
		
		$I_TAB=[];
		foreach($skus_data as $sku_data){
			$I_TAB[]=array(
				'VKBUR'=>$sku_data['sap_site_id'],
				'SITE'=>array_get(getSapSiteCode(),$sku_data['sap_site_id']),
				'MATKL'=>$sku_data['item_group'],
				'MATNR'=>$sku_data['sku'],
				'WERKS'=>array_get(getSapFactoryCode(),$sku_data['sap_site_id']),
				'SOLD1'=>$sku_data['sales_28_22'],
				'SOLD2'=>$sku_data['sales_21_15'],
				'SOLD3'=>$sku_data['sales_14_08'],
				'SOLD4'=>$sku_data['sales_07_01'],
			);
		}
		
		$appkey = 'site0001';
		$appsecret= 'testsite0001';
		$array['sku']='getSalesP';
		$array['appid']= $appkey;
		$array['method']='getSalesP';
		ksort($array);
		$authstr = "";
		foreach ($array as $k => $v) {
			$authstr = $authstr.$k.$v;
		}
		$array['I_TAB']=$I_TAB;
		$authstr=$authstr.$appsecret;
		$sign = strtoupper(sha1($authstr));
		$array['sign']=$sign;
		$res = curl_request('http://116.6.105.153:18003/rfc_site.php',$array);
		$result = json_decode($res,true);
		$Lists = array_get($result,'data.O_TAB');
		$sku_sales_p=$skus_s =[];
		foreach($Lists as $list){
			$sku_sales_p[$list['MATNR'].'_'.$list['VKBUR']][$list['ZWEEK']]=intval($list['MENGE']);
			$skus_s[$list['MATNR']]=$list['MATNR'];
		}
		$i=0;
		$skus_name = DB::table('fbm_stock')->pluck('item_name','item_code');
		
		foreach($skus_data as $sku_data){
			$skus_data[$i]['week_sales'] = serialize(array_get($sku_sales_p,strtoupper($sku_data['sku']).'_'.$sku_data['sap_site_id'],[]));
			$skus_data[$i]['date'] = date('Y-m-d H:i:s');
			$skus_data[$i]['sku_des'] = array_get($skus_name,strtoupper($sku_data['sku']),'');
			$i++;
		}
		DB::table('sales_prediction')->insert($skus_data);
	}

}
