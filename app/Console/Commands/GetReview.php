<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpImap;
use Illuminate\Support\Facades\Input;
use PDO;
use DB;
use Log;

class GetReview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:review {time}';

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
		
        $time =  $this->argument('time');
        if(!$time) $time = '3days';
		$date_from=date('Y-m-d',strtotime('-'.$time));		
		$words = getReviewWarnWords();
		$reviewList = DB::connection('review_new')->query('SET NAMES latin1');
		$reviewList = DB::connection('review_new')->select("
		select date(tbl_review_hunter_review.review_date) as date,tbl_star_system_product.asin,
tbl_star_system_product.domain,tbl_review_hunter_review.review,customer_id,is_delete,vp,title,rating,
tbl_review_hunter_review.new_rating as updated_rating,tbl_review_hunter_review.content,substring_index(substring_index(tbl_review_hunter_review.reviewer_name,'a-profile-name\">',-1),'<',1) as reviewer_name
from  tbl_review_hunter_review 
left join tbl_star_system_product using(product_id)
where (tbl_review_hunter_review.last_updated>=:date_from and tbl_review_hunter_review.rating<>tbl_review_hunter_review.new_rating)
or tbl_review_hunter_review.created_at>=:date_from1",['date_from' => $date_from,'date_from1' => $date_from]);
		
		foreach($reviewList as $review){
			$exists = DB::table('review')->where('review', $review->review)->where('site', $review->domain)->first();
			$insert_data = array();
			$insert_data['date'] = $review->date;
			$insert_data['reviewer_name'] = $review->reviewer_name;
			$insert_data['review_content'] = $review->content;
			$insert_data['customer_id'] = $review->customer_id;
			$insert_data['rating'] = $review->rating;
			$insert_data['updated_rating'] = $review->updated_rating;
			$insert_data['vp'] = $review->vp;
			$insert_data['title'] = $review->title;
			$insert_data['is_delete'] = $review->is_delete;
			foreach($words as $word){
				if(stripos($review->content,$word) !== false) $insert_data['warn'] = 1;
			}
			if(!$exists) {
				$user_id = 0;
				$insert_data['asin'] = $review->asin;
				$insert_data['site'] = $review->domain;
				$insert_data['review'] = $review->review;
				$insert_data['status'] = 1;
				
				$user_arr = DB::table('asin')->where('asin', $review->asin)->where('site', $review->domain)->first();
				if(isset($user_arr->review_user_id)){
					$user_id = $user_arr->review_user_id;
				}
				if($user_id) $insert_data['user_id'] = $user_id;
				$result = DB::table('review')->insert($insert_data);
			}else{
				DB::table('review')->where('review', $review->review)->where('site', $review->domain)->update($insert_data);
			}
				
    	}
		
		DB::update("update review set negative_value=IFNULL((select negative_value from seller_asins where seller_asins.asin=review.asin and seller_asins.site=review.site)+(case 
	when DATEDIFF(CURRENT_DATE(),date)<=1 then 10
	when DATEDIFF(CURRENT_DATE(),date)<=7 and DATEDIFF(CURRENT_DATE(),date)>1 then 9
	when DATEDIFF(CURRENT_DATE(),date)<=30 and DATEDIFF(CURRENT_DATE(),date)>7 then 8
	when DATEDIFF(CURRENT_DATE(),date)<=60 and DATEDIFF(CURRENT_DATE(),date)>30 then 7
	when DATEDIFF(CURRENT_DATE(),date)<=120 and DATEDIFF(CURRENT_DATE(),date)>60 then 6
	when DATEDIFF(CURRENT_DATE(),date)<=180 and DATEDIFF(CURRENT_DATE(),date)>120 then 5
	when DATEDIFF(CURRENT_DATE(),date)<=360 and DATEDIFF(CURRENT_DATE(),date)>180 then 4
	when DATEDIFF(CURRENT_DATE(),date)<=720 and DATEDIFF(CURRENT_DATE(),date)>360 then 3
	when DATEDIFF(CURRENT_DATE(),date)<=1000 and DATEDIFF(CURRENT_DATE(),date)>720 then 2
	when DATEDIFF(CURRENT_DATE(),date)>1000 then 1
	End)+(case 
	when rating=1 then 10
	when rating=2 then 8
	when rating=3 then 6
	else 0
	End),0) where date>'2015-01-01' and `status` =1");
	
	}

}
