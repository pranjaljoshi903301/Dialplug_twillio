<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UsageReportMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:report';

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

    
    public function weekly_usage_report($id,$email,$product){
        $query=false;
        if($product==4){ // audit_bitrix_manage_telephony
            $query1 = 'select freepbx_domain from bt_config where user_id='.$id.' limit 1';
            $output = DB::select($query1);
            if(count($output)){                
                $freepbx_domain = "https://".$output[0]->freepbx_domain.".dialplug.com";
                $query='select agent "Agent Extension", call_type as "Call Type", call_duration as "Call Duration", call_date as "Call Date" from Dialplug_Misc.audit_bitrix_manage_telephony where call_date >=(current_date()-interval 7 Day) and freepbx_domain='.'"'.$freepbx_domain.'"';
            }
        }
        elseif($product==5){ // audit_bitrix_mobile_connector
            $query='select subscriber_email as "Subscriber Email", agent_email "Agent Email", call_type as "Call Type", call_duration as "Call Duration", created_at as "Call Date" from Dialplug_Misc.audit_bitrix_mobile_connector where created_at>=(current_date()-interval 7 Day) and subscriber_email='.'"'.$email.'"';
        }
        elseif($product==6){ //audit_bitrix_cloudpbx
            $query='select agent "Agent Extension", call_type as "Call Type", call_duration as "Call Duration", call_date as "Call Date" from Dialplug_Misc.audit_bitrix_cloudpbx where call_date >=(current_date()-interval 7 Day) and subscriber_email='.'"'.$email.'"';
        }
        if($query){
            $query = DB::select($query);              
        }
        return $query;
    }    

    public function excel($data)
    {        
        if (!$fp = fopen('php://temp', 'w+')) return FALSE;
        $headings = array_keys($data[0]);
        fputcsv($fp, $headings);
        foreach($data as $dt) fputcsv($fp, $dt);
        rewind($fp);        
        return stream_get_contents($fp);
    }

    public function send_email($email,$data,$name,$product_id)
    {                
        $attachment = $this->excel($data);               
        $subject_email = "DialPlug Managed Telephony Weekly Usage Report";
        if($product_id==5){
            $subject_email = "DialPlug Call Tracker Weekly Usage Report";
        }elseif($product_id==6){
            $subject_email = "DialPlug Cloud PBX Weekly Usage Report";
        }
        try{                
            Mail::send('cron_mail.usage_report', array('name'=>$name,'subject_email'=>$subject_email), function ($emailMessage) use($attachment,$email,$subject_email) {
                $emailMessage->subject($subject_email);
                // $emailMessage->to("yuvraj.hinger@digiclave.com");
                $emailMessage->to($email);
                $emailMessage->cc("service-desk@dialplug.com");
                $emailMessage->attachData($attachment,'dialplug_usage_report.csv');
            });            
        }
        catch(\Exception $e){
            echo "Not Sent.!";
        }                   
    }

    public function handle()
    {                        
        $query = 'select us.name as username,us.id as user_id,us.email as email, pl.id as "planid", pr.id as "product"
            from subscriptions sub inner join products pr inner join plans pl inner join users us
            where sub.user_id=us.id and sub.plan_id=pl.id and pl.product_id=pr.id and sub.status="active" order by sub.created_at desc';
        $results = DB::select($query);                                      
        foreach($results as $result){
            $output = $this->weekly_usage_report($result->user_id,$result->email,$result->product);
            if($output){
                if(count($output)){
                    $output = json_decode(json_encode($output), true);        
                    $this->send_email($result->email,$output,$result->username,$result->product);
                }
            }
        }
        
    }
}
