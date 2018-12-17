<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Asin;
use App\User;
use App\Review;
use App\Accounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\MultipleQueue;
use PDO;
use DB;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ReviewController extends Controller
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
	 
	public function upload( Request $request )
	{	
		if($request->isMethod('POST')){  
            $file = $request->file('importFile');  
  			if($file){
            if($file->isValid()){  
  
                $originalName = $file->getClientOriginalName();  
                $ext = $file->getClientOriginalExtension();  
                $type = $file->getClientMimeType();  
                $realPath = $file->getRealPath();  
                $newname = date('Y-m-d-H-i-S').'-'.uniqid().'.'.$ext;  
				$newpath = '/uploads/reviewUpload/'.date('Ymd').'/';
				$inputFileName = public_path().$newpath.$newname;
  				$bool = $file->move(public_path().$newpath,$newname);

				if($bool){
					$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
					$importData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);		
					$successCount = $addCount = $errorCount = 0;
					foreach($importData as $key => $data){
						if($key==1){
							if(array_get($data,'A')!='site' || array_get($data,'B')!='customer id' || array_get($data,'C')!='review id'){
								die('Customer profile import template error');
							}
						} 
						if($key>1 && array_get($data,'A') && array_get($data,'B') && array_get($data,'C')){
							
							if(array_get($data,'B')!='not_match'){
								$exists = DB::table('customers')->where('customer_id',trim($data['B']))->where('site',trim($data['A']))->first();
								if($exists){
									$update_result = DB::table('customers')->where('site',trim($data['A']))->where('customer_id',trim($data['B']))->update(array(
										'email'=>trim($data['D']),
										'phone'=>trim($data['E']),
										'other'=>trim($data['F']),
										'last_update_date'=>date('Y-m-d')
									));
									if ($update_result) {
										$successCount++;
									}
								}else{
									$insert_result = DB::table('customers')->insert(
										array(
											'site'=>trim($data['A']),
											'customer_id'=>trim($data['B']),
											'email'=>trim($data['D']),
											'phone'=>trim($data['E']),
											'other'=>trim($data['F']),
											'last_update_date'=>date('Y-m-d')
										)
									);
									if ($insert_result) {
										$addCount++;
									} else {
										$errorCount++;
									}
								}
								DB::table('review')->where('site',trim($data['A']))->where('customer_id',trim($data['B']))->where('status',102)->update(['status'=>103]);
							
							}else{
								$exists = DB::table('review_customers')->where('review',trim($data['C']))->where('site',trim($data['A']))->first();
								if($exists){
									$update_result = DB::table('review_customers')->where('site',trim($data['A']))->where('review',trim($data['C']))->update(array(
										'email'=>trim($data['D']),
										'phone'=>trim($data['E']),
										'other'=>trim($data['F']),
										'last_update_date'=>date('Y-m-d')
									));
									if ($update_result) {
										$successCount++;
									}
								}else{
									$insert_result = DB::table('review_customers')->insert(
										array(
											'site'=>trim($data['A']),
											'review'=>trim($data['C']),
											'email'=>trim($data['D']),
											'phone'=>trim($data['E']),
											'other'=>trim($data['F']),
											'last_update_date'=>date('Y-m-d')
										)
									);
									if ($insert_result) {
										$addCount++;
									} else {
										$errorCount++;
									}
								}
								DB::table('review')->where('site',trim($data['A']))->where('review',trim($data['C']))->where('status',102)->update(['status'=>103]);
							
							}
							
						}
					}
					$request->session()->flash('success_message','Import Customer Data Success! '.$successCount.' covered  '.$addCount.' added  '.$errorCount.'  Errors');
				}else{
					$request->session()->flash('error_message','Upload Customer Failed');
				}          
            } 
			}else{
				$request->session()->flash('error_message','Please Select Upload File');
			} 
        } 
		return redirect('review');
	
	}
	
	
	public function export(Request $request){
		set_time_limit(0);
		$date_from=date('Y-m-d',strtotime('-90 days'));		
		$date_to=date('Y-m-d');	
		
		$customers = DB::table('review')
			->select('review.*','asin.status as asin_status','customers.email as email','customers.phone as phone','customers.other as other','review_customers.email as email_add','review_customers.phone as phone_add','review_customers.other as other_add','star.average_score as average_score','star.total_star_number as total_star_number')
			->leftJoin(DB::raw('(select max(status) as status,asin,site,max(bg) as bg,max(bu) as bu from asin group by asin,site) as asin'),function($q){
				$q->on('review.asin', '=', 'asin.asin')
					->on('review.site', '=', 'asin.site');
			})->leftJoin('star',function($q){
				$q->on('review.asin', '=', 'star.asin')
					->on('review.site', '=', 'star.domain');
			})->leftJoin('customers',function($q){
				$q->on('review.customer_id', '=', 'customers.customer_id')
					->on('review.site', '=', 'customers.site');
			})->leftJoin('review_customers',function($q){
				$q->on('review.review', '=', 'review_customers.review')
					->on('review.site', '=', 'review_customers.site');
			});
		if(!Auth::user()->admin){
            $customers = $customers->where('review.user_id',$this->getUserId());
        }
		
		if(array_get($_REQUEST,'bgbu')){
			   $bgbu = array_get($_REQUEST,'bgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(array_get($bgbu_arr,0)) $customers = $customers->where('asin.bg',array_get($bgbu_arr,0));
			   if(array_get($bgbu_arr,1)) $customers = $customers->where('asin.bu',array_get($bgbu_arr,1));
		}

        if(array_get($_REQUEST,'vp')){
			   $customers = $customers->where('review.vp',array_get($_REQUEST,'vp')-1);
		}
		
		if(array_get($_REQUEST,'del')){
			   $customers = $customers->where('review.is_delete',array_get($_REQUEST,'del')-1);
		}
		 
		if(array_get($_REQUEST,'rc')){
			   if(array_get($_REQUEST,'rc')==1) $customers = $customers->whereRaw('review.rating=review.updated_rating');
			    if(array_get($_REQUEST,'rc')==2) $customers = $customers->whereRaw('review.rating<>review.updated_rating');
		}
		
		if(array_get($_REQUEST,'asin_status')){
            $customers = $customers->whereIn('asin.status',explode(',',array_get($_REQUEST,'asin_status')));
        }
		if(Auth::user()->admin){
			if(array_get($_REQUEST,'user_id')){
				$customers = $customers->whereIn('review.user_id',explode(',',array_get($_REQUEST,'user_id')));
			}
		}
		
		
		if(array_get($_REQUEST,'date_from')) $date_from= array_get($_REQUEST,'date_from');
		if(array_get($_REQUEST,'date_to')) $date_to= array_get($_REQUEST,'date_to');
		$customers = $customers->where('date','>=',$date_from)->where('rating','<',4);;
		$customers = $customers->where('date','<=',$date_to);
		
		
		if(array_get($_REQUEST,'follow_status')){
            $customers = $customers->whereIn('review.status',explode(',',array_get($_REQUEST,'follow_status')));
        }
		
		if(array_get($_REQUEST,'site')){
            $customers = $customers->whereIn('review.site',explode(',',array_get($_REQUEST,'site')));
        }
		
		if(array_get($_REQUEST,'rating')){
            $customers = $customers->where('review.rating',$_REQUEST['rating']);
        }

		if(array_get($_REQUEST,'keywords')){
            //$customers = $customers->where('subject', 'like', '%'.$_REQUEST['subject'].'%');
            $keywords = array_get($_REQUEST,'keywords');
            $customers = $customers->where(function ($query) use ($keywords) {

                $query->where('reviewer_name', 'like', '%'.$keywords.'%')
						 ->orwhere('review.asin', 'like', '%'.$keywords.'%')
						  ->orwhere('review.review', 'like', '%'.$keywords.'%')
						  ->orwhere('amazon_order_id', 'like', '%'.$keywords.'%')
						   ->orwhere('buyer_email', 'like', '%'.$keywords.'%')
						   ->orwhere('review.customer_id', 'like', '%'.$keywords.'%')
						  ->orwhere('etype', 'like', '%'.$keywords.'%');

            });
        }

		
		
		$orderby = 'date';
        $sort = 'desc';
		
		
		
		
        $reviews =  $customers->orderBy($orderby,$sort)->get();
		
		$reviewsLists =json_decode(json_encode($reviews), true);
		$arrayData = array();
		$headArray[] = 'Review Date';
		$headArray[] = 'Asin';
		$headArray[] = 'Review';
		$headArray[] = 'ReviewCount';
		$headArray[] = 'Customer ID';
		$headArray[] = 'Site';
		$headArray[] = 'ReviewID';
		$headArray[] = 'Reviewer Name';
		$headArray[] = 'Rating';
		$headArray[] = 'Review Content';
		$headArray[] = 'Buyer Email';
		$headArray[] = 'Amazon OrderId';
		$headArray[] = 'Review Status';
		$headArray[] = 'Follow Content';
		$headArray[] = 'Question Type';
		$headArray[] = 'Follow up Date';
		$headArray[] = 'Customer Feedback';
		$headArray[] = 'Asin Status';
		$headArray[] = 'User';
		$headArray[] = 'SellerID';
		$headArray[] = 'Customer Email';
		$headArray[] = 'Customer Phone';
		$headArray[] = 'Customer Other';
		$headArray[] = 'Review Email';
		$headArray[] = 'Review Phone';
		$headArray[] = 'Review Other';

		$arrayData[] = $headArray;
		$users_array = $this->getUsers();
		$asin_status_array =  getAsinStatus();
		$follow_status_array = getReviewStatus();
		$steps = DB::table('review_step')->get();
		foreach($steps as $step){
			$follow_status_array[$step->id]=$step->title;
		}
		foreach ( $reviewsLists as $review){
			$fol_arr= unserialize($review['follow_content'])?unserialize($review['follow_content']):array();
            $arrayData[] = array(
               	$review['date'],
				$review['asin'],
				round($review['average_score'],1),
				$review['total_star_number'],
				$review['customer_id'],
				$review['site'],
				$review['review'],
				$review['reviewer_name'],
				$review['rating'],
				strip_tags($review['review_content']),
				$review['buyer_email'],
				$review['amazon_order_id'],
				array_get($follow_status_array,empty(array_get($review,'status'))?0:array_get($review,'status'),''),
				strip_tags(array_get($fol_arr,array_get($review,'status').'.do_content')),
				$review['etype'],
				$review['edate'],
				array_get(getCustomerFb(),$review['customer_feedback']),
				array_get($asin_status_array,empty(array_get($review,'asin_status'))?0:array_get($review,'asin_status')),				
				array_get($users_array,intval(array_get($review,'user_id')),''),
				$review['seller_id'],
				$review['email'],
				$review['phone'],
				$review['other'],
				$review['email_add'],
				$review['phone_add'],
				$review['other_add']
				
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
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//¸æËßä¯ÀÀÆ÷Êä³ö07ExcelÎÄ¼þ
			header('Content-Disposition: attachment;filename="Export_review.xlsx"');//¸æËßä¯ÀÀÆ÷Êä³öä¯ÀÀÆ÷Ãû³Æ
			header('Cache-Control: max-age=0');//½ûÖ¹»º´æ
			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');
		}
		die();
	}
	
	
    public function index()
    {   
	

		$date_from=date('Y-m-d',strtotime('-90 days'));		
		$date_to=date('Y-m-d');	
		
		
		$asin_status_array = getAsinStatus();
		$follow_status_array = getReviewStatus();
		$steps = DB::table('review_step')->get();
		foreach($steps as $step){
			$follow_status_array[$step->id]=$step->title;
		}
		$teams= DB::select('select bg,bu from asin group by bg,bu ORDER BY BG ASC,BU ASC');
        return view('review/index',['date_from'=>$date_from ,'date_to'=>$date_to, 'asin_status'=>$asin_status_array,'follow_status'=>$follow_status_array, 'users'=>$this->getUsers(),'teams'=>$teams]);

    }

    public function get()
    {
		$date_from=date('Y-m-d',strtotime('-90 days'));		
		$date_to=date('Y-m-d');	
		if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
            $updateDate=array();
			
			if(array_get($_REQUEST,"giveReviewUser")){
                $updateDate['user_id'] = array_get($_REQUEST,"giveReviewUser");
            }
			$updatebox = new Review;
			
			if($updateDate) $updatebox->whereIN('id',$_REQUEST["id"])->update($updateDate);
            unset($updateDate);
			foreach($_REQUEST["id"] as $up_id){
				DB::table('review_change_log')->insert(array(
					'review_id'=>$up_id,
					'to_user_id'=>array_get($_REQUEST,"giveReviewUser"),
					'user_id'=>Auth::user()->id,
					'date'=>date('Y-m-d H:i:s')
				));
			}
        }
		
		$customers = DB::table('review')
			->select('review.*','asin.status as asin_status','asin.item_no as item_no','customers.email as email','review_customers.email as email_add','star.average_score as average_score','star.total_star_number as total_star_number')
			->leftJoin(DB::raw('(select max(status) as status,asin,site,max(bg) as bg,max(bu) as bu,max(item_no) as item_no from asin group by asin,site) as asin'),function($q){
				$q->on('review.asin', '=', 'asin.asin')
					->on('review.site', '=', 'asin.site');
			})->leftJoin('star',function($q){
				$q->on('review.asin', '=', 'star.asin')
					->on('review.site', '=', 'star.domain');
			})->leftJoin('customers',function($q){
				$q->on('review.customer_id', '=', 'customers.customer_id')
					->on('review.site', '=', 'customers.site');
			})->leftJoin('review_customers',function($q){
				$q->on('review.review', '=', 'review_customers.review')
					->on('review.site', '=', 'review_customers.site');
			});
		
		if(!Auth::user()->admin){
            $customers = $customers->where('review.user_id',$this->getUserId());
        }
		
		if(array_get($_REQUEST,'bgbu')){
			   $bgbu = array_get($_REQUEST,'bgbu');
			   $bgbu_arr = explode('_',$bgbu);
			   if(array_get($bgbu_arr,0)) $customers = $customers->where('asin.bg',array_get($bgbu_arr,0));
			   if(array_get($bgbu_arr,1)) $customers = $customers->where('asin.bu',array_get($bgbu_arr,1));
		}

        if(array_get($_REQUEST,'vp')){
			   $customers = $customers->where('review.vp',array_get($_REQUEST,'vp')-1);
		}
		
		if(array_get($_REQUEST,'del')){
			   $customers = $customers->where('review.is_delete',array_get($_REQUEST,'del')-1);
		}
		
		if(array_get($_REQUEST,'rc')){
			   if(array_get($_REQUEST,'rc')==1) $customers = $customers->whereRaw('review.rating=review.updated_rating');
			    if(array_get($_REQUEST,'rc')==2) $customers = $customers->whereRaw('review.rating<>review.updated_rating');
		}
		
		if(array_get($_REQUEST,'asin_status')){
            $customers = $customers->whereIn('asin.status',array_get($_REQUEST,'asin_status'));
        }
		if(Auth::user()->admin){
			if(array_get($_REQUEST,'user_id')){
				$customers = $customers->whereIn('review.user_id',array_get($_REQUEST,'user_id'));
			}
		}
		
		
		if(array_get($_REQUEST,'date_from')) $date_from= array_get($_REQUEST,'date_from');
		if(array_get($_REQUEST,'date_to')) $date_to= array_get($_REQUEST,'date_to');
		$customers = $customers->where('date','>=',$date_from);
		$customers = $customers->where('date','<=',$date_to);
		$nev_rating=4;
		$customers = $customers->where(function ($query) use ($nev_rating) {
			$query->where('rating', '<', $nev_rating)->orWhere('updated_rating', '<', $nev_rating);
		});
		if(array_get($_REQUEST,'nextdate')) $customers = $customers->where('nextdate',array_get($_REQUEST,'nextdate'));
		
		if(array_get($_REQUEST,'follow_status')){
            $customers = $customers->whereIn('review.status',array_get($_REQUEST,'follow_status'));
        }
		
		if(array_get($_REQUEST,'site')){
            $customers = $customers->whereIn('review.site',array_get($_REQUEST,'site'));
        }
		
		if(array_get($_REQUEST,'rating')){
            $customers = $customers->where('review.rating',$_REQUEST['rating']);
        }

		if(array_get($_REQUEST,'keywords')){
            //$customers = $customers->where('subject', 'like', '%'.$_REQUEST['subject'].'%');
            $keywords = array_get($_REQUEST,'keywords');
            $customers = $customers->where(function ($query) use ($keywords) {

                $query->where('reviewer_name', 'like', '%'.$keywords.'%')
						 ->orwhere('review.asin', 'like', '%'.$keywords.'%')
						  ->orwhere('review.review', 'like', '%'.$keywords.'%')
						  ->orwhere('amazon_order_id', 'like', '%'.$keywords.'%')
						   ->orwhere('buyer_email', 'like', '%'.$keywords.'%')
						   ->orwhere('review.customer_id', 'like', '%'.$keywords.'%')
						  ->orwhere('etype', 'like', '%'.$keywords.'%');

            });
        }

		
		
		$orderby = 'date';
        $sort = 'desc';
        if(isset($_REQUEST['order'][0])){
			 if($_REQUEST['order'][0]['column']==1) $orderby = 'negative_value';
            if($_REQUEST['order'][0]['column']==2) $orderby = 'asin';
			if($_REQUEST['order'][0]['column']==3) $orderby = 'date';
            if($_REQUEST['order'][0]['column']==4) $orderby = 'rating';
			if($_REQUEST['order'][0]['column']==5) $orderby = 'average_score';
			if($_REQUEST['order'][0]['column']==6) $orderby = 'total_star_number';
            if($_REQUEST['order'][0]['column']==7) $orderby = 'reviewer_name';
			if($_REQUEST['order'][0]['column']==8) $orderby = 'vp';
            if($_REQUEST['order'][0]['column']==9) $orderby = 'status';
            if($_REQUEST['order'][0]['column']==10) $orderby = 'buyer_email';
			if($_REQUEST['order'][0]['column']==11) $orderby = 'customer_feedback';
            if($_REQUEST['order'][0]['column']==12) $orderby = 'nextdate';
			if($_REQUEST['order'][0]['column']==15) $orderby = 'user_id';

            $sort = $_REQUEST['order'][0]['dir'];
			
			
        }
		
		
		
		
        $reviews =  $customers->orderBy($orderby,$sort)->get()->toArray();
		$ordersList =json_decode(json_encode($reviews), true);
		
	
        $iTotalRecords = count($ordersList);
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
		

		
		$users_array = $this->getUsers();
		$asin_status_array =  getAsinStatus();
		$follow_status_array = getReviewStatus();
		$steps = DB::table('review_step')->get();
		foreach($steps as $step){
			$follow_status_array[$step->id]=$step->title;
		}
		
        for($i = $iDisplayStart; $i < $end; $i++) {
			$rating_chstr = '';
			if($ordersList[$i]['updated_rating']>0 && $ordersList[$i]['updated_rating']!=$ordersList[$i]['rating']){
				if($ordersList[$i]['updated_rating']>$ordersList[$i]['rating']){
					$rating_chstr = '<span class="badge badge-danger"><i class="fa fa-angle-double-up"></i> '.$ordersList[$i]['updated_rating'].'</span>';
				}else{
					$rating_chstr = '<span class="badge badge-success"><i class="fa fa-angle-double-down"></i>'.$ordersList[$i]['updated_rating'].'</span>';
				}
				
				
			}
			$fol_arr= unserialize($ordersList[$i]['follow_content'])?unserialize($ordersList[$i]['follow_content']):array();
			$records["data"][] = array(
				 '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$ordersList[$i]['id'].'"/><span></span></label>',
				$ordersList[$i]['negative_value'],
				'<a href="https://'.$ordersList[$i]['site'].'/dp/'.$ordersList[$i]['asin'].'" target="_blank">'.$ordersList[$i]['asin'].'</a> <span class="label label-sm label-default">'.strtoupper(substr(strrchr($ordersList[$i]['site'], '.'), 1)).'</span>',
				$ordersList[$i]['date'],
				$ordersList[$i]['rating'].' '.$rating_chstr,
				$ordersList[$i]['average_score'],
				$ordersList[$i]['total_star_number'],
				$ordersList[$i]['reviewer_name'],
				($ordersList[$i]['vp'])?'<span class="badge badge-danger">VP</span>':'',
				array_get($follow_status_array,$ordersList[$i]['status'],'').' '.(($ordersList[$i]['is_delete'])?'<span class="badge badge-danger">Del</span>':''),
				(($ordersList[$i]['email'])?'<span class="badge badge-danger">C</span>':' ').(($ordersList[$i]['email_add'])?'<span class="badge badge-danger">R</span>':' ').$ordersList[$i]['buyer_email'],
				array_get(getCustomerFb(),$ordersList[$i]['customer_feedback']),
				$ordersList[$i]['nextdate'],
				$ordersList[$i]['item_no'],
				strip_tags(array_get($fol_arr,$ordersList[$i]['status'].'.do_content')),
				array_get($users_array,intval(array_get($ordersList[$i],'user_id')),''),				
				(($ordersList[$i]['warn']>0)?'<i class="fa fa-warning" title="Contains dangerous words"></i>&nbsp;&nbsp;&nbsp;':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').'<a href="https://'.$ordersList[$i]['site'].'/gp/customer-reviews/'.$ordersList[$i]['review'].'" target="_blank" class="btn btn-success btn-xs"> View </a>'.'<a href="/review/'.$ordersList[$i]['id'].'/edit" target="_blank" class="btn btn-danger btn-xs"><i class="fa fa-search"></i> Resolve </a>'
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
	
	public function edit(Request $request,$id)
    {
        //if(!Auth::user()->admin) die();
        $review = Review::where('id',$id)->first()->toArray();
		if(!$review){
            $request->session()->flash('error_message','Review not Exists');
            return redirect('review');
        }
		$order = array();
		if(array_get($review,'amazon_order_id') && array_get($review,'seller_id')){
            $order = DB::table('amazon_orders')->where('SellerId', array_get($review,'seller_id'))->where('AmazonOrderId', array_get($review,'amazon_order_id'))->first();
            if($order) $order->item = DB::table('amazon_orders_item')->where('SellerId', array_get($review,'seller_id'))->where('AmazonOrderId', array_get($review,'amazon_order_id'))->get();
        }
		
		$customer=[];
		if(array_get($review,'customer_id') && array_get($review,'site')){
            $customer_obj = DB::table('customers')->where('site', array_get($review,'site'))->where('customer_id', array_get($review,'customer_id'))->first();
			$customer = json_decode(json_encode($customer_obj), true);
        }
		$review_info=[];
		if(array_get($review,'review') && array_get($review,'site')){
            $review_info_obj = DB::table('review_customers')->where('site', array_get($review,'site'))->where('review', array_get($review,'review'))->first();
			$review_info = json_decode(json_encode($review_info_obj), true);
        }
		$return['customer'] = $customer;
		$return['review_info'] = $review_info;
		$return['users'] = $this->getUsers();
		$return['steps'] = DB::table('review_step')->get();
		$return['review'] = $review;
		$return['sellerids'] = $this->getSellerIds();
		$return['accounts'] = $this->getAccounts();
		$return['emails'] = DB::table('sendbox')->where('to_address', array_get($review,'buyer_email'))->orderBy('date','desc')->get();
		$return['emails'] =json_decode(json_encode($return['emails']), true);
		if($order) $return['order'] = $order;
        return view('review/edit',$return);

    }

    public function update(Request $request,$id)
    {
        //if(!Auth::user()->admin) die();
		
		if($request->get('status')==6 && !($request->get('creson'))){
            $request->session()->flash('error_message','Set Review Failed, Set Review Closed Must Fill Closed Reson !.');
            return redirect()->back()->withInput();
            die();
        }
		
        $seller_account = Review::findOrFail($id);;
		
		$seller_account->seller_id = $request->get('rebindordersellerid');
		$seller_account->amazon_order_id = $request->get('rebindorderid');
		$seller_account->buyer_email = $request->get('buyer_email');
		$seller_account->buyer_phone = $request->get('buyer_phone');
		$seller_account->etype = $request->get('etype');
		$seller_account->edate = date('Y-m-d');
		$seller_account->nextdate = $request->get('nextdate');
		$seller_account->customer_feedback = $request->get('customer_feedback');
		$do_ids = $request->get('do_id');
		
		if($do_ids){
			asort($do_ids);
			$do_log = [];
			foreach($do_ids as $k=>$do_id){
				if($do_id=='X'){
					$seller_account->status = $request->get('status');
					if($request->get('status')==6) $seller_account->creson = $request->get('creson');
					$do_log[$do_id]['do_date']=$request->get('do_date_'.$do_id);
					$do_log[$do_id]['do_content']=$request->get('status');
				}else{
					$do_log[$do_id]['do_date']=$request->get('do_date_'.$do_id);
					$do_log[$do_id]['do_content']=$request->get('valuelink_follow_content_'.$do_id);
					$seller_account->status = $do_id;
				}
			}
			$seller_account->follow_content = serialize($do_log);
		}
				
		if(Auth::user()->admin && $request->get('user_id')){
			if($seller_account->user_id!=$request->get('user_id')){
				DB::table('review_change_log')->insert(array(
					'review_id'=>$id,
					'to_user_id'=>$request->get('user_id'),
					'user_id'=>Auth::user()->id,
					'date'=>date('Y-m-d H:i:s')
				));
			}
			$seller_account->user_id = $request->get('user_id');
		}
        if ($seller_account->save()) {
            $request->session()->flash('success_message','Set Review Success');
            return redirect('review/'.$id.'/edit');
        } else {
            $request->session()->flash('error_message','Set Review Failed');
            return redirect()->back()->withInput();
        }
    }
	
	function getExtension($inputFileName)
    {
        $ext = substr(strrchr($inputFileName, '.'), 1);
		echo $ext;
        if (!$ext) {
            return null;
        }

        switch (strtolower($ext)) {
            case 'xlsx': // Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm': // Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx': // Excel (OfficeOpenXML) Template
            case 'xltm': // Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                return 'Xlsx';
            case 'xls': // Excel (BIFF) Spreadsheet
            case 'xlt': // Excel (BIFF) Template
                return 'Xls';
            case 'ods': // Open/Libre Offic Calc
            case 'ots': // Open/Libre Offic Calc Template
                return 'Ods';
            case 'slk':
                return 'Slk';
            case 'xml': // Excel 2003 SpreadSheetML
                return 'Xml';
            case 'gnumeric':
                return 'Gnumeric';
            case 'htm':
            case 'html':
                return 'Html';
            case 'csv':
                // Do nothing
                // We must not try to use CSV reader since it loads
                // all files including Excel files etc.
                return 'Csv';
            default:
                return null;
        }
    }
	
	public function getAccounts(){
        $accounts = Accounts::get()->toArray();
        $accounts_array = array();
        foreach($accounts as $account){
            $accounts_array[strtolower($account['account_email'])] = $account['account_name'];
        }
        return $accounts_array;
    }
	
	public function getSellerIds(){
        $accounts = Accounts::where('type','Amazon')->get()->toArray();
        $accounts_array = array();
        foreach($accounts as $account){
            $accounts_array[$account['account_sellerid']] = $account['account_name'];
        }
        return $accounts_array;
    }

}