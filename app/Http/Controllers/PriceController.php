<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Accounts;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PDO;
use DB;
use Log;
class PriceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     *
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		return view('price/index');
    }
	
	public function get(Request $request){
		$data = $request->get('data');
		$array = $priceGroup = $monthGroup = $normalGroup= [];
		
		foreach($data as $val){
			if(substr($val['name'],0,11)=='price-group'){
				$vm = explode('][',$val['name']);
				$index = str_replace('price-group[','',array_get($vm,0)); 
				$key = str_replace(']','',array_get($vm,1)); 
				$priceGroup[str_replace('price-group[','',array_get($vm,0))][str_replace(']','',array_get($vm,1))] = (str_replace(']','',array_get($vm,1))=='ZLX')?trim($val['value']):round($val['value'],2);
			}elseif(substr($val['name'],0,6)=='month-'){
				$vm = explode('-',$val['name']);
				$monthGroup[array_get($vm,2)][array_get($vm,1)] = round($val['value'],2);
				$monthGroup[array_get($vm,2)]['MONTH2'] = sprintf("%02d",round(array_get($vm,2),2));
			}else{
				$normalGroup[$val['name']]=($val['name']=='I_MATNR' || $val['name']=='I_ZCPLX')?trim($val['value']):round($val['value'],2);
			}	
		}
		$appkey = 'site0001';
		$appsecret= 'testsite0001';
		$array['sku']=array_get($normalGroup,'I_MATNR');
		$array['appid']= $appkey;
		$array['method']='getPrice';
		ksort($array);
		$authstr = "";
		foreach ($array as $k => $v) {
			$authstr = $authstr.$k.$v;
		}
		$array['I_TAB2']=$monthGroup;
		$array['I_TAB1']=$priceGroup;
		$array['IMPORT']=$normalGroup;
		$authstr=$authstr.$appsecret;
		$sign = strtoupper(sha1($authstr));
		$array['sign']=$sign;
		$res = curl_request('http://116.6.105.153:18003/rfc_site.php',$array);
		die($res);
	}
	
	
	
	public function getStockAge(Request $request){
		$saprfc = new \App\Classes\SapRfcRequest();
		$res = $saprfc->getStockAge(['sku' => strtoupper($request->get('sku')),'site' => strtoupper($request->get('site'))]);
		die(json_encode((array_get($res,'1',[]))));
	}
}