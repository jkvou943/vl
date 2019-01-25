<?php

namespace App\Http\Controllers;
use App\User;
use App\Asin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\MultipleQueue;
use PDO;
use DB;
class SalespController extends Controller
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
		$teams= DB::select('select bg,bu from asin group by bg,bu ORDER BY BG ASC,BU ASC');
		$addcolspans = DB::table('sales_prediction')->value('week_sales');
        return view('salesp/index',['teams'=>$teams,'addcolspans'=>unserialize($addcolspans),'users'=>$this->getUsers()]);
		

    }
	
	
	public function getUsers(){
        $users = User::Where('sap_seller_id','>',0)->orderBy('sap_seller_id','asc')->get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['sap_seller_id']] = $user['name'];
        }
        return $users_array;
    }

    
	
    public function get(Request $request)
    {
		$orderby = $request->input('order.0.column',1);
		if($orderby==0){
			$orderby = 'bg';
		}elseif($orderby==1){
			$orderby = 'sap_seller_id';
		}elseif($orderby==2){
			$orderby = 'sku';
		}elseif($orderby==3){
			$orderby = 'sap_site_id';
		}elseif($orderby==5){
			$orderby = 'sales_28_22';
		}elseif($orderby==6){
			$orderby = 'sales_21_15';
		}elseif($orderby==7){
			$orderby = 'sales_14_08';
		}elseif($orderby==8){
			$orderby = 'sales_07_01';
		}else{
			$orderby = 'sap_seller_id';
		}
        $sort = $request->input('order.0.dir','desc');
		$users= $this->getUsers();
		
		$datas= DB::table('sales_prediction');
               
        if($request->input('sap_seller_id')){
            $datas = $datas->where('sap_seller_id', $request->input('sap_seller_id'));
        }
		
		if($request->input('bgbu') ){
			   $bgbu = $request->input('bgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(count($bgbu_arr)>1){
			   	if(array_get($bgbu_arr,0)) $datas = $datas->where('bg',array_get($bgbu_arr,0));
			   	if(array_get($bgbu_arr,1)) $datas = $datas->where('bu',array_get($bgbu_arr,1));
			   }else{
			   		$datas = $datas->whereNull('bg');
			   }
		}
		if($request->input('sku')){
            $datas = $datas->where('sku', $request->input('sku'));
        }
		if($request->input('sap_site_id')){
            $datas = $datas->where('sap_site_id', $request->input('sap_site_id'));
        }
		
		$iTotalRecords = $datas->count();
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
		$lists =  $datas->orderBy($orderby,$sort)->offset($iDisplayStart)->limit($iDisplayLength)->get()->toArray();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

		
		$lists=json_decode(json_encode($lists), true);
		foreach ( $lists as $list){
			$sales_p = unserialize($list['week_sales']);
            $records["data"][] = array_merge(array(
				$list['bg'].' - '.$list['bu'],
                array_get($users,$list['sap_seller_id'],$list['sap_seller_id']),
				$list['sku'],
				
				array_get(getSapFactoryCode(),$list['sap_site_id']),
				$list['sku_des'],
				$list['sales_28_22'],
				$list['sales_21_15'],
				$list['sales_14_08'],
				$list['sales_07_01'],
            ),$sales_p);
		}
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }
	
	
}