<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Couponkunnr;
use Illuminate\Support\Facades\Session;
use App\Accounts;
use App\User;
use App\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\MultipleQueue;
use DB;
class CouponkunnrController extends Controller
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
        $autoprice = Couponkunnr::get()->toArray();
        $users_array = $this->getUsers();
        return view('couponkunnr/index',['users'=>$users_array,'accounts'=>$this->getAccounts()]);

    }
	
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
				$newpath = '/uploads/coupon/'.date('Ymd').'/';
				$inputFileName = public_path().$newpath.$newname;
  				$bool = $file->move(public_path().$newpath,$newname);

				if($bool){
					$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
					$importData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);		
					foreach($importData as $key => $data){
						if($key>1 && array_get($data,'A') && array_get($data,'B') && array_get($data,'C')  && array_get($data,'D')){
							Couponkunnr::updateOrCreate(
							[
								'kunnr' => trim(array_get($data,'A')),
								'coupon_description' => trim(array_get($data,'B'))
								],[
								'sku'=> trim(array_get($data,'C')),
								'sap_seller_id' => trim(array_get($data,'D'))
							]);	
						}
					}
					$request->session()->flash('success_message','Import Data Success!');
				}else{
					$request->session()->flash('error_message','Upload Customer Failed');
				}          
            } 
			}else{
				$request->session()->flash('error_message','Please Select Upload File');
			} 
        } 
		return redirect('couponkunnr');
	
	}
	
	
	public function get(Request $request)
    {
		$datas= new Couponkunnr;
               
        if($request->input('coupon_description')){
            $datas = $datas->where('coupon_description','like','%'.$request->input('coupon_description').'%');
        }
		if($request->input('sap_seller_id')){
            $datas = $datas->where('sap_seller_id', $request->input('sap_seller_id'));
        }
		if($request->input('sku')){
            $datas = $datas->where('sku','like','%'.$request->input('sku').'%');
        }
		if($request->input('kunnr')){
            $datas = $datas->where('kunnr', $request->input('kunnr'));
        }
		
		$iTotalRecords = $datas->count();
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
		$lists =  $datas->offset($iDisplayStart)->limit($iDisplayLength)->get()->toArray();
        $records = array();
        $records["data"] = array();

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
		foreach ( $lists as $list){
            $records["data"][] = array(
				$list['coupon_description'],
				$list['kunnr'],
                $list['sku'],
				$list['sap_seller_id'],
				'<a data-target="#ajax" data-toggle="modal" href="'.url('couponkunnr/'.$list['id'].'/edit').'" class="badge badge-success"> View </a>'
				
            );
		}
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        echo json_encode($records);
    }

    public function getUsers(){
        $users = User::Where('sap_seller_id','>',0)->orderBy('sap_seller_id','asc')->get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['sap_seller_id']] = $user['name'];
        }
        return $users_array;
    }

    public function getAccounts(){
        $seller=[];
		$accounts= DB::table('sap_kunnr')->orderBy('kunnr','asc')->get(['kunnr']);
		$accounts=json_decode(json_encode($accounts), true);
		foreach($accounts as $account){
			$seller[$account['kunnr']]=$account['kunnr'];
		}
		return $seller;
    }

    public function create()
    {
        return view('couponkunnr/add',['accounts'=>$this->getAccounts(),'users'=>$this->getUsers()]);
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            'kunnr' => 'required|string',
            'coupon_description' => 'required|string',
			'sku' => 'required|string',
			'sap_seller_id' => 'required|int'
        ]);

		if($this->checkAccount($request)){
            $request->session()->flash('error_message','Set Coupon Rule Failed, this Coupon rule has been exists.');
            return redirect()->back()->withInput();
            die();
        }

        $rule = new Couponkunnr;
        $rule->kunnr = $request->get('kunnr');
        $rule->coupon_description = $request->get('coupon_description');
        $rule->sku = $request->get('sku');
        $rule->sap_seller_id = intval($request->get('sap_seller_id'));
        if ($rule->save()) {
            $request->session()->flash('success_message','Set  Coupon rule Success');
            return redirect('couponkunnr');
        } else {
            $request->session()->flash('error_message','Set  Coupon rule Failed');
            return redirect()->back()->withInput();
        }
    }

    public function edit(Request $request,$id)
    {
        $rule= Couponkunnr::where('id',$id)->first()->toArray();
        if(!$rule){
            $request->session()->flash('error_message','Coupon rule not Exists');
            return redirect('couponkunnr');
        }

        return view('couponkunnr/edit',['rule'=>$rule,'users'=>$this->getUsers(),'accounts'=>$this->getAccounts()]);
    }

    public function update(Request $request,$id)
    {

        $this->validate($request, [
            'kunnr' => 'required|string',
            'coupon_description' => 'required|string',
			'sku' => 'required|string',
			'sap_seller_id' => 'required|string'
        ]);
		
		if($this->checkAccount($request)){
            $request->session()->flash('error_message','Set Coupon Rule Failed, this Coupon Rule has been.');
            return redirect()->back()->withInput();
            die();
        }
		
        $rule = Couponkunnr::findOrFail($id);
        $rule->kunnr = $request->get('kunnr');
        $rule->coupon_description = $request->get('coupon_description');
        $rule->sku = $request->get('sku');
        $rule->sap_seller_id = intval($request->get('sap_seller_id'));
        if ($rule->save()) {
            $request->session()->flash('success_message','Set Coupon Rule Success');
            return redirect('couponkunnr');
        } else {
            $request->session()->flash('error_message','Set Coupon Rule Failed');
            return redirect()->back()->withInput();
        }
    }
	
	public function checkAccount($request){
        $id = ($request->get('id'))?($request->get('id')):0;
        $seller_account = Couponkunnr::where('coupon_description',$request->get('coupon_description'))->where('kunnr',$request->get('kunnr'))->where('id','<>',$id)
            ->first();
        if($seller_account) return true;
        return false;
    }

}