<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inbox;
use App\Sendbox;
use App\Accounts;
use Illuminate\Support\Facades\Session;
use App\Asin;
use App\User;
use App\Exception;
use App\Group;
use App\Groupdetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\MultipleQueue;
use PDO;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExceptionController extends Controller
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
    public function index($type = '')
    {
        return view('exception/index',['users'=>$this->getUsers(),'groups'=>$this->getGroups(),'mygroups'=>$this->getUserGroup(),'sellerids'=>$this->getSellerIds()]);

    }
	
	public function export(Request $request){
		//if(Auth::user()->admin){
            $customers = new Exception;
        //}else{
		//	$mgroup_ids  = array_get($this->getUserGroup(),'manage_groups',array());
		//	$user_id  = Auth::user()->id;
		//	$customers = Exception::where(function ($query) use ($mgroup_ids,$user_id) {
        //        $query->whereIn('group_id'  , $mgroup_ids)
		//				  ->orwhere('user_id', $user_id);
//
        //    });
        //}
		if(array_get($_REQUEST,'type')){
            $customers = $customers->where('type', array_get($_REQUEST,'type'));
        }
        if(isset($_REQUEST['status']) && $_REQUEST['status']!=''){
            $customers = $customers->where('process_status', $_REQUEST['status']);
        }
        //if(Auth::user()->admin) {
		
			if (array_get($_REQUEST, 'group_id')) {
				
                $customers = $customers->where('group_id', array_get($_REQUEST, 'group_id'));

            }
            if (array_get($_REQUEST, 'user_id')) {
				$customers = $customers->where('user_id',  array_get($_REQUEST, 'user_id'));
            }
        //}
		
        if(array_get($_REQUEST,'sellerid')){
            $customers = $customers->where('sellerid',  array_get($_REQUEST, 'sellerid'));
			
        }
        if(array_get($_REQUEST,'amazon_order_id')){
            $customers = $customers->where('amazon_order_id', array_get($_REQUEST, 'amazon_order_id'));
        }
		

        if(array_get($_REQUEST,'order_sku')){
            $customers = $customers->where('order_sku', 'like', '%'.$_REQUEST['order_sku'].'%');
           
        }
        if(array_get($_REQUEST,'date_from')){
            $customers = $customers->where('date','>=',$_REQUEST['date_from'].' 00:00:00');
        }
        if(array_get($_REQUEST,'date_to')){
            $customers = $customers->where('date','<=',$_REQUEST['date_to'].' 23:59:59');
        }
		$customersLists =  $customers->orderBy('date','desc')->get()->toArray();
		$arrayData = $arrayAmazon = $arraySap = array();
		$headArray[] = 'Account';
		$headArray[] = 'Amazon OrderID';
		$headArray[] = 'Type';
		$headArray[] = 'Customer Name';
		$headArray[] = 'Order Skus';
		$headArray[] = 'Create Date';
		$headArray[] = 'Status';
		$headArray[] = 'Operate';
		$headArray[] = 'Ship Name';
		$headArray[] = 'Address1';
		$headArray[] = 'Address2';
		$headArray[] = 'Address3';
		$headArray[] = 'City';
		$headArray[] = 'County';
		$headArray[] = 'State';
		$headArray[] = 'District';
		$headArray[] = 'PostalCode';
		$headArray[] = 'Country';
		$headArray[] = 'Phone';
		$headArray[] = 'Reson';
		$headArray[] = 'Operator';
		$headArray[] = 'Group';
		$headArray[] = 'Creator';
		
		$arrayAmazon[] =['Status','Account','Returned/Urgent','MerchantFulfillmentOrderID','DisplayableOrderID','DisplayableOrderDate','MerchantSKU','Quantity','MerchantFulfillmentOrderItemID','GiftMessage','DisplayableComment','PerUnitDeclaredValue','DisplayableOrderComment','DeliverySLA','AddressName','AddressFieldOne','AddressFieldTwo','AddressFieldThree','AddressCity','AddressCountryCode','AddressStateOrRegion','AddressPostalCode','AddressPhoneNumber','NotificationEmail','FulfillmentAction','MarketplaceID'];
		
		$arraySap[] =['Status','Returned/Urgent','平台编号','站点','平台订单号','售达方','订单类型','订单交易号','付款日期','付款交易ID(不能重复)','买家ID','买家姓名','国家代码','城市名','州/省','街道1','街道2','邮编','邮箱','电话1','成交费','货币','佣金','货币','订单总价','货币','实际运输方式','平台订单号','站点','行号','SAP物料号','数量','工厂','仓库','行项目ID','帖子ID','帖子标题','销售员编号','行交易ID','标记完成'];

		$arrayData[] = $headArray;
		$users=$this->getUsers();
		$groups = $this->getGroups();
		$groupleaders = $this->getGroupLeader();
		$accounts = $this->getSellerIds();
        $status_list['done'] = "Done";
        $status_list['cancel'] = "Cancelled";
        $status_list['submit'] = "Processing";
		$type_list['1'] = "Refund";
        $type_list['2'] = "Replacement";
        $type_list['3'] = "Refund & Replacement";
		foreach ( $customersLists as $customersList){
			$operate = '';
			$replacements = array();
			if($customersList['type']==1 || $customersList['type']==3) $operate.= 'Refund : '.$customersList['refund'].PHP_EOL;
			if($customersList['type']==2 || $customersList['type']==3){
				$operate.= 'Replace : ';
				$replacements = unserialize($customersList['replacement']);
				$products = array_get($replacements,'products',array());
				if(is_array($products)){
					$sap_line_num=0;
					foreach( $products as $product){
						$sap_line_num+=10;
						$operate.= ($product['seller_sku']??$product['note']??$product['sku']??'').' ( '.(array_get($product,'item_code')??array_get($product,'title')??'').' ) * '.array_get($product,'qty').'; ';
						if($customersList['process_status']!='cancel'){
						if(array_get($product,'seller_id') && array_get($accounts,array_get($product,'seller_id'))){
							$arrayAmazon[] =[
								$customersList['process_status'],
								array_get($accounts,array_get($product,'seller_id')),
								implode(', ',array_get($product,'addattr',[])),
								array_get($product,'replacement_order_id'),
								array_get($product,'replacement_order_id'),
								array_get($customersList,'date'),
								array_get($product,'seller_sku'),
								array_get($product,'qty'),
								array_get($product,'item_code'),
								'',
								'',
								'',
								'Thank you for the order.',
								'Standard',
								array_get($replacements,'shipname'),
								array_get($replacements,'address1'),
								array_get($replacements,'address2'),
								array_get($replacements,'address3'),
								array_get($replacements,'city'),
								array_get($replacements,'countrycode'),
								array_get($replacements,'state'),
								array_get($replacements,'postalcode'),
								'',
								'',
								'',
								''
							];
						}
						$o_sap_seller_id = Asin::where('sellersku',array_get(explode('*',$customersList['order_sku']),0))->value('sap_seller_id');
						if($sap_sku_info = Asin::where('sellersku',array_get($product,'seller_sku'))->first()){
							$sap_sku_info = $sap_sku_info->toArray();
						}else{
							$sap_sku_info=[];
						}
						
						$arraySap[] =[
							$customersList['process_status'],
							implode(', ',array_get($product,'addattr',[])),
							'11',
							array_get($sap_sku_info,'sap_site_id'),
							$customersList['amazon_order_id'],
							array_get($sap_sku_info,'sap_store_id'),
							'ZRR1',
							'',
							'',
							'',
							'',
							array_get($replacements,'shipname'),
							array_get($replacements,'countrycode'),
							array_get($replacements,'city'),
							array_get($replacements,'state'),
							array_get($replacements,'address1'),
							array_get($replacements,'address2'),
							array_get($replacements,'postalcode'),
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							'',
							array_get($sap_sku_info,'sap_shipment_id'),
							$customersList['amazon_order_id'],
							array_get($sap_sku_info,'sap_site_id'),
							$sap_line_num,
							strtoupper(array_get($product,'item_code')),
							array_get($product,'qty'),
							array_get($sap_sku_info,'sap_factory_id'),
							array_get($sap_sku_info,'sap_warehouse_id'),
							'',
							'',
							'',
							$o_sap_seller_id,
							'',
							''
						];
						}
					}
				}
			}
            if($customersList['type']==4) $operate.= 'Gift Card : '.$customersList['gift_card_amount'].PHP_EOL;

            $arrayData[] = array(
				array_get($accounts,$customersList['sellerid']),
                $customersList['amazon_order_id'],
                array_get($type_list,$customersList['type']),
				$customersList['name'],
                $customersList['order_sku'],
				$customersList['date'],
                array_get($status_list,$customersList['process_status']),
				$operate,
				array_get($replacements,'shipname'),
				array_get($replacements,'address1'),
				array_get($replacements,'address2'),
				array_get($replacements,'address3'),
				array_get($replacements,'city'),
				array_get($replacements,'county'),
				array_get($replacements,'state'),
				array_get($replacements,'district'),
				array_get($replacements,'postalcode'),
				array_get($replacements,'countrycode'),
				array_get($replacements,'phone'),
				$customersList['request_content'],
				array_get($users,$customersList['process_user_id'])?array_get($users,$customersList['process_user_id']):array_get($groupleaders,$customersList['group_id']),
                array_get($groups,$customersList['group_id'].'.group_name'),
				array_get($users,$customersList['user_id'])
            );
		}

		if($arrayData){

			$spreadsheet = new Spreadsheet();
			
			$spreadsheet->getActiveSheet()
				->fromArray(
					$arrayData,  // The data to set
					NULL,        // Array values with this value will not be set
					'A1'         // Top left coordinate of the worksheet range where
								 //    we want to set these values (default is A1)
				);
			$myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Replacement For Amazon');
			$spreadsheet->addSheet($myWorkSheet, 1)->fromArray(
						$arrayAmazon,  // The data to set
						NULL,        // Array values with this value will not be set
						'A1'         // Top left coordinate of the worksheet range where
									 //    we want to set these values (default is A1)
					);
			$myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Replacement For SAP');
			$spreadsheet->addSheet($myWorkSheet, 2)->fromArray(
						$arraySap,  // The data to set
						NULL,        // Array values with this value will not be set
						'A1'         // Top left coordinate of the worksheet range where
									 //    we want to set these values (default is A1)
					);
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
			header('Content-Disposition: attachment;filename="Export_'.array_get($_REQUEST,'ExportType').'.xlsx"');//告诉浏览器输出浏览器名称
			header('Cache-Control: max-age=0');//禁止缓存
			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');
		}
		die();
	}

    public function create()
    {
        $vars = ['groups'=>$this->getGroups(),'mygroups'=>$this->getUserGroup(),'sellerids'=>$this->getSellerIds()];

        $vars['requestContentHistoryValues'] = [];
        // array_map(function ($row) {
        //     return $row['rc'];
        // }, Exception::selectRaw('DISTINCT request_content AS rc')->get()->toArray());

        array_push(
            $vars['requestContentHistoryValues'],
            'Change Request Of Order Info',
            'Damage In Transit',
            'Exchange Request',
            'Free Order',
            'Gift',
            'Listing Error',
            'Lost In Transit',
            'Original Info Error',
            'Out Of Stock',
            'Qulity Issue',
            'Return Request',
            'Ship Wrong Item',
            'Shipping Delay',
            'Unsatisfied Customer Service',
            'Other Shipping Issue'
        );

        return view('exception/add', $vars);
    }

     public function edit(Request $request,$id)
    {
        //if(Auth::user()->admin){
			$rule= Exception::where('id',$id)->first();
		//}else{
//			$mgroup_ids  = array_get($this->getUserGroup(),'manage_groups',array());
//			$user_id  = Auth::user()->id;
//			$rule = Exception::where(function ($query) use ($mgroup_ids,$user_id) {
//
//                $query->whereIn('group_id'  , $mgroup_ids)
//						  ->orwhere('user_id', $user_id);
//
//            })->where('id',$id)->first();
//		}
        
        if(!$rule){
            $request->session()->flash('error_message','Exception not Exists');
            return redirect('exception');
        }
		$rule= $rule->toArray();
		//$account = Accounts::where('account_sellerid',array_get($rule,'sellerid'))->first();
		$last_inboxid=0;
	
		$last_inbox = Inbox::where('amazon_seller_id',array_get($rule,'sellerid'))->where('amazon_order_id',array_get($rule,'amazon_order_id'))->orderBy('date','desc')->first();
		if($last_inbox) $last_inboxid= $last_inbox->id;
		$replacement_order_ids=[];
		if($rule['type']==2 || $rule['type']==3){
			$replacements = unserialize($rule['replacement']);
			$products = array_get($replacements,'products',array());
			if(is_array($products)){
				foreach( $products as $product){
					$replacement_order_ids[]=array_get($product,'replacement_order_id');
				}
			}
		}	
					
		$mcf_orders = DB::connection('order')->table('amazon_mcf_shipment_package')->whereIn('SellerFulfillmentOrderId',$replacement_order_ids)->get();
		
		if($last_inbox) $last_inboxid= $last_inbox->id;
		
        return view('exception/edit',['exception'=>$rule,'groups'=>$this->getGroups(),'mygroups'=>$this->getUserGroup(),'sellerids'=>$this->getSellerIds(),'last_inboxid'=>$last_inboxid,'mcf_orders'=>$mcf_orders]);
    }

    public function update(Request $request,$id)
    {
		$exception = Exception::findOrFail($id);
		if($exception->process_status=='submit' && $request->get('process_status')!='submit'){
			$this->validate($request, [
				'process_status' => 'required|string',
			]);
			$exception->process_content = $request->get('process_content');
			$exception->process_status = $request->get('process_status');
			$exception->process_date = date('Y-m-d H:i:s');
			$exception->process_user_id = intval(Auth::user()->id);
			if($exception->type==2 || $exception->type==3){
				$replacements = unserialize($exception->replacement);
				$products=[];
				$products_arr = array_get($replacements,'products',array());
				if(is_array($products_arr)){
					$id_add=0;
					foreach( $products_arr as $product_arr){
						$product_arr['replacement_order_id']=$request->input('replacement_order_id.'.$id_add);	
						$products[]=$product_arr;
						$id_add++;
					}
				}
				$replacements['products']=$products;
				$exception->replacement =  serialize($replacements);
			}
			$file = $request->file('importFile');  
  			if($file){
				if($file->isValid()){  
					$originalName = $file->getClientOriginalName();  
					$ext = $file->getClientOriginalExtension();  
					$type = $file->getClientMimeType();  
					$realPath = $file->getRealPath();  
					$newname = date('Y-m-d-H-i-S').'-'.uniqid().'.'.$ext;  
					$newpath = '/uploads/exceptionUpload/'.date('Ymd').'/';
					$inputFileName = public_path().$newpath.$newname;
					$bool = $file->move(public_path().$newpath,$newname);
					if($bool) $exception->process_attach = $newpath.$newname;
				}
			}
			if ($exception->save()) {
				return redirect('exception/'.$id.'/edit');
			} else {
				$request->session()->flash('error_message','Set Failed');
				return redirect()->back()->withInput();
			}
		}
		if($exception->process_status=='cancel'){
			 $this->validate($request, [
				'group_id' => 'required|string',
				'name' => 'required|string',
				'rebindordersellerid' => 'required|string',
				'rebindorderid' => 'required|string',
				'type' => 'required|string',
			]);
			$exception->type = $request->get('type');
			$exception->name = $request->get('name');
			$exception->order_sku = $request->get('order_sku');
			$exception->date = date('Y-m-d H:i:s');
			$exception->sellerid = $request->get('rebindordersellerid');
			$exception->amazon_order_id = $request->get('rebindorderid');
			$exception->group_id = $request->get('group_id');
			$exception->user_id = intval(Auth::user()->id);
			$exception->request_content = $request->get('request_content');
			$exception->process_status = 'submit';
			if( $exception->type == 1 || $exception->type == 3){
				$exception->refund = $request->get('refund');
			}else{
				$exception->refund = 0;
			}
	
			if( $exception->type == 2 || $exception->type == 3){
				
				$products=[];
				$products_arr = $request->get('group-products');
	
				$id_add=0;
				foreach($products_arr as $product_arr){
					$id_add++;
					if(array_get($product_arr,'seller_id')==$request->get('rebindordersellerid')){
						$product_arr['replacement_order_id']=$request->get('rebindorderid').'-0'.$id_add;
					}else{
						$product_arr['replacement_order_id']=substr($request->get('rebindorderid'),4).'-0'.$id_add;	
					}		
					$products[]=$product_arr;
				}
				$exception->replacement = serialize(
				array(
					'shipname'=>$request->get('shipname'),
					'address1'=>$request->get('address1'),
					'address2'=>$request->get('address2'),
					'address3'=>$request->get('address3'),
					'city'=>$request->get('city'),
					'county'=>$request->get('county'),
					'state'=>$request->get('state'),
					'district'=>$request->get('district'),
					'postalcode'=>$request->get('postalcode'),
					'countrycode'=>$request->get('countrycode'),
					'phone'=>$request->get('phone'),
					'products'=>$products,
				));
			}else{
				$exception->replacement = '';
			}
	
			if ($exception->save()) {
				return redirect('exception/'.$id.'/edit');
			} else {
				$request->session()->flash('error_message','Set Failed');
				return redirect()->back()->withInput();
			}
		
		}
		
       return redirect('exception/'.$id.'/edit');
    }
    public function get(Request $request)
    {

        $orderby = 'date';
        $sort = 'desc';
        if(isset($_REQUEST['order'][0])){
            if($_REQUEST['order'][0]['column']==1) $orderby = 'sellerid';
            if($_REQUEST['order'][0]['column']==2) $orderby = 'amazon_order_id';
            if($_REQUEST['order'][0]['column']==3) $orderby = 'type';
            if($_REQUEST['order'][0]['column']==5) $orderby = 'date';
            if($_REQUEST['order'][0]['column']==6) $orderby = 'process_status';
            if($_REQUEST['order'][0]['column']==9) $orderby = 'user_id';
            $sort = $_REQUEST['order'][0]['dir'];
        }

        if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
            $updateDate=array();
            if(isset($_REQUEST['process_status']) && $_REQUEST['process_status']!='' && array_get($_REQUEST,"process_content")){
                $updateDate['process_status'] = $_REQUEST['process_status'];
				$updateDate['process_content'] = $_REQUEST['process_content'];
            }
            
            if(Auth::user()->admin){
                $updatebox = new Exception;
            }else{
                $updatebox = Exception::whereIn('group_id'  , array_get($this->getUserGroup(),'manage_groups',array()));
            }
            $updatebox->where('process_status','submit')->whereIN('id',$_REQUEST["id"])->update($updateDate);
            //$request->session()->flash('success_message','Group action successfully has been completed. Well done!');
            //$records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
           // $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
            unset($updateDate);
        }
        //if(Auth::user()->admin){
            $customers = new Exception;
        //}else{
//			$mgroup_ids  = array_get($this->getUserGroup(),'manage_groups',array());
//			$user_id  = Auth::user()->id;
//			$customers = Exception::where(function ($query) use ($mgroup_ids,$user_id) {
//
//                $query->whereIn('group_id'  , $mgroup_ids)
//						  ->orwhere('user_id', $user_id);
//
//            });
//        }
		if(array_get($_REQUEST,'type')){
            $customers = $customers->where('type', array_get($_REQUEST,'type'));
        }
        if(isset($_REQUEST['status']) && $_REQUEST['status']!=''){
            $customers = $customers->where('process_status', $_REQUEST['status']);
        }
        //if(Auth::user()->admin) {
		
			if (array_get($_REQUEST, 'group_id')) {
				
                $customers = $customers->where('group_id', array_get($_REQUEST, 'group_id'));

            }
            if (array_get($_REQUEST, 'user_id')) {
				$customers = $customers->where('user_id',  array_get($_REQUEST, 'user_id'));
            }
        //}
		
        if(array_get($_REQUEST,'sellerid')){
            $customers = $customers->where('sellerid',  array_get($_REQUEST, 'sellerid'));
			
        }
        if(array_get($_REQUEST,'amazon_order_id')){
            $customers = $customers->where('amazon_order_id', array_get($_REQUEST, 'amazon_order_id'));
        }
		

        if(array_get($_REQUEST,'order_sku')){
            $customers = $customers->where('order_sku', 'like', '%'.$_REQUEST['order_sku'].'%');
           
        }
		if(array_get($_REQUEST,'resellerid')){
            $customers = $customers->where('replacement', 'like', '%:"'.$_REQUEST['resellerid'].'"%');
        }
		if(array_get($_REQUEST,'resku')){
            $customers = $customers->where('replacement', 'like', '%:"'.$_REQUEST['resku'].'"%');
        }
		
        if(array_get($_REQUEST,'date_from')){
            $customers = $customers->where('date','>=',$_REQUEST['date_from'].' 00:00:00');
        }
        if(array_get($_REQUEST,'date_to')){
            $customers = $customers->where('date','<=',$_REQUEST['date_to'].' 23:59:59');
        }
		
		$iTotalRecords = $customers->count();
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
		$customersLists =  $customers->orderBy($orderby,$sort)->skip($iDisplayStart)->take($iDisplayLength)->get()->toArray();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $users = $this->getUsers();
		$groups = $this->getGroups();
		$groupleaders = $this->getGroupLeader();
		$accounts = $this->getSellerIds();
        $status_list['done'] = "<span class=\"label label-sm label-success\">Done</span>";
        $status_list['cancel'] = "<span class=\"label label-sm label-danger\">Cancelled</span>";
        $status_list['submit'] = "<span class=\"label label-sm label-warning\">Processing</span>";
		$type_list['1'] = "Refund";
        $type_list['2'] = "Replacement";
        $type_list['3'] = "Refund & Replacement";
		foreach ( $customersLists as $customersList){
			$operate = '';
			if($customersList['type']==1 || $customersList['type']==3) $operate.= 'Refund : '.$customersList['refund'].'</BR>';
			if($customersList['type']==2 || $customersList['type']==3){
				
				$replacements = unserialize($customersList['replacement']);
				$products = array_get($replacements,'products',array());
				if(is_array($products)){
					$operate.= 'Replace : </BR>';
					foreach( $products as $product){
						if(array_get($product,'seller_id')){
							$seller_id=array_get($product,'seller_id');
						}else{
							$seller_id = $customersList['sellerid'];
						}
						
						$operate.= '<span class="label label-sm label-primary">'.array_get($accounts,$seller_id,$seller_id).'</span></BR>'.(((array_get($accounts,$seller_id)?array_get($product,'seller_sku'):array_get($product,'item_code'))??array_get($product,'sku')??null)??array_get($product,'title')).'*'.array_get($product,'qty').'</BR>';
						
						
					}
				}
			}
            if($customersList['type']==4) $operate.= 'Gift Card : '.$customersList['gift_card_amount'].PHP_EOL;

            $records["data"][] = array(
                ((Auth::user()->admin || in_array($customersList['group_id'],array_get($this->getUserGroup(),'manage_groups',array()))) && $customersList['process_status']=='submit')?'<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$customersList['id'].'"/><span></span></label>':'',
                
				array_get($accounts,$customersList['sellerid']),
                $customersList['amazon_order_id'],
                array_get($type_list,$customersList['type']),
                $customersList['order_sku'],
				$customersList['date'],
                array_get($status_list,$customersList['process_status']),
				$operate,
				array_get($users,$customersList['process_user_id'])?array_get($users,$customersList['process_user_id']):array_get($groupleaders,$customersList['group_id']),
                array_get($groups,$customersList['group_id'].'.group_name').' > '.array_get($users,$customersList['user_id']),
                ((Auth::user()->admin || in_array($customersList['group_id'],array_get($this->getUserGroup(),'manage_groups',array()))) && $customersList['process_status']=='submit')?'<a href="/exception/'.$customersList['id'].'/edit" class="btn btn-sm red btn-outline " target="_blank"><i class="fa fa-search"></i> Process </a>':'<a href="/exception/'.$customersList['id'].'/edit" class="btn blue btn-sm btn-outline green" target="_blank"><i class="fa fa-search"></i> View </a>',
            );
		}


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }

    public function getUsers(){
        $users = User::get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['id']] = $user['name'];
        }
        return $users_array;
    }
	
	public function getGroups(){
        $users = Group::get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['id']]['group_name'] = $user['group_name'];
			$users_array[$user['id']]['user_ids'] = explode(",",$user['user_ids']);
        }
        return $users_array;
    }

    public function getAccounts(){
		
		$seller=[];
		$accounts= DB::connection('order')->table('accounts')->where('status',1)->groupBy(['sellername','sellerid'])->get(['sellername','sellerid']);
		$accounts=json_decode(json_encode($accounts), true);
		foreach($accounts as $account){
			$seller[$account['sellerid']]=$account['sellername'];
		}
		return $seller;
		
    }
	
	public function getSellerIds(){
        $accounts = Accounts::where('type','Amazon')->get()->toArray();
        $accounts_array = array();
        foreach($accounts as $account){
            $accounts_array[$account['account_sellerid']] = $account['account_name'];
        }
        return $accounts_array;
    }

	public function store(Request $request)
    {
        $this->validate($request, [
			'group_id' => 'required|string',
            'name' => 'required|string',
			'rebindordersellerid' => 'required|string',
			'rebindorderid' => 'required|string',
			'type' => 'required|string',
        ]);
        $exception = new Exception;
		
        $exception->type = $request->get('type');
		$exception->name = $request->get('name');
		$exception->order_sku = $request->get('order_sku');
		$exception->date = date('Y-m-d H:i:s');
		$exception->sellerid = $request->get('rebindordersellerid');
		$exception->amazon_order_id = $request->get('rebindorderid');
		$exception->group_id = $request->get('group_id');
		$exception->user_id = intval(Auth::user()->id);
		$exception->request_content = $request->get('request_content');
		$exception->process_status = 'submit';
		if( $exception->type == 1 || $exception->type == 3){
			$exception->refund = $request->get('refund');
		}else{
			$exception->refund = 0;
		}

        if( $exception->type == 4){
            $exception->gift_card_amount = $request->get('gift_card_amount')??0;
        }else{
            $exception->gift_card_amount = 0;
        }

		if( $exception->type == 2 || $exception->type == 3){
			$products=[];
			$products_arr = $request->get('group-products');
			$id_add=0;
			foreach($products_arr as $product_arr){
				$id_add++;
				if(array_get($product_arr,'seller_id')==$request->get('rebindordersellerid')){
					$product_arr['replacement_order_id']=$request->get('rebindorderid').'-0'.$id_add;
				}else{
					$product_arr['replacement_order_id']=substr($request->get('rebindorderid'),4).'-0'.$id_add;	
				}		
				$products[]=$product_arr;
			}
			
			$exception->replacement = serialize(
			array(
				'shipname'=>$request->get('shipname'),
				'address1'=>$request->get('address1'),
				'address2'=>$request->get('address2'),
				'address3'=>$request->get('address3'),
				'city'=>$request->get('city'),
				'county'=>$request->get('county'),
				'state'=>$request->get('state'),
				'district'=>$request->get('district'),
				'postalcode'=>$request->get('postalcode'),
				'countrycode'=>$request->get('countrycode'),
				'phone'=>$request->get('phone'),
				'products'=>$products,
			));
		}else{
			$exception->replacement = '';
		}

        if ($exception->save()) {
            return redirect('exception');
        } else {
            $request->session()->flash('error_message','Set Failed');
            return redirect()->back()->withInput();
        }
    }


	public function getGroupLeader(){
		$group_leaders=array();
		$leaders = Groupdetail::where('leader',1)->get(['group_id','user_id']);
		foreach($leaders as $leader){
			$group_leaders[$leader->group_id] = array_get($group_leaders,$leader->group_id).(array_get($group_leaders,$leader->group_id)?'; ':'').array_get($this->getUsers(),$leader->user_id);
		}
		
		return $group_leaders;
	
	}
	
	
	
	public function getUserGroup(){
	
		if(Auth::user()->admin){
            $groups = Groupdetail::get(['group_id']);
			$group_arr =array();
			foreach($groups as $group){
				$group_arr['groups'][$group->group_id] = $group->group_id;
			}
			$users = Groupdetail::get(['user_id']);
			foreach($users as $user){
				$group_arr['users'][$user->user_id] = $user->user_id;
			}
			return $group_arr;
        }else{
			$user_id = Auth::user()->id;
            $groups = Groupdetail::where('user_id',$user_id)->get(['group_id','leader']);
			$group_arr =array();
			foreach($groups as $group){
				$group_arr['groups'][$group->group_id] = $group->group_id;
				if($group->leader == 1)  $group_arr['manage_groups'][$group->group_id] = $group->group_id;
			}
			$users = Groupdetail::whereIn('group_id',array_get($group_arr,'manage_groups',array()))->get(['user_id']);
			foreach($users as $user){
				$group_arr['users'][$user->user_id] = $user->user_id;
			}
			$group_arr['users'][$user_id] = $user_id;
			return $group_arr;
			
        }
		
		
		
	}
	public function getRepeatOrder(Request $request){
		$orderid = $request->get('orderid');
		$id = intval($request->get('id'));
		$exists = Exception::where('exception.amazon_order_id',$orderid)->where('exception.id','<>',$id)->where('exception.process_status','<>','cancel')
		->leftJoin('users',function($q){
				$q->on('exception.user_id', '=', 'users.id');
			})->get(['users.name','exception.date','exception.process_status'])->toArray();
		die(json_encode($exists));
	}

	
	public function getrfcorder(Request $request){
		$orderid = $request->get('orderid');
		$sellerid = $request->get('sellerid');
		$order = array();
		$re = $message = $sku = $asin = '';
		if(!$orderid) $message='Incorrect Order ID';
		//if(!$sellerid) $message='Incorrect Seller ID';
		/*
		$inbox_email = DB::table('inbox')->where('id', $inboxid)->first();
		$account_email = $inbox_email->to_address;
		if(!$sellerid) $sellerid = $inbox_email->amazon_seller_id;
		if(!$sellerid) $sellerid = DB::table('accounts')->where('account_email', $account_email)->where('type','Amazon')->value('account_sellerid');
		*/
		if(!$message){
			$exists = DB::table('amazon_orders')->where('AmazonOrderId', $orderid);
			if($sellerid){
				$exists = $exists->where('SellerId', $sellerid);
			}
			$exists = $exists->first();
			if(!$exists){
				DB::beginTransaction();
				try{
					$appkey = 'site0001';
					$appsecret= 'testsite0001';
					$array['orderId']=$orderid;
					$array['appid']= $appkey;
					$array['method']='getOrder';
					ksort($array);
					$authstr = "";
					foreach ($array as $k => $v) {
						$authstr = $authstr.$k.$v;
					}
					$authstr=$authstr.$appsecret;
					$sign = strtoupper(sha1($authstr));
					
					$res = file_get_contents('http://116.6.105.153:18003/rfc_site.php?appid='.$appkey.'&sellerid='.$sellerid.'&method=getOrder&orderId='.$orderid.'&sign='.$sign);
					$result = json_decode($res,true);
					
					if(array_get($result,'result')){
						$data  = array_get($result,'data',array());
						$order = $orderItemData = array();
						$sellerid = $data['SELLERID'];
						$order= array(
							'SellerId'=>$data['SELLERID'],
							'MarketPlaceId'=>$data['ZMPLACEID'],
							'AmazonOrderId'=>$data['ZAOID'],
							'SellerOrderId'=>$data['ZSOID'],
							'ApiDownloadDate'=>date('Y-m-d H:i:s',strtotime($data['ALOADDATE'].$data['ALOADTIME'])),
							'PurchaseDate'=>date('Y-m-d H:i:s',strtotime($data['PCHASEDATE'].$data['PCHASETIME'])),
							'LastUpdateDate'=>date('Y-m-d H:i:s',strtotime($data['LUPDATEDATE'].$data['LUPDATETIME'])),
							'OrderStatus'=>$data['ORSTATUS'],
							'FulfillmentChannel'=>$data['FCHANNEL'],
							'SalesChannel'=>$data['SCHANNEL'],
							'OrderChannel'=>$data['OCHANNEL'],
							'ShipServiceLevel'=>$data['SHIPLEVEL'],
							'Name'=>$data['ZNAME'],
							'AddressLine1'=>$data['ADDR1'],
							'AddressLine2'=>$data['ADDR2'],
							'AddressLine3'=>$data['ADDR3'],
							'City'=>$data['ZCITY'],
							'County'=>$data['ZCOUNTRY'],
							'District'=>$data['ZDISTRICT'],
							'StateOrRegion'=>$data['ZSOREGION'],
							'PostalCode'=>$data['ZPOSCODE'],
							'CountryCode'=>$data['ZCOUNTRYCODE'],
							'Phone'=>$data['ZPHONE'],
							'Amount'=>$data['ZAMOUNT'],
							'CurrencyCode'=>$data['ZCURRCODE'],
							'NumberOfItemsShipped'=>$data['NISHIPPED'],
							'NumberOfItemsUnshipped'=>$data['NIUNSHIPPED'],
							'PaymentMethod'=>$data['PMETHOD'],
							'BuyerName'=>$data['BUYNAME'],
							'BuyerEmail'=>$data['BUYEMAIL'],
							'ShipServiceLevelCategory'=>$data['SSCATEGORY'],
							'EarliestShipDate'=>($data['ESDATE']>0)?date('Y-m-d H:i:s',strtotime($data['ESDATE'].$data['ESTIME'])):'',
							'LatestShipDate'=>($data['LSDATE']>0)?date('Y-m-d H:i:s',strtotime($data['LSDATE'].$data['LSTIME'])):'',
							'EarliestDeliveryDate'=>($data['EDDATE']>0)?date('Y-m-d H:i:s',strtotime($data['EDDATE'].$data['EDTIME'])):'',
							'LatestDeliveryDate'=>($data['LDDATE']>0)?date('Y-m-d H:i:s',strtotime($data['LDDATE'].$data['LDTIME'])):'',
						);
						foreach($data['O_ITEMS'] as $sdata){
							if(!$sku) $sku = $sdata['ZSSKU'];
							if(!$asin) $asin = $sdata['ZASIN'];
							$orderItemData[]= array(			
									'SellerId'=>$sdata['SELLERID'],
									'MarketPlaceId'=>$sdata['ZMPLACEID'],
									'AmazonOrderId'=>$sdata['ZAOID'],
									'OrderItemId'=>$sdata['ZORIID'],
									'Title'=>$sdata['TITLE'],
									'QuantityOrdered'=>intval($sdata['QORDERED']),
									'QuantityShipped'=>intval($sdata['QSHIPPED']),
									'GiftWrapLevel'=>$sdata['GWLEVEL'],
									'GiftMessageText'=>$sdata['GMTEXT'],
									'ItemPriceAmount'=>round($sdata['IPAMOUNT'],2),
									'ItemPriceCurrencyCode'=>$sdata['IPCCODE'],
									'ShippingPriceAmount'=>round($sdata['SPAMOUNT'],2),
									'ShippingPriceCurrencyCode'=>$sdata['SPCCODE'],
									'GiftWrapPriceAmount'=>round($sdata['GWPAMOUNT'],2),
									'GiftWrapPriceCurrencyCode'=>$sdata['GWPCCODE'],
									'ItemTaxAmount'=>round($sdata['ITAMOUNT'],2),
									'ItemTaxCurrencyCode'=>$sdata['ITCCODE'],
									'ShippingTaxAmount'=>round($sdata['STAMOUNT'],2),
									'ShippingTaxCurrencyCode'=>$sdata['STCCODE'],
									'GiftWrapTaxAmount'=>round($sdata['GWTAMOUNT'],2),
									'GiftWrapTaxCurrencyCode'=>$sdata['GWTCCODE'],
									'ShippingDiscountAmount'=>round($sdata['SDAMOUNT'],2),
									'ShippingDiscountCurrencyCode'=>$sdata['SDCCODE'],
									'PromotionDiscountAmount'=>round($sdata['PDAMOUNT'],2),
									'PromotionDiscountCurrencyCode'=>$sdata['PDCCODE'],
									'PromotionIds'=>$sdata['PROMOID'],
									'CODFeeAmount'=>round($sdata['CFAMOUNT'],2),
									'CODFeeCurrencyCode'=>$sdata['CFCCODE'],
									'CODFeeDiscountAmount'=>round($sdata['CFDAMOUNT'],2),
									'CODFeeDiscountCurrencyCode'=>$sdata['CFDCCODE'],
									'ASIN'=>$sdata['ZASIN'],
									'SellerSKU'=>$sdata['ZSSKU'],
							);
						}
						DB::table('amazon_orders_item')->insert($orderItemData);
						DB::table('amazon_orders')->insert($order);
						DB::commit();
					}else{
						$message = $result['message'];
					}
				} catch (\Exception $e) {
					DB::rollBack();
					$message = $e->getMessage();
				}
			}else{
				$sellerid =  $exists->SellerId;
				
				$exists_item = DB::table('amazon_orders_item')->where('AmazonOrderId', $orderid);
				if($sellerid){
					$exists_item = $exists_item->where('SellerId', $sellerid);
				}
				$exists_item = $exists_item->get();
				$order = json_decode(json_encode($exists),true);
				$orderItemData = json_decode(json_encode($exists_item),true);
			}
		}
		if(!$message){
			$order['orderItemData'] = $orderItemData;
			
			$re = $order;
			if($re){
				$message = 'Get Amazon Order ID Success';
			}else{
				$message = 'Get Amazon Order ID Failed';
			}
		}
		die(json_encode(array('result'=>$re , 'message'=>$message)));
	}

}
