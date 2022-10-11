<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SubscriptionReminderMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:trial_subscription';

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
     * @return int
     */

    public function subscription_expire()
    {

        // subscription expire in 10 days or 1 days
        $query ='select us.name as name, us.email as email, 
            CONCAT(pr.name, " - ", pl.name) as "product_plan",  
            TIMESTAMPDIFF(day, CURDATE(),sub.ends_at) as "day",
            sub.ends_at as "ends_at",sub.status as "status" from subscriptions sub 
            inner join products pr inner join plans pl inner join users us
            where sub.status="active" and sub.user_id=us.id and sub.plan_id=pl.id and pl.product_id=pr.id
            and (DATE(sub.ends_at)=CURDATE()+INTERVAL 1 DAY or DATE(sub.ends_at)=CURDATE() + INTERVAL 10 DAY);';
        
        $results = DB::select($query);      
        
        foreach($results as $result){
            $maildata = json_decode(json_encode($result),true);              
            try{                
                Mail::send('cron_mail.subscription_expiry_reminder', $maildata, function ($emailMessage) use ($maildata) {
                    $emailMessage->subject('DialPlug Subscription Renewal Alert');
                    $emailMessage->to($maildata['email']);
                    $emailMessage->cc("service-desk@dialplug.com");
                });
            }
            catch(\Exception $e){
                echo "Not Sent.!";
            }                   
            
        }

    }
    public function trial_expire()
    {
        //dialplug trial expire in 3 days or 1 days
        $query ='select CONCAT(us.name, " ", us.last_name) as name, us.email as email, 
        CONCAT(pr.name, " - ", pl.name) as "product_plan",  
        TIMESTAMPDIFF(day, CURDATE(),sub.trial_ends_at) as "day",
        pl.bill_cycle as "cycle",
        sub.trial_ends_at as "trial_ends_at",sub.ends_at as "ends_at",sub.status as "status" from subscriptions sub 
        inner join products pr inner join plans pl inner join users us
        where sub.user_id=us.id and sub.plan_id=pl.id and pl.product_id=pr.id 
        and pl.product_id!=5 and sub.plan_id!=24
        and (DATE(sub.trial_ends_at)=CURDATE()+INTERVAL 1 DAY or DATE(sub.trial_ends_at)=CURDATE()+INTERVAL 3 DAY);';
        
        $results = DB::select($query);
        
        foreach($results as $result)
        {
            $maildata = json_decode(json_encode($result),true);              
            try{                
                Mail::send('cron_mail.trial_expiry_reminder', $maildata, function ($emailMessage) use ($maildata) {
                    $emailMessage->subject('DialPlug Free Trial Subscription Expiry Alert');
                    $emailMessage->to($maildata['email']);
                    $emailMessage->cc("service-desk@dialplug.com");
                });
            }
            catch(\Exception $e){
                echo "Not Sent.!";
            }                   
            
        }   

        //presubscription trial expire in 3 days or 1 days[call tracker]
        $query ='select email as name,"Bitrix24 Mobile Call Tracker" as product_plan,email,(created_at+INTERVAL 10 DAY) as trial_ends_at from bm_config where product_id=5 and 
            (DATE(created_at+INTERVAL 10 DAY)=CURDATE()+INTERVAL 1 DAY or DATE(created_at+INTERVAL 10 DAY)=CURDATE() + INTERVAL 3 DAY);';
        
        $results = DB::select($query);
    
        foreach($results as $result)
        {
            $maildata = json_decode(json_encode($result),true);              
            try{                
                Mail::send('cron_mail.trial_expiry_reminder', $maildata, function ($emailMessage) use ($maildata) {
                    $emailMessage->subject('DialPlug Free Trial Subscription Expiry Alert');
                    $emailMessage->to($maildata['email']);
                    $emailMessage->cc("service-desk@dialplug.com");
                });
            }
            catch(\Exception $e){
                echo "Not Sent.!";
            }                   
            
        }   

        $query ='select email as name,"Bitrix24 Sync Engine - FreePBX" as product_plan,email,(created_at+INTERVAL 10 DAY) as trial_ends_at from bm_config where plan_id=24 and 
            (DATE(created_at+INTERVAL 10 DAY)=CURDATE()+INTERVAL 1 DAY or DATE(created_at+INTERVAL 10 DAY)=CURDATE() + INTERVAL 3 DAY);';
        
        $results = DB::select($query);

        foreach($results as $result)
        {
            $maildata = json_decode(json_encode($result),true);              
            try{                
                Mail::send('cron_mail.trial_expiry_reminder', $maildata, function ($emailMessage) use ($maildata) {
                    $emailMessage->subject('DialPlug Free Trial Subscription Expiry Alert');
                    $emailMessage->to($maildata['email']);
                    $emailMessage->cc("service-desk@dialplug.com");
                });
            }
            catch(\Exception $e){
                echo "Not Sent.!";
            }                   
            
        }    

    }

    public function handle()
    {                
        $this->subscription_expire();
        $this->trial_expire();
    }    
}
