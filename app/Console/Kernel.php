<?php

namespace App\Console;

use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App;
use PDO;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\GetEmails',
        'App\Console\Commands\ScanEmails',
        'App\Console\Commands\SendEmails',
        'App\Console\Commands\Warning',
        'App\Console\Commands\AutoReply',
		'App\Console\Commands\GetReview',
		'App\Console\Commands\GetStar',
		'App\Console\Commands\GetAsin',
		'App\Console\Commands\GetOrder',
		'App\Console\Commands\GetSellers',
		'App\Console\Commands\GetAsininfo',
		'App\Console\Commands\GetAds',
		'App\Console\Commands\GetProfits',
        'App\Console\Commands\GetSettlementReport',
        'App\Console\Commands\GetAwsInfo',
		'App\Console\Commands\GetSales28day',
		'App\Console\Commands\GetShoudafang'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 由于 php artisan 命令会触发 schedule 执行；

        // if (!Schema::hasTable('accounts')) return;

        // 防止第一次执行 php artisan migrate 时，报找不到表的错误；
		$schedule->call(function (){
			DB::update('update rsg_products set daily_remain = daily_stock;');
        })->dailyAt('14:00');
        $accountList = DB::table('accounts')->get(array('id'));
        $i=0;
        foreach($accountList as $account){
            if($i>59) $i=0;
            $schedule->command('get:email '.$account->id.' 1hours')->cron('*/30 * * * *')->name($account->id.'_get_emails')->withoutOverlapping();
            $i++;
        }

        $schedule->command('scan:send')->cron('*/5 * * * *')->name('sendmails')->withoutOverlapping();
		$schedule->command('get:order')->cron('*/30 * * * *')->name('getOrder')->withoutOverlapping();
		$schedule->command('get:review 7days')->cron('0 */4 * * *')->name('getreviews')->withoutOverlapping();
		$schedule->command('get:star 7days')->twiceDaily(20, 22)->name('getstars')->withoutOverlapping();
		$schedule->command('get:asin 3 0')->hourly()->name('getasins')->withoutOverlapping();
		$schedule->command('get:kunnr 3 0')->hourly()->name('getkunnrs')->withoutOverlapping();
		$schedule->command('get:sellers')->cron('*/1 * * * *')->name('sendmails')->withoutOverlapping();
		$schedule->command('get:asininfo')->cron('30 0 * * *')->name('getasininfo')->withoutOverlapping();
		$schedule->command('get:ads 10 1')->cron('5 0 * * *')->name('getads')->withoutOverlapping();
		$schedule->command('get:profits 10 1 ')->cron('10 0 * * *')->name('getprotit')->withoutOverlapping();
        //$schedule->command('scan:warn')->hourly()->name('warningcheck')->withoutOverlapping();
        //$schedule->command('scan:auto')->hourly()->name('autocheck')->withoutOverlapping();
        $schedule->command('get:awsinfo')->dailyAt('23:00')->name('getawsinfo')->withoutOverlapping();
		$schedule->command('get:dailysales 7')->dailyAt('9:00')->name('getdailysales')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
