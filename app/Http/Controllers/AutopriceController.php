<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutoPrice;
use Illuminate\Support\Facades\Session;
use App\Accounts;
use App\User;
use App\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\MultipleQueue;
use DB;
class AutopriceController extends Controller
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
        $autoprice = AutoPrice::get()->toArray();
        $users_array = $this->getUsers();
        return view('autoprice/index',['rules'=>$autoprice,'users'=>$users_array,'accounts'=>$this->getAccounts(),'actived'=>array(0=>'<span class="badge badge-default">Disabled</a>',1=>'<span class="badge badge-success">Enabled</span>')]);

    }

    public function getUsers(){
        $users = User::get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['id']] = $user['name'];
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

    public function create()
    {
        return view('autoprice/add',['accounts'=>$this->getAccounts()]);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'seller_id' => 'required|string',
            'seller_sku' => 'required|string',
			'marketplace_id' => 'required|string',
        ]);
		
		if($this->checkAccount($request)){
            $request->session()->flash('error_message','Set Auto Price Failed, this sellersku has been exists.');
            return redirect()->back()->withInput();
            die();
        }
		
        $rule = new AutoPrice;
        $rule->seller_id = $request->get('seller_id');
        $rule->seller_sku = $request->get('seller_sku');
        $rule->marketplace_id = $request->get('marketplace_id');
        $rule->actived = 1;
        $rule->user_id = intval(Auth::user()->id);
        if ($rule->save()) {
            $request->session()->flash('success_message','Set Auto Price Success');
            return redirect('autoprice');
        } else {
            $request->session()->flash('error_message','Set Auto Price Failed');
            return redirect()->back()->withInput();
        }
    }

    public function edit(Request $request,$id)
    {
        $rule= AutoPrice::where('id',$id)->first()->toArray();
        if(!$rule){
            $request->session()->flash('error_message','Auto Price not Exists');
            return redirect('autoprice');
        }
		$logs = DB::connection('order')->table('auto_price_log')->where('auto_price_id',$id)->orderBy('id','desc')->get();
        return view('autoprice/edit',['rule'=>$rule,'logs'=>$logs,'users'=>$this->getUsers(),'accounts'=>$this->getAccounts()]);
    }

    public function update(Request $request,$id)
    {

        $this->validate($request, [
            'actived' => 'required|int',
        ]);
		
		if($this->checkAccount($request)){
            $request->session()->flash('error_message','Set Auto Price Failed, this sellersku has been.');
            return redirect()->back()->withInput();
            die();
        }
		
        $rule = AutoPrice::findOrFail($id);
        $rule->actived = intval($request->get('actived'));
        $rule->user_id = intval(Auth::user()->id);
        if ($rule->save()) {
            $request->session()->flash('success_message','Set Auto Price Success');
            return redirect('autoprice');
        } else {
            $request->session()->flash('error_message','Set Auto Price Failed');
            return redirect()->back()->withInput();
        }
    }
	
	public function checkAccount($request){
        $id = ($request->get('id'))?($request->get('id')):0;

        $seller_account = AutoPrice::where('seller_id',$request->get('seller_id'))->where('marketplace_id',$request->get('marketplace_id'))->where('seller_sku',$request->get('seller_sku'))->where('id','<>',$id)
            ->first();
        if($seller_account) return true;
        return false;
    }

}