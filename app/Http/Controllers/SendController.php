<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inbox;
use App\Sendbox;
use App\Accounts;
use Illuminate\Support\Facades\Session;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\MultipleQueue;
use PDO;
use DB;
use Illuminate\Http\Response;
class SendController extends Controller
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
        return view('send/index',['users'=>$this->getUsers()]);
    }

    public function create(Request $request)
    {
		$accounts = Accounts::get()->toArray();
        $accounts_array = $type_array =  array();
        foreach($accounts as $account){
            $accounts_array[$account['id']] = $account['account_email'];
			$type_array[$account['account_email']] = $account['type'];
        }
        return view('send/add',['accounts'=>$accounts_array,'request'=>$request,'accounts_type'=>$type_array]);
    }

    public function getAccounts(){
        $accounts = Accounts::get()->toArray();
        $accounts_array = array();
        foreach($accounts as $account){
            $accounts_array[$account['id']] = $account['account_email'];
        }
        return $accounts_array;
    }
	
	
	public function gAccounts(){
        $accounts = Accounts::get()->toArray();
        $accounts_array = array();
        foreach($accounts as $account){
            $accounts_array[strtolower($account['account_email'])] = $account['account_name'];
        }
        return $accounts_array;
    }

    public function deletefile($filename){
        try {
            $filename = base64_decode($filename);
            \File::delete(public_path().$filename);
            $success = new \stdClass();
            $success->{md5($filename)} = true;
            return \Response::json(array('files'=> array($success)), 200);
        } catch(\Exception $exception){
            // Return error
            return \Response::json($exception->getMessage(), 400);
        }
    }
	
	public function destroy(Request $request,$id)
    {
		$email = Sendbox::where('id',$id)->first();
        if(!Auth::user()->admin){
            $email->where('user_id',$this->getUserId());
        }
		
        $email = Sendbox::where('id',$id)->where('status','Draft');
		if(!Auth::user()->admin){
            $email = $email->where('user_id',$this->getUserId());
        }
		$result = $email->delete();
		if($result){
        $request->session()->flash('success_message','Delete Draft Success');
		}else{
		$request->session()->flash('error_message','Delete Draft Failed');
		}
        return redirect()->back()->withInput();
    }
	
    public function store(Request $request)
    {
        $file = $request->file('files');
        if($file) {
            try {
                $file_name = $file[0]->getClientOriginalName();
                $file_size = $file[0]->getSize();
                $file_ex = $file[0]->getClientOriginalExtension();
                $newname = $file_name ;
                $newpath = '/uploads/'.date('Ym').'/'.date('d').'/'.date('His').rand(100,999).intval(Auth::user()->id).'/';
                $file[0]->move(public_path().$newpath,$newname);
            } catch (\Exception $exception) {
                $error = array(
                    'name' => $file[0]->getClientOriginalName(),
                    'size' => $file[0]->getSize(),
                    'error' => $exception->getMessage(),
                );
                // Return error
                return \Response::json($error, 400);
            }

            // If it now has an id, it should have been successful.
            if (file_exists(public_path().$newpath.$newname)) {
                $newurl = $newpath . $newname;
                $success = new \stdClass();
                $success->name = $newname;
                $success->size = $file_size;
                $success->url = $newurl;
                $success->thumbnailUrl = $newurl;
                $success->deleteUrl = url('send/deletefile/' . base64_encode($newpath . $newname));
                $success->deleteType = 'get';
                $success->fileID = md5($newpath . $newname);
                return \Response::json(array('files' => array($success)), 200);
            } else {
                return \Response::json('Error', 400);
            }
            return \Response::json('Error', 400);
        }

        $this->validate($request, [
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'subject' => 'required|string',
            'content' => 'required|string',
            'user_id' => 'required|int',
        ]);
		if($request->get('fileid')){
			$up_attachs = $request->get('fileid');
			foreach( $up_attachs as $up_attach){
				if (!file_exists(public_path().$up_attach)){
					$request->session()->flash('error_message','Attachments does not exist, please re-upload!');
            		return redirect()->back()->withInput();
				}
			}
			$attachs = serialize($request->get('fileid'));
		}
		$to_address_array = explode(';',str_replace("；",";",$request->get('to_address')));

		foreach($to_address_array as $to_address){
			if(trim($to_address)){
				if($request->get('sendbox_id')){
					$sendbox = Sendbox::findOrFail($request->get('sendbox_id'));
				}else{
					$sendbox = new Sendbox;
				}
				$sendbox->user_id = intval(Auth::user()->id);
				$sendbox->from_address = trim($request->get('from_address'));
				$sendbox->to_address = substr(trim($to_address),0,99);
				$sendbox->subject = $request->get('subject');
				$sendbox->text_html = $request->get('content');
				$sendbox->date = date('Y-m-d H:i:s');
				if($request->get('plan_date')) $sendbox->plan_date = strtotime($request->get('plan_date'));
				$sendbox->status = $request->get('asDraft')?'Draft':'Waiting';
				$sendbox->inbox_id = $request->get('inbox_id')?intval($request->get('inbox_id')):0;
				$sendbox->warn = $request->get('warn')?intval($request->get('warn')):0;
				if($request->get('fileid')) $sendbox->attachs = $attachs;
				$sendbox->save();
			}
		}
        
        
        if ($sendbox->id) {
            $request->session()->flash('success_message','Save Email Success');
            if($request->get('inbox_id')){
				if(!$request->get('asDraft')){
                	Inbox::where('id',intval($request->get('inbox_id')))->update(['reply'=>2]);
				}
                return redirect('inbox/'.$request->get('inbox_id'));
            }else{
                return redirect('send/'.$sendbox->id);
            }
            //return redirect('inbox/'.$request->get('inbox_id'));
        } else {
            $request->session()->flash('error_message','Set Email Failed');
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {

        $email = Sendbox::where('id',$id)->first();
        if(!Auth::user()->admin){
            //$email->where('user_id',$this->getUserId());
        }
        $email->toArray();
		$email_from_history = Inbox::where('date','<',$email['date'])->where('from_address',$email['to_address'])->where('to_address',$email['from_address'])
        ->take(10)->orderBy('date','desc')->get()->toArray();
		
        $email_to_history = Sendbox::where('date','<',$email['date'])->where('from_address',$email['from_address'])->where('to_address',$email['to_address'])->take(10)->orderBy('date','desc')->get()->toArray();
		
        $email_history[strtotime($email['date'])] = $email;
		
		$account = Accounts::where('account_email',$email['from_address'])->first();
		$account_type = $account->type;
		
		$amazon_order_id='';
		$i=0;
		foreach($email_from_history as $mail){
			$i++;
			if($i==1){
				$amazon_order_id=$mail['amazon_order_id'];
				$amazon_seller_id=$mail['amazon_seller_id'];
				$email['mark']=$mail['mark'];
				$email['sku']=$mail['sku'];
				$email['asin']=$mail['asin'];
				$email['etype']=$mail['etype'];
				$email['remark']=$mail['remark'];
				$email['reply']=$mail['reply'];
				$email['from_name']=$mail['from_name'];
			}
            $key = strtotime($mail['date']);
            while(key_exists($key,$email_history)){
                $key--;
            }
            $email_history[$key] = $mail;
        }

        foreach($email_to_history as $mail){
            $key = strtotime($mail['date']);
            while(key_exists($key,$email_history)){
                $key--;
            }
            $email_history[$key] = $mail;
        }
        krsort($email_history);
		
		$order=array();
		if($amazon_order_id){
			if(!$amazon_seller_id) $amazon_seller_id = Accounts::where('account_email',$email['from_address'])->value('account_sellerid');
            $order = DB::table('amazon_orders')->where('SellerId', $amazon_seller_id)->where('AmazonOrderId', $amazon_order_id)->first();
            if($order) $order->item = DB::table('amazon_orders_item')->where('SellerId', $amazon_seller_id)->where('AmazonOrderId', $amazon_order_id)->get();
        }
		return view('send/view',['email_history'=>$email_history,'order'=>$order,'email'=>$email,'users'=>$this->getUsers(),'accounts'=>$this->gAccounts(),'account_type'=>$account_type]);
    }
    public function get()
    {
        /*
   * Paging
   */

        $orderby = 'date';
        $sort = 'desc';
        if(isset($_REQUEST['order'][0])){
            if($_REQUEST['order'][0]['column']==1) $orderby = 'from_address';
            if($_REQUEST['order'][0]['column']==2) $orderby = 'to_address';
            if($_REQUEST['order'][0]['column']==3) $orderby = 'subject';
            if($_REQUEST['order'][0]['column']==4) $orderby = 'date';
            $sort = $_REQUEST['order'][0]['dir'];
        }
        /*
        if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
            Inbox::where('user_id',$this->getUserId())->whereIN('id',$_REQUEST["id"])->update(['reply'=>$_REQUEST["customActionName"]]);

            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        */
        //if(Auth::user()->admin){
            $customers = new Sendbox;
        //}else{
        //    $customers = Sendbox::where('user_id',$this->getUserId());
        //}


        if(array_get($_REQUEST,'status')){
            $customers = $customers->where('status',$_REQUEST['status']);
        }
		
		if(array_get($_REQUEST,'user_id')){
            $customers = $customers->where('user_id',$_REQUEST['user_id']);
        }
        if(array_get($_REQUEST,'from_address')){
            $customers = $customers->where('from_address', 'like', '%'.$_REQUEST['from_address'].'%');
        }
        if(array_get($_REQUEST,'to_address')){
            $customers = $customers->where('to_address', 'like', '%'.$_REQUEST['to_address'].'%');
        }

        if(array_get($_REQUEST,'subject')){
            $customers = $customers->where('subject', 'like', '%'.$_REQUEST['subject'].'%');
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

        foreach ( $customersLists as $customersList){
            $records["data"][] = array(
                '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input name="id[]" type="checkbox" class="checkboxes" value="'.$customersList['id'].'"/><span></span></label>',
                $customersList['from_address'],
                $customersList['to_address'],
                '<a href="/send/'.$customersList['id'].'" style="color:#333;" target="_blank">'.$customersList['subject'].'</a>',
                $customersList['date'],
				array_get($users,$customersList['user_id']),
                $customersList['send_date']?'<span class="label label-sm label-success">'.$customersList['send_date'].'</span> ':'<span class="label label-sm label-danger">'.$customersList['status'].'</span> ',

				'<a href="/send/'.$customersList['id'].'" target="_blank">
                                        <button type="submit" class="btn btn-success btn-xs">View</button>
                                    </a>'.(($customersList['status']=='Draft')?'
                                    <form action="'.url('send/'.$customersList['id']).'" method="POST" style="display: inline;">
                                        '.method_field('DELETE').'
                                        '.csrf_field().'
                                        <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                    </form>':''),
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

}