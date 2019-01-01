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
class FeesController extends Controller
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
		$date_from=date('Y-m-d',strtotime('-90 days'));		
		$date_to=date('Y-m-d');	
	
		$teams= DB::select('select bg,bu from asin group by bg,bu ORDER BY BG ASC,BU ASC');

        return view('fees/index',['date_from'=>$date_from ,'date_to'=>$date_to,'teams'=>$teams,'accounts'=>$this->getSellerId(),'users'=>$this->getUsers()]);
		

    }
	
	public function getSellerId(){
		$seller=[];
		$accounts= DB::connection('order')->table('accounts')->where('status',1)->groupBy(['sellername','sellerid'])->get(['sellername','sellerid']);
		$accounts=json_decode(json_encode($accounts), true);
		foreach($accounts as $account){
			$seller[$account['sellerid']]=$account['sellername'];
		}
		return $seller;
	}
	
	public function getUsers(){
        $users = User::get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['id']] = $user['name'];
        }
        return $users_array;
    }

    
	
    public function getads(Request $request)
    {
		$orderby = $request->input('order.0.column',1);
		if($orderby==7){
			$orderby = 'TransactionValue';
		}else{
			$orderby = 'PostedDate';
		}
        $sort = $request->input('order.0.dir','desc');
		$users= $this->getUsers();
		$error_message='';
        if ($request->input("custombgbu") && $request->input("customsku") && $request->input("customActionType") == "group_action") {
			   $sap_seller_id =  Auth::user()->sap_seller_id;
			   $customskus = explode('/',trim($request->input("customsku")));
			   $exists_skus = Asin::whereIn('item_no',$customskus)->groupBy('item_no')->get(['item_no'])->count();

			   if( $sap_seller_id && $exists_skus == count($customskus)){
			   	   $updateDate = [];
				   $bgbu = $request->input('custombgbu');
				   $bgbu_arr = explode('_',$bgbu);
				   if(array_get($bgbu_arr,0)) $updateDate['bg'] = array_get($bgbu_arr,0);
				   if(array_get($bgbu_arr,1)) $updateDate['bu'] = array_get($bgbu_arr,1);
				   $updateDate['user_id'] = Auth::user()->id;
				   $updateDate['sku'] = $request->input("customsku");
				   DB::connection('order')->table('finances_product_ads_payment_event')->whereIn('id',$request->input("id"))->where('ImportToSap',0)->update($updateDate);
			   }else{
			   	   $error_message = 'Your account is not bound to the SAP Seller ID, or the entered SKU is invalid.';
			   } 
        }
		$date_from=$request->input('date_from')?$request->input('date_from'):date('Y-m-d',strtotime('- 90 days'));
        $date_to=$request->input('date_to')?$request->input('date_to'):date('Y-m-d');
		
		$datas= DB::connection('order')->table('finances_product_ads_payment_event')->where('PostedDate','>=',$date_from.'T00:00:00Z')->where('PostedDate','<=',$date_to.'T23:59:59Z');
               
        if($request->input('sellerid')){
            $datas = $datas->where('SellerId', $request->input('sellerid'));
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
		if($request->input('user_id')){
            $datas = $datas->where('user_id', $request->input('user_id'));
        }
		if($request->input('sku')){
            $datas = $datas->where('sku', $request->input('sku'));
        }
		
		if($request->input('invoiceid')){
            $datas = $datas->where('InvoiceId', $request->input('invoiceid'));
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
		$accounts = $this->getSellerId();
		
		$lists=json_decode(json_encode($lists), true);
		foreach ( $lists as $list){
			$disabled = ($list['ImportToSap'])?'disabled':'';
            $records["data"][] = array(
                '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" '.$disabled.' value="'.$list['Id'].'"  /><span></span></label>',
                $list['PostedDate'],
				array_get($accounts,$list['SellerId']),
				$list['InvoiceId'],
				$list['bg'].' - '.$list['bu'],
				array_get($users,$list['user_id'],''),
				$list['sku'],
				$list['TransactionValue'].' '.$list['Currency'],
            );
		}
        $records["draw"] = $sEcho;
		$records["cusErrorMessage"] = $error_message;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }
	
	
	
	public function getdeal(Request $request)
    {
		$orderby = $request->input('order.0.column',1);
		if($orderby==7){
			$orderby = 'TotalAmount';
		}else{
			$orderby = 'PostedDate';
		}
        $sort = $request->input('order.0.dir','desc');
		$users= $this->getUsers();
		$error_message='';
        if ($request->input("custombgbu") && $request->input("customsku") && $request->input("customActionType") == "group_action") {
			$sap_seller_id = Auth::user()->sap_seller_id;
			$customskus = explode('/',trim($request->input("customsku")));
			   $exists_skus = Asin::whereIn('item_no',$customskus)->groupBy('item_no')->get(['item_no'])->count();

			   if( $sap_seller_id && $exists_skus == count($customskus)){
			   $updateDate = [];
               $bgbu = $request->input('custombgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(array_get($bgbu_arr,0)) $updateDate['bg'] = array_get($bgbu_arr,0);
			   if(array_get($bgbu_arr,1)) $updateDate['bu'] = array_get($bgbu_arr,1);
			   $updateDate['user_id'] = Auth::user()->id;
			   $updateDate['sku'] = trim($request->input("customsku"));
			    DB::connection('order')->table('finances_deal_event')->whereIn('id',$request->input("id"))->where('ImportToSap',0)->update($updateDate);
			}else{
			   $error_message = 'Your account is not bound to the SAP Seller ID, or the entered SKU is invalid.';
			} 
        }
		$date_from=$request->input('date_from')?$request->input('date_from'):date('Y-m-d',strtotime('- 90 days'));
        $date_to=$request->input('date_to')?$request->input('date_to'):date('Y-m-d');
		
		$datas= DB::connection('order')->table('finances_deal_event')->where('PostedDate','>=',$date_from.'T00:00:00Z')->where('PostedDate','<=',$date_to.'T23:59:59Z');
               
        if($request->input('sellerid')){
            $datas = $datas->where('SellerId', $request->input('sellerid'));
        }
		
		if($request->input('bgbu')){
			   $bgbu = $request->input('bgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(count($bgbu_arr)>1){
			   	if(array_get($bgbu_arr,0)) $datas = $datas->where('bg',array_get($bgbu_arr,0));
			   	if(array_get($bgbu_arr,1)) $datas = $datas->where('bu',array_get($bgbu_arr,1));
			   }else{
			   		$datas = $datas->whereNull('bg');
			   }
		}
		if($request->input('user_id')){
            $datas = $datas->where('user_id', $request->input('user_id'));
        }
		if($request->input('sku')){
            $datas = $datas->where('sku', $request->input('sku'));
        }
		
		if($request->input('feedes')){
            $datas = $datas->where('DealDescription','like','%'.$request->input('feedes').'%');
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
		$accounts = $this->getSellerId();
		
		$lists=json_decode(json_encode($lists), true);
		foreach ( $lists as $list){
			$disabled = ($list['ImportToSap'])?'disabled':'';
            $records["data"][] = array(
                '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$list['Id'].'" '.$disabled.' /><span></span></label>',
                $list['PostedDate'],
				array_get($accounts,$list['SellerId']),
				$list['DealDescription'],
				$list['bg'].' - '.$list['bu'],
				array_get($users,$list['user_id'],''),
				$list['sku'],
				$list['TotalAmount'].' '.$list['Currency'],
            );
		}
        $records["draw"] = $sEcho;
		$records["cusErrorMessage"] = $error_message;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }
	
	
	public function getcoupon(Request $request)
    {
		$orderby = $request->input('order.0.column',1);
		if($orderby==7){
			$orderby = 'TotalAmount';
		}else{
			$orderby = 'PostedDate';
		}
        $sort = $request->input('order.0.dir','desc');
		$users= $this->getUsers();
		$error_message='';
        if ($request->input("custombgbu") && $request->input("customsku") && $request->input("customActionType") == "group_action") {
			   $sap_seller_id = Auth::user()->sap_seller_id;
			$customskus = explode('/',trim($request->input("customsku")));
			   $exists_skus = Asin::whereIn('item_no',$customskus)->groupBy('item_no')->get(['item_no'])->count();

			   if( $sap_seller_id && $exists_skus == count($customskus)){
				$updateDate = [];
               $bgbu = $request->input('custombgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(array_get($bgbu_arr,0)) $updateDate['bg'] = array_get($bgbu_arr,0);
			   if(array_get($bgbu_arr,1)) $updateDate['bu'] = array_get($bgbu_arr,1);
			   $updateDate['user_id'] = Auth::user()->id;
			   $updateDate['sku'] = $request->input("customsku");
			    DB::connection('order')->table('finances_coupon_event')->whereIn('id',$request->input("id"))->where('ImportToSap',0)->update($updateDate);
				}else{
			   	  $error_message = 'Your account is not bound to the SAP Seller ID, or the entered SKU is invalid.';
			   } 
        }
		$date_from=$request->input('date_from')?$request->input('date_from'):date('Y-m-d',strtotime('- 90 days'));
        $date_to=$request->input('date_to')?$request->input('date_to'):date('Y-m-d');
		
		$datas= DB::connection('order')->table('finances_coupon_event')->where('PostedDate','>=',$date_from.'T00:00:00Z')->where('PostedDate','<=',$date_to.'T23:59:59Z');
               
        if($request->input('sellerid')){
            $datas = $datas->where('SellerId', $request->input('sellerid'));
        }
		
		if($request->input('bgbu')){
			   $bgbu = $request->input('bgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(count($bgbu_arr)>1){
			   	if(array_get($bgbu_arr,0)) $datas = $datas->where('bg',array_get($bgbu_arr,0));
			   	if(array_get($bgbu_arr,1)) $datas = $datas->where('bu',array_get($bgbu_arr,1));
			   }else{
			   		$datas = $datas->whereNull('bg');
			   }
		}
		if($request->input('user_id')){
            $datas = $datas->where('user_id', $request->input('user_id'));
        }
		if($request->input('sku')){
            $datas = $datas->where('sku', $request->input('sku'));
        }
		
		if($request->input('feedes')){
            $datas = $datas->where('SellerCouponDescription','like','%'.$request->input('feedes').'%');
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
		$accounts = $this->getSellerId();
		
		$lists=json_decode(json_encode($lists), true);
		foreach ( $lists as $list){
			$disabled = ($list['ImportToSap'])?'disabled':'';
            $records["data"][] = array(
                '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$list['Id'].'" '.$disabled.' /><span></span></label>',
                $list['PostedDate'],
				array_get($accounts,$list['SellerId']),
				$list['SellerCouponDescription'],
				$list['bg'].' - '.$list['bu'],
				array_get($users,$list['user_id'],''),
				$list['sku'],
				$list['TotalAmount'].' '.$list['Currency'],
            );
		}
        $records["draw"] = $sEcho;
		$records["cusErrorMessage"] = $error_message;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }
	
	public function getservice(Request $request)
    {
		$orderby = $request->input('order.0.column',1);
		if($orderby==7){
			$orderby = 'Amount';
		}else{
			$orderby = 'PostedDate';
		}
        $sort = $request->input('order.0.dir','desc');
		$users= $this->getUsers();
		$error_message='';
        if ($request->input("custombgbu") && $request->input("customsku") && $request->input("customActionType") == "group_action") {
			$sap_seller_id = Auth::user()->sap_seller_id;
			$customskus = explode('/',trim($request->input("customsku")));
			   $exists_skus = Asin::whereIn('item_no',$customskus)->groupBy('item_no')->get(['item_no'])->count();

			   if( $sap_seller_id && $exists_skus == count($customskus)){
				$updateDate = [];
               $bgbu = $request->input('custombgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(array_get($bgbu_arr,0)) $updateDate['bg'] = array_get($bgbu_arr,0);
			   if(array_get($bgbu_arr,1)) $updateDate['bu'] = array_get($bgbu_arr,1);
			   $updateDate['user_id'] = Auth::user()->id;
			   $updateDate['sku'] = $request->input("customsku");
			    DB::connection('order')->table('finances_servicefee_event')->whereIn('id',$request->input("id"))->where('ImportToSap',0)->update($updateDate);
			}else{
			   	   $error_message = 'Your account is not bound to the SAP Seller ID, or the entered SKU is invalid.';
			   } 
        }
		$date_from=$request->input('date_from')?$request->input('date_from'):date('Y-m-d',strtotime('- 90 days'));
        $date_to=$request->input('date_to')?$request->input('date_to'):date('Y-m-d');
		
		$datas= DB::connection('order')->table('finances_servicefee_event')->where('PostedDate','>=',$date_from.'T00:00:00Z')->where('PostedDate','<=',$date_to.'T23:59:59Z');
               
        if($request->input('sellerid')){
            $datas = $datas->where('SellerId', $request->input('sellerid'));
        }
		
		if($request->input('bgbu')){
			   $bgbu = $request->input('bgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(count($bgbu_arr)>1){
			   	if(array_get($bgbu_arr,0)) $datas = $datas->where('bg',array_get($bgbu_arr,0));
			   	if(array_get($bgbu_arr,1)) $datas = $datas->where('bu',array_get($bgbu_arr,1));
			   }else{
			   		$datas = $datas->whereNull('bg');
			   }
		}
		if($request->input('user_id')){
            $datas = $datas->where('user_id', $request->input('user_id'));
        }
		if($request->input('sku')){
            $datas = $datas->where('sku', $request->input('sku'));
        }
		
		if($request->input('feedes')){
            $datas = $datas->where('Type','like','%'.$request->input('feedes').'%');
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
		$accounts = $this->getSellerId();
		
		$lists=json_decode(json_encode($lists), true);
		foreach ( $lists as $list){
			$disabled = ($list['ImportToSap'])?'disabled':'';
            $records["data"][] = array(
                '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$list['Id'].'" '.$disabled.' /><span></span></label>',
                $list['PostedDate'],
				array_get($accounts,$list['SellerId']),
				$list['Type'],
				$list['bg'].' - '.$list['bu'],
				array_get($users,$list['user_id'],''),
				$list['sku'],
				$list['Amount'].' '.$list['Currency'],
            );
		}
        $records["draw"] = $sEcho;
		$records["cusErrorMessage"] = $error_message;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }
	
	
	public function getcpc(Request $request)
    {	
		$users= $this->getUsers();
		$error_message='';
        if ($request->input("custombgbu") && $request->input("customsku") && $request->input("customActionType") == "group_action") {
			$sap_seller_id = Auth::user()->sap_seller_id;
			$customskus = explode('/',trim($request->input("customsku")));
			   $exists_skus = Asin::whereIn('item_no',$customskus)->groupBy('item_no')->get(['item_no'])->count();

			   if( $sap_seller_id && $exists_skus == count($customskus)){
				$updateDate = [];
               $bgbu = $request->input('custombgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(array_get($bgbu_arr,0)) $updateDate['bg'] = array_get($bgbu_arr,0);
			   if(array_get($bgbu_arr,1)) $updateDate['bu'] = array_get($bgbu_arr,1);
			   $updateDate['user_id'] = Auth::user()->id;
			   $updateDate['sku'] = $request->input("customsku");
			    DB::table('aws_report')->whereIn('id',$request->input("id"))->where('ImportToSap',0)->update($updateDate);
			}else{
			   	   $error_message = 'Your account is not bound to the SAP Seller ID, or the entered SKU is invalid.';
			   } 
        }
		$date_from=$request->input('date_from')?$request->input('date_from'):date('Y-m-d',strtotime('- 90 days'));
        $date_to=$request->input('date_to')?$request->input('date_to'):date('Y-m-d');
		
		$datas= DB::table('aws_report')->where('date','>=',$date_from)->where('date','<=',$date_to);
               
        if($request->input('sellerid')){
            $datas = $datas->where('seller_id', $request->input('sellerid'));
        }
		
		if($request->input('bgbu')){
			   $bgbu = $request->input('bgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(count($bgbu_arr)>1){
			   	if(array_get($bgbu_arr,0)) $datas = $datas->where('bg',array_get($bgbu_arr,0));
			   	if(array_get($bgbu_arr,1)) $datas = $datas->where('bu',array_get($bgbu_arr,1));
			   }else{
			   		$datas = $datas->where('bg','');
			   }
		}
		if($request->input('user_id')){
            $datas = $datas->where('user_id', $request->input('user_id'));
        }
		if($request->input('status')){
            $datas = $datas->where('state', $request->input('status'));
        }
		
		if($request->input('sku')){
            $datas = $datas->where('sku', $request->input('sku'));
        }
		
		if($request->input('feedes1')){
            $datas = $datas->where('campaign_name','like','%'.$request->input('feedes1').'%');
        }
		if($request->input('feedes2')){
            $datas = $datas->where('ad_group','like','%'.$request->input('feedes2').'%');
        }
		$iTotalRecords = $datas->count();
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
		$lists =  $datas->orderBy('date','desc')->offset($iDisplayStart)->limit($iDisplayLength)->get()->toArray();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
		$accounts = $this->getSellerId();
		
		$lists=json_decode(json_encode($lists), true);
		foreach ( $lists as $list){
			$disabled = ($list['ImportToSap'])?'disabled':'';
            $records["data"][] = array(
                '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$list['id'].'" '.$disabled.' /><span></span></label>',
                $list['date'],
				array_get($accounts,$list['seller_id']),
				$list['campaign_name'],
				$list['ad_group'],
				$list['sales'],
				$list['profit'],
				$list['orders'],
				$list['state'],
				$list['bg'].' - '.$list['bu'],
				array_get($users,$list['user_id'],''),
				$list['sku'],
				$list['cost']
            );
		}
        $records["draw"] = $sEcho;
		$records["cusErrorMessage"] = $error_message;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }
	
}