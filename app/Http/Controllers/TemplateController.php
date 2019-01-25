<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Templates;
use Illuminate\Support\Facades\Session;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\MultipleQueue;
use DB;
class TemplateController extends Controller
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
		//if(Auth::user()->admin){
			$templates = new Templates;
		//}else{
		//	$templates = Templates::where('user_id',$this->getUserId());
		//}
        $templates = $templates->get()->toArray();
        return view('template/index',['templates'=>$templates,'users'=>$this->getUsers()]);

    }
	
	
	public function get(Request $request)
    {	
		$keywords = $request->get('term');
		$keywords_array = explode(';',$keywords);
		$where = ' where 1=1 ';
		foreach($keywords_array as $keyword){
			$where.= " and tag like '%".$keyword."%' ";
		}	
        $templates = DB::select('select id,tag,title,content,user_id from templates '.$where.' order by case when user_id= '.Auth::user()->id.' then 0 else user_id end ');
		$result = array();
		$users= $this->getUsers();
        foreach($templates as $template){
			$option = array();
			$option['id'] = $template->id;
			$option['label'] = $template->tag.' By '.array_get($users,$template->user_id,$template->user_id);
			$option['value'] = $template->tag;
			$option['title'] = $template->title;
			$option['desc'] = $template->content;
			$result[]=$option;
		}
		return response(json_encode($result));

    }
	
	 public function getUsers(){
        $users = User::get()->toArray();
        $users_array = array();
        foreach($users as $user){
            $users_array[$user['id']] = $user['name'];
        }
        return $users_array;
    }

    public function create()
    {
        return view('template/add');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'tag' => 'required|string',
			'title' => 'required|string',
            'content' => 'required|string',
        ]);
        $seller_account = new Templates;
        $seller_account->tag = $request->get('tag');
        $seller_account->title = $request->get('title');
        $seller_account->content = $request->get('content');
		$seller_account->user_id = Auth::user()->id;
   
        if($request->get('id')>0){
            $seller_account->id = $request->get('id');
        }
        if ($seller_account->save()) {
            $request->session()->flash('success_message','Set Templates Success');
            return redirect('template');
        } else {
            $request->session()->flash('error_message','Set TemplatesFailed');
            return redirect()->back()->withInput();
        }
    }


    public function destroy(Request $request,$id)
    {
		//if(Auth::user()->admin){
			$templates = new Templates;
		//}else{
			//$templates = Templates::where('user_id',$this->getUserId());
		//}
		
        $result =  $templates->where('id',$id)->delete();
		if($result){
			$request->session()->flash('success_message','Delete Templates Success');
		}else{
			$request->session()->flash('error_message','Delete Templates Failed');
		}
        
        return redirect('template');
    }

    public function edit(Request $request,$id)
    {
		//if(Auth::user()->admin){
			$templates = new Templates;
		//}else{
		//	$templates = Templates::where('user_id',$this->getUserId());
		//}
        $template = $templates->where('id',$id)->first()->toArray();
        if(!$template){
            $request->session()->flash('error_message','Template not Exists');
            return redirect('template');
        }
        return view('template/edit')->with('template',$template);
    }

    public function update(Request $request,$id)
    {
         $this->validate($request, [
            'tag' => 'required|string',
			'title' => 'required|string',
            'content' => 'required|string',
        ]);
        //if(Auth::user()->admin){
			$templates = new Templates;
		//}else{
			//$templates = Templates::where('user_id',$this->getUserId());
		//}
        $seller_account = $templates->findOrFail($id);
        $seller_account->tag = $request->get('tag');
        $seller_account->title = $request->get('title');
        $seller_account->content = $request->get('content');
		
        if ($seller_account->save()) {
            $request->session()->flash('success_message','Set Templates Success');
            return redirect('template');
        } else {
            $request->session()->flash('error_message','Set Templates Failed');
            return redirect()->back()->withInput();
        }
    }



}