<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpImap;
use Illuminate\Support\Facades\Input;
use PDO;
use DB;
use Log;

class GetEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:email {Id} {time}';

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
        $Id =  $this->argument('Id');
        $time =  $this->argument('time');
        if(!$Id) die;
        if(!$time) $time = '1day';
        $accounts = DB::table('accounts')->where('id',$Id);
        $accountList = $accounts->get();
		Log::useFiles(storage_path().'/logs/'.$Id.'/'.date('Y-m-d').'_email.log','debug');
		$this->rules = DB::table('rules')->orderBy('priority','asc')->get()->toArray();
        foreach($accountList as $account){
            $this->runAccount = array(
                'id' => $account->id,
                'account_email'=> $account->account_email,
                'account_sellerid'=> $account->account_sellerid,
                'email' => $account->email,
                'password' => $account->password,
                'imap_host' => $account->imap_host,
                'imap_ssl' => $account->imap_ssl,
                'imap_port' => $account->imap_port,
				'type' => $account->type,
            );
			$this->saveEmails($time);
        }
    }


    public function saveEmails($time){
        $date = date('j F Y',strtotime('- '.$time));
        $attDir = public_path('attachs').'/'.date('Ymd');
        if (!is_dir($attDir)) mkdir($attDir, 0777,true);
		Log::Info(' '.$this->runAccount['account_email'].' Since '.$date.' Emails Scan Start...');
        $domain = array_get(explode('@',$this->runAccount['email']),1,'');
		$imap_charset = 'UTF-8';
		if($domain=='outlook.com' || $domain=='hotmail.com') $imap_charset = 'US-ASCII';
		
		$junkmailbox = new PhpImap\Mailbox('{'.$this->runAccount['imap_host'].':'.$this->runAccount['imap_port'].'/imap/'.$this->runAccount['imap_ssl'].'}Junk', $this->runAccount['email'], $this->runAccount['password'], $attDir,$imap_charset);
		$junkmailsIds = $junkmailbox->searchMailbox('ALL');
		foreach($junkmailsIds as $junkmailsId){
			$junkmailbox->moveMail($junkmailsId,'INBOX');
		}
		unset($junkmailbox);
		
		
	$mailbox = new PhpImap\Mailbox('{'.$this->runAccount['imap_host'].':'.$this->runAccount['imap_port'].'/imap/'.$this->runAccount['imap_ssl'].'}INBOX', $this->runAccount['email'], $this->runAccount['password'], $attDir,$imap_charset);
		
        $mailsIds = $mailbox->searchMailbox('SINCE "'.$date.'"'); 
        if(!$mailsIds) {
            Log::Info(' '.$this->runAccount['account_email'].' Since '.$date.' Mailbox is empty...');
        }else{
			$mailbox->setServerEncoding('UTF-8');
            foreach($mailsIds as $mailsId){
                $mail = $mailbox->getMail($mailsId);
                //
                $exists = DB::table('inbox')->where('mail_address', $this->runAccount['email'])->where('mail_id', $mail->messageId)->first();
                if(!$exists) {
                    try{
                        $insert_data = array();
                        $attach_data = array();
                        if(!$mail->to) continue;
                        if(!array_key_exists(strtolower($this->runAccount['account_email']),array_change_key_case($mail->to,CASE_LOWER))) continue;
                        if($mail){
							$reply_to = current(array_keys($mail->replyTo));
                            $insert_data['mail_address'] = $this->runAccount['email'];
                            $insert_data['mail_id'] = ($mail->messageId)?$mail->messageId:$mail->id;
                            $insert_data['from_name'] = $mail->fromName;
                            $insert_data['from_address'] = ($reply_to)?$reply_to:$mail->fromAddress;
                            if($insert_data['from_address']=='invalid_address@.syntax-error.'){
								if(preg_match('/From:([\s\S]*?)[\n\r]/i', $mail->headersRaw, $fromstr)){
									$insert_data['from_address'] = str_replace(array(' ','<','>'),'',$fromstr[1]);
								}
							}
                            $insert_data['to_address'] = $this->runAccount['account_email'];
                            $insert_data['subject'] = $mail->subject;
                            $insert_data['text_html'] = $mail->textHtml;
                            $insert_data['text_plain'] = $mail->textPlain;
                            $insert_data['date'] = $mail->date;
							$insert_data['type'] = $this->runAccount['type'];
                            if($mail->getAttachments()){
                                foreach($mail->getAttachments() as $k=>$v){
                                    $attach_data[]=str_ireplace(public_path(),'',$v->filePath);
                                }
                                $insert_data['attachs'] = serialize($attach_data);
                            }
                            $orderInfo = $this->matchOrder($mail);
                            //if($orderInfo){
                            $insert_data['amazon_order_id'] = array_get($orderInfo,'amazon_order_id','');
							$insert_data['amazon_seller_id'] = array_get($orderInfo,'order.SellerId',NULL);
                            $insert_data['sku'] = array_get($orderInfo,'order.Sku', NULL);
							$insert_data['asin'] = array_get($orderInfo,'order.ASIN', NULL);
                            $match_rule = $this->matchUser($insert_data,array_get($orderInfo,'order',array()));

                            if($match_rule['reply_status']==99){
                                if(env('AFTER_GET_MAIL_DELETE',0)){
                                    $mailbox->deleteMail($mailsId);
                                }
                                Log::Info(' Mail From '.$mail->fromAddress.' To '.$this->runAccount['account_email'].' have been trashed...');
                                continue;
                            }
                            if(array_get($match_rule,'etype')) $insert_data['etype'] = $match_rule['etype'];
                            if(array_get($match_rule,'remark')) $insert_data['remark'] = $match_rule['remark'];
							if(array_get($match_rule,'sku')) $insert_data['sku'] = $match_rule['sku'];
							if(array_get($match_rule,'asin')) $insert_data['asin'] = $match_rule['asin'];
							if(array_get($match_rule,'mark')) $insert_data['mark'] = $match_rule['mark'];
							if(array_get($match_rule,'item_no')) $insert_data['item_no'] = $match_rule['item_no'];
							if(array_get($match_rule,'epoint')) $insert_data['epoint'] = $match_rule['epoint'];
                            $insert_data['user_id'] = $match_rule['user_id'];
							$insert_data['group_id'] = $match_rule['group_id'];
                            $insert_data['rule_id'] = $match_rule['rule_id'];
                            $insert_data['reply'] = $match_rule['reply_status'];
							
                            //}

                            $result = DB::table('inbox')->insert($insert_data);
                            if(env('AFTER_GET_MAIL_DELETE',0) && $result){
                                $mailbox->deleteMail($mailsId);
                            }
                            Log::Info(' '.$this->runAccount['account_email'].' MailID '.$mailsId.' Insert Success...');
                        }
                    }catch (\Exception $e){
                        Log::Info(' '.$this->runAccount['account_email'].' MailID '.$mailsId.' Insert Error...'.$e->getMessage());
                    }
                }else{
                    Log::Info(' '.$this->runAccount['account_email'].' MailID '.$mailsId.' AlReady Exists...');
                }
            }
        }
        Log::Info(' '.$this->runAccount['account_email'].' Since '.$date.' Emails Scan Complete...');
    }

    public function matchOrder($mail){
        //先匹配中间件1个月内订单，同步到导入到本地，再匹配本地订单
        //标题中含有订单号
        $data = array();
        preg_match('/\d{3}-\d{7}-\d{7}/i', $mail->subject, $order_str);
        if(isset($order_str[0])){
            $data['amazon_order_id'] = $order_str[0];
        }elseif(stripos($mail->fromAddress,'marketplace.amazon') !== false){
            $data['amazon_order_id'] = $this->getOrderByEmail($mail->fromAddress);
        }else{
            $data['amazon_order_id']='';
        }
        if($data['amazon_order_id'] && stripos($mail->fromAddress,'marketplace.amazon') !== false){
            $data['order'] = $this->SaveOrderToLocal($data['amazon_order_id']);
        }
        return $data;

    }
	
	public function assignGroupUser($group_id,$mailData){
		$time =  date('Hi',strtotime(array_get($mailData,'date')));
		
		$date =  date('Y-m-d',strtotime(array_get($mailData,'date')));
		$users = DB::table('group_detail')->where('group_id',$group_id)->whereRaw("replace(time_from,':','')<=".$time)->whereRaw("replace(time_to,':','')>=".$time)->get();
		
		$users_arr =array();
		foreach($users as $user){
			$users_arr[] = $user->user_id;
		}
		if($users_arr){
			$user_mail_count = DB::table('inbox')->select(DB::raw('count(*) as count,user_id,group_id'))->where('group_id',$group_id)->whereIn('user_id',$users_arr)->where('date','<=',$date.' 23:59:59')->where('date','>=',$date.' 00:00:00')->groupBy(['user_id','group_id'])
    		->orderBy('count', 'asc')->get();
			if(count($user_mail_count)>0){
				//print_r($user_mail_count);
				//print_r($user_mail_count[0]->user_id);
				return $user_mail_count[0]->user_id;
			}else{
				//print_r($users_arr[0]);
				return $users_arr[0];
			}
		}else{
			return 0;
		}
		
	}
	public function getUserGroupRand($user_id){
		$group = DB::table('group_detail')->where('user_id',$user_id)->first();
		if($group){
			$group_id = $group->group_id;
		}else{
			$group_id = 0;
		}
		return $group_id;
	}
	
	

	
    public function matchUser($mailData,$orderData){
		$return_data= array();
        $orderId = array_get($mailData,'amazon_order_id','');
        $lastUser = DB::table('inbox')->where('from_address',array_get($mailData,'from_address',''))
            ->where('to_address',$this->runAccount['account_email'])
            ->where('amazon_order_id',$orderId)->orderBy('date','desc')->first();
        if($lastUser){
            $return_data = array('etype'=>$lastUser->etype,'remark'=>$lastUser->remark,'sku'=>$lastUser->sku,'asin'=>$lastUser->asin,'item_no'=>$lastUser->item_no,'mark'=>$lastUser->mark,'epoint'=>$lastUser->epoint);
			 
        }
		//
		$lastSend = DB::table('sendbox')->where('to_address',array_get($mailData,'from_address',''))
            ->where('from_address',$this->runAccount['account_email'])->orderBy('date','desc')->first();
        if($lastSend){
			if($lastSend->inbox_id==0){
			
				 $exists = DB::table('group_detail')->where('user_id',$lastSend->user_id)->first();
				 if($exists){
					 $return_data['user_id'] = $lastSend->user_id;
					 $return_data['group_id'] = $this->getUserGroupRand($lastSend->user_id);
					 $return_data['reply_status'] =0;
					 $return_data['rule_id'] = 888888;
					 return $return_data;
				 }
			}
		}
		if($lastUser){
			$exists = DB::table('group_detail')->where('user_id',$lastUser->user_id)->where('group_id',$lastUser->group_id)->first();
			if($exists){
				$return_data['user_id'] = $lastUser->user_id;
				$return_data['group_id'] = $lastUser->group_id;
				$return_data['reply_status'] =0;
				$return_data['rule_id'] = $lastUser->rule_id;
				return $return_data;
			}
		}
		
        
		
		$orderItems = array_get($orderData,'OrderItems',array());
		
		if($orderItems){			 
			 $asin_rule = DB::table('asin')->where('sellersku',array_get($orderItems[0],'SellerSKU'))->where('asin',array_get($orderItems[0],'ASIN'))->where('site','www'.array_get($orderData,'SalesChannel'))->first();
			 if($asin_rule){
			 	 if($asin_rule->group_id){
					 $return_data['user_id'] = $this->assignGroupUser($asin_rule->group_id,$mailData);
					 $return_data['group_id'] = $asin_rule->group_id;
					 $return_data['reply_status'] =0;
					 $return_data['item_no'] = array_get($return_data,'item_no',$asin_rule->item_no);
					 $return_data['rule_id'] = (900000+($asin_rule->id));
					 
					 return $return_data;
				 }
			 }		 
		}
		
		
        $rules = $this->rules;//DB::table('rules')->orderBy('priority','asc')->get()->toArray();
        $orderItems = array_get($orderData,'OrderItems',array());
        $orderSkus = ''; $orderAsins = array();
        foreach($orderItems as $item){
            $orderSkus.= $item['SellerSKU'].';';
            $orderAsins[]=$item['ASIN'];
        }
        foreach($rules as $rule){
            //标题匹配
            if($rule->subject){
                $matched = false;
                $subject_match_array = explode(';',$rule->subject);
                foreach($subject_match_array as $subject_match_string){
                    if($subject_match_string && stripos(array_get($mailData,'subject',''),$subject_match_string) !== false){
                        $matched = true;
                    }
					if($subject_match_string && stripos(array_get($mailData,'text_plain',''),$subject_match_string) !== false){
                        $matched = true;
                    }
					if($subject_match_string && stripos(strip_tags(array_get($mailData,'text_html','')),$subject_match_string) !== false){
                        $matched = true;
                    }
					
                }
                if(!$matched) continue;
            }

            //发件人匹配
            if($rule->from_email){
                $matched = false;
                $from_match_array = explode(';',$rule->from_email);
                foreach($from_match_array as $from_match_string){
                    if($from_match_string && stripos(array_get($mailData,'from_address',''),$from_match_string) !== false){
                        $matched = true;
                    }
                }
                if(!$matched) continue;
            }

            //收件人匹配
            if($rule->to_email){
                $matched = false;
                $to_match_array = explode(';',$rule->to_email);
                foreach($to_match_array as $to_address){
                    if($to_address == $this->runAccount['account_email'] ){
                        $matched = true;
                    }
                }
                if(!$matched) continue;
            }

            //站点匹配
            /*
            if($rule->site){
                $matched = false;
                $site_match_array = explode(';',$rule->site);
                if(in_array($data->site,$site_match_array) ){
                    $matched = true;
                }
                if(!$matched) continue;
            }
            */
            //Asin匹配
            if($rule->asin){
                $matched = false;
                $asin_match_array = explode(';',$rule->asin);
                foreach($orderAsins as $asin){
                    if($asin   && in_array($asin,$asin_match_array)){
                        $matched = true;
                    }
                }
                if(!$matched) continue;
            }


            //Sku匹配
            if($rule->sku){
                $matched = false;
                $sku_match_array = explode(';',$rule->sku);
                foreach($sku_match_array as $sku){
                    $str=array();
                    preg_match('/'.$sku.'(\d{4})/i', $orderSkus,$str);
                    if($sku && $str){
                        $matched = true;
                    }
                }
                if(!$matched) continue;
            }
			$return_data['user_id'] = $this->assignGroupUser($rule->group_id,$mailData);
			$return_data['group_id'] = $rule->group_id;
			$return_data['reply_status'] =$rule->reply_status;
			$return_data['rule_id'] = $rule->id;
			return $return_data;
        }
		
		$return_data['user_id'] = env('SYSTEM_AUTO_REPLY_USER_ID',1);
		$return_data['group_id'] = 15;
		$return_data['reply_status'] =0;
		$return_data['rule_id'] = 0;
		return $return_data;

    }

    public function getOrderByEmail($email){
        $order = DB::connection('order')->table('amazon_orders')->where('SellerId',$this->runAccount['account_sellerid'])->where('BuyerEmail',$email)->orderBy('LastUpdateDate','Desc')->first();
        if($order){
            return $order->AmazonOrderId;
        }

        $order = DB::table('amazon_orders')->where('SellerId',$this->runAccount['account_sellerid'])->where('BuyerEmail',$email)->orderBy('LastUpdateDate','Desc')->first();
        if($order){
            return $order->AmazonOrderId;
        }

        return '';
    }


    public function SaveOrderToLocal($orderId){
        $returnData = array();
        $exists = DB::table('amazon_orders')->where('SellerId', $this->runAccount['account_sellerid'])->where('AmazonOrderId', $orderId)->first();
        if(!$exists){
            $order = DB::connection('order')->table('amazon_orders')->where('SellerId',$this->runAccount['account_sellerid'])->where('AmazonOrderId',$orderId)->first();
            if($order){
                $orderItems = DB::connection('order')->table('amazon_orders_item')->where('SellerId',$this->runAccount['account_sellerid'])->where('AmazonOrderId',$order->AmazonOrderId)->get();
                $order->OrderItems= $orderItems;
                $order->Sku= isset($orderItems[0]->SellerSKU)?$orderItems[0]->SellerSKU:'';
				$order->ASIN= isset($orderItems[0]->ASIN)?$orderItems[0]->ASIN:'';
                $order = json_decode(json_encode( $order),true);
                unset($order['ImportToSap']);
                $returnData = $order;
                DB::beginTransaction();
                try{
                    DB::table('amazon_orders_item')->insert($order['OrderItems']);
                    unset($order['OrderItems']);
                    unset($order['Sku']);
					unset($order['ASIN']);
                    DB::table('amazon_orders')->insert($order);
                    DB::commit();
                    Log::Info(' SellerID: '.$this->runAccount['account_sellerid'].' AmazonOrderId: '.array_get($order,'AmazonOrderId').' Save Success...');
                } catch (\Exception $e) {
                    DB::rollBack();
                }
            }
        }else{
            $order = $exists;
            $order->OrderItems = $orderItems = DB::table('amazon_orders_item')->where('SellerId',$this->runAccount['account_sellerid'])->where('AmazonOrderId',$order->AmazonOrderId)->get();
            $order->Sku= isset($orderItems[0]->SellerSKU)?$orderItems[0]->SellerSKU:'';
			$order->ASIN= isset($orderItems[0]->ASIN)?$orderItems[0]->ASIN:'';
            $order = json_decode(json_encode( $order),true);
            $returnData = $order;
        }
        return $returnData;
    }
}
