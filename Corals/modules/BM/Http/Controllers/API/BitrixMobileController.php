<?php

namespace Corals\Modules\BM\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\BM\Services\BitrixMobileService;
use Corals\Modules\BM\Transformers\API\BitrixMobilePresenter;
use Corals\Modules\Subscriptions\Classes\Subscription;
use Corals\Modules\Subscriptions\Http\Requests\API\SubscriptionCheckoutRequest;
use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\Subscriptions\Services\SubscriptionService;
use Corals\Modules\Subscriptions\Transformers\API\SubscriptionPresenter;
use Corals\Settings\Models\Setting;
use Corals\User\Http\Requests\UserRequest;
use Corals\User\Models\User;
use Corals\User\Services\UserService;
use Corals\User\Transformers\API\UserPresenter;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BitrixMobileController extends APIBaseController
{
    protected $bitrixmobileService;

    /**
     * BitrixMobileController constructor.
     * @param BitrixMobileService $bitrixmobileService
     * @throws \Exception
     */
    public function __construct(BitrixMobileService $bitrixmobileService)
    {
        $this->bitrixmobileService = $bitrixmobileService;
        $this->bitrixmobileService->setPresenter(new BitrixMobilePresenter());

        if (!in_array('get_by_mobile_number', $this->corals_middleware_except)) {
            array_push($this->corals_middleware_except, "get_by_mobile_number");
        }

        //parent::__construct();
    }
    
    public function get_by_mobile_number ($request) {
        $bitrix_user = BitrixMobile::where('mobile_number', $request)->first();
        if ($bitrix_user) {
            return response()->json($bitrix_user, 200);
        } else {
            return response()->json('Not Found', 404);
        }
    }

    // All 5 methods are used in validation api
    // Method 1
    public function  is_user_exists($email){
        $user = User::where('email',$email)->first();
        return $user;
    }
    // Method 2
    public function subscription_validation($order_time,$free_trial,$subscription_status=null){
        $current_time = \Carbon\Carbon::now();
        $order_time =  \Carbon\Carbon::parse($order_time);        
        $time_interval = $order_time->diffInDays($current_time,false);                                                
        if($subscription_status=="active" || $time_interval<=$free_trial){            
            return true;
        }        
        return false;
    }    
    public function extractDomain($input)
    {
        $input = str_replace(' ', '', $input);
        $input = trim($input, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);
        $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
        return $domain_name;
    }
    function imei_exist_or_not($obj_arr,$imei,$agent_email)
    {
        foreach($obj_arr as $agent) {
            if(($agent['imei']==$imei)&&($agent['email']==$agent_email)) return true;            
        }
        return false;
    }
    // Method 3
    public function local_table_manage($email,$webhook,$free_trial=10,$product_id,$phone_number=null,$plugin_plan_id=null,$plan_name=null)      //emailchanges
    {        
        
        // Extract Domain from Webhook
        $domain = $this->extractDomain($webhook);

        if($plugin_plan_id!=null)        
            $local_instance = BitrixMobile::where([['email',$email],['domain',$domain],['webhook_url',$webhook],['product_id',$product_id],['plan_id',$plugin_plan_id]])->first();

        else
            $local_instance = BitrixMobile::where([['email',$email],['domain',$domain],['webhook_url',$webhook],['product_id',$product_id]])->first();        

        if($local_instance){                                     
            
            if(!$this->subscription_validation($local_instance->created_at,$free_trial))            
                return false;                

            /*
            // Send mail if one three days before free trial expired
            if(!$this->subscription_validation($local_instance->created_at,$free_trial-3)){
                // send EMAIL 2
                $send_mail = false;
                $last_date = $local_instance->last_email_date;
                if($last_date!=NULL){                    
                    $last_date = json_decode($last_date,true);
                    $last_time = $last_date['expire'];
                    if(!$last_time){
                        $send_mail = true;
                        $last_date['expire']=\Carbon\Carbon::now();
                        $local_instance->last_email_date = json_encode($last_date);
                        $local_instance->save();
                    }                 
                }
                else{
                    $send_mail = true;
                    $last_date = array();                    
                    $last_date['expire']=\Carbon\Carbon::now();
                    $last_date['agentlimit']=false;
                    $local_instance->last_email_date = json_encode($last_date);
                    $local_instance->save();
                }                

                if($send_mail){
                    $maildata = array(
                        'email' => $email,
                        'plan_name' => $plan_name
                    );                
                    try{
                        Mail::send('email.ExpireSubscriptionMail', $maildata, function ($emailMessage) use ($maildata, $email) {
                            $emailMessage->subject('DialPlug Free Trial Subscription Expiry Alert');
                            $emailMessage->to($email);
                            $emailMessage->cc("service-desk@dialplug.com");
                        });
                    }
                    catch(\Exception $e){
                        return true;
                    }                
                }
            }
            */
            return true;
        }
        else{            
            if($plugin_plan_id!=null)
                $local_instance = BitrixMobile::where([['domain',$domain],['product_id',$product_id],['plan_id',$plugin_plan_id]])->first();                        
            else
                $local_instance = BitrixMobile::where([['domain',$domain],['product_id',$product_id]])->first();                        
            
            if($local_instance) 
                return false;
            
            $local_instance = new BitrixMobile();
            $local_instance->email = $email;
            $local_instance->webhook_url = $webhook;
            $local_instance->product_id = $product_id;

            if($phone_number!=null) $local_instance->mobile_number = $phone_number;
            if($plugin_plan_id!=null) $local_instance->plan_id = $plugin_plan_id;

            $local_instance->domain = $domain;
            $local_instance->save();
            
            // send EMAIL 1            
                        
            $maildata = array(
                'email' => $email,                
                'plan_name' => $plan_name
            );            
            try{
                Mail::send('email.PreSubscriptionMail', $maildata, function ($emailMessage) use ($maildata, $email) {
                    $emailMessage->subject('Dialplug Free Trial Subscription Mail');
                    $emailMessage->to($email);
                    $emailMessage->cc("service-desk@dialplug.com");
                });
            }
            catch(\Exception $e){
                return true;
            }                

            return true;
        }                
    }
    // Method 4
    public function imei_validation($webhook,$imei_number,$agent_count,$email,$product_id,$phone_number,$agent_email) 
    {                                                                                        
        $is_valid = false;        
        
        // Extract Domain from Webhook
        $domain = $this->extractDomain($webhook);
        
        $local_instance = BitrixMobile::where([['email',$email],['webhook_url',$webhook],['product_id',$product_id]])->first();
        if($local_instance)
        {                                    
            $agent_detail = $local_instance->agent_detail;
            if($agent_detail!=NULL)
            {                
                $agent_detail = json_decode($agent_detail,true);                
                if($this->imei_exist_or_not($agent_detail,$imei_number,$agent_email)) $is_valid = true;
                elseif(count($agent_detail)<$agent_count)
                {                                                
                    $is_valid = true;                    
                    $agent_obj = array();
                    $agent_obj['email'] = $agent_email;
                    $agent_obj['imei'] = $imei_number;
                    $agent_obj['created_at'] = now();
                    $agent_detail[] = $agent_obj;
                    $local_instance->agent_detail = json_encode($agent_detail);
                    $local_instance->save();
                }else{
                    // SEND EMAIL TO USER ABOUT AGENT LIMIT EXCEED                    
                    $send_mail = false;
                    $last_date = $local_instance->last_email_date;
                    if($last_date!=NULL)
                    {                    
                        $last_date = json_decode($last_date,true);
                        $last_time = $last_date['agentlimit'];
                        if(!$last_time){
                            $send_mail = true;
                            $last_date['agentlimit']=\Carbon\Carbon::now();
                            $local_instance->last_email_date = json_encode($last_date);
                            $local_instance->save();
                        }                        
                        else
                        {              
                            $current_time = \Carbon\Carbon::now();      
                            $last_time = \Carbon\Carbon::parse($last_time);
                            $time_interval = $current_time->diffInHours($last_time);
                            if($time_interval>2){
                                $send_mail = true;
                                $last_date['agentlimit']=\Carbon\Carbon::now();
                                $local_instance->last_email_date = json_encode($last_date);
                                $local_instance->save();
                            }
                        }
                    }
                    else
                    {
                        $send_mail = true;
                        $last_date = array();                    
                        $last_date['agentlimit']=\Carbon\Carbon::now();
                        $last_date['expire']=false;
                        $local_instance->last_email_date = json_encode($last_date);
                        $local_instance->save();
                    }   
                                                     
                    if($send_mail)
                    {
                        $maildata = array(
                            'email' => $email,
                            'body' => "Any custom Message.!",
                            'webhook' => $webhook,
                            'agentlimit' => $agent_count
                        );                
                        try{
                            Mail::send('email.AgentLimitExceed', $maildata, function ($emailMessage) use ($maildata, $email) {
                                $emailMessage->subject('Dialplug Subscription Agent Limit Exceed Mail');
                                $emailMessage->to($email);
                                $emailMessage->cc("service-desk@dialplug.com");
                            });
                        }
                        catch(\Exception $e){
                            return false;
                        }                
                    }

                    $is_valid = false;                    
                }
            }
            else
            {
                $is_valid = true;

                $agent_object = array();
                $agent_obj = array();
                $agent_obj['email'] = $agent_email;
                $agent_obj['imei'] = $imei_number;
                $agent_obj['created_at'] = now();
                $agent_object[] = $agent_obj;
                $local_instance->agent_detail = json_encode($agent_object);                
                $local_instance->save();                
            }
        }
        else{            
            $local_instance = BitrixMobile::where([['domain',$domain],['product_id',$product_id]])->first();
            if($local_instance) return false;

            $local_instance = new BitrixMobile();
            $local_instance->email = $email;
            $local_instance->webhook_url = $webhook;
            $local_instance->product_id = $product_id;
            if($phone_number!=null) $local_instance->mobile_number = $phone_number;
            $local_instance->domain = $domain;
            
            
            $agent_object = array();
            $agent_obj = array();
            $agent_obj['email'] = $agent_email;
            $agent_obj['imei'] = $imei_number;
            $agent_obj['created_at'] = now();
            $agent_object[] = $agent_obj;
            $local_instance->agent_detail = json_encode($agent_object);

            $local_instance->save();                        

            $is_valid = true;        
        }
        return $is_valid;
    }
    // Method 5
    public function is_user_subscribed_or_in_trial(Request $request){
        $response = array();
        $response['is_valid'] = false;
        $response['is_presubscription'] = true;

        $webhook = $request->webhook;                        

        $email = $request->email;                
        $product_name = $request->product_name;
        $phone_number = null;
        
        // default
        $agent_count = 10; 
        $free_trial = 10;
        $imei_product_id = 5;
        $plugin_product_id = 7;

        $getimei_product_id = DB::table('products')->where('name','Bitrix24 Mobile Sync')->first();
        if($getimei_product_id) $imei_product_id = $getimei_product_id->id;
        
        $getplugin_product_id = DB::table('products')->where('name','Bitrix24 Sync Engine')->first();
        if($getplugin_product_id) $plugin_product_id = $getplugin_product_id->id;
                
        $product = DB::table('products')->where('name',$product_name)->first();        
        if(empty($product))
        {
            $response['error'] = "Product name not exists.!";
            return $response;
        }        
        $product_id = $product->id;                

        $response['is_presubscription'] = $this->presubscription_check($email,$product_id);

        if($product_id==$imei_product_id)
        {
            if($request->has('imei') && $request->has('agent_email'))
            {
                $imei_number = $request->imei;                
                $agent_email = $request->agent_email;
            }
            else
            {
                $response['error'] = "Insufficient fields.";
                return $response;
            }
        }
        elseif($product_id==$plugin_product_id)
        {
            if($request->has('plan_name'))
            {
                $plan_name = $request->plan_name;                
                $plugin_plan = DB::table('plans')->where('name',$plan_name)->first();
                if(empty($plugin_plan))
                {
                    $response['error'] = "Plan name not exists.!";
                    return $response;
                }    
                $plugin_plan_id = $plugin_plan->id;
            }
            else
            {
                $response['error'] = "Insufficient fields.";
                return $response;
            }
        }
        
        $user = $this->is_user_exists($email);                 
        if($user)
        {            
            if($product_id==$plugin_product_id) // call only for freepbx
            {
                $subscription = DB::table('subscriptions')->where([['user_id',$user->id],['status','!=','canceled'],['plan_id',$plugin_plan_id]])->first();
                if($subscription)
                {
                    if($this->subscription_validation($subscription->created_at,$free_trial,$subscription->status))
                    {
                        $response['is_valid'] = true;
                    }
                }
                else
                {
                    $valid_instance_flag = $this->local_table_manage($email,$webhook,$free_trial,$product_id,$phone_number,$plugin_plan_id,$request->plan_name);
                    if($valid_instance_flag)
                    {
                        $response['is_valid'] = true;
                    }
                }                
            }        
            else{
                $subscriptions = DB::table('subscriptions')->where([['user_id',$user->id],['status','!=','canceled']])->get();                                    
                if(count($subscriptions))
                {                
                    $plan_found = false;                
                    foreach($subscriptions as $subscription)
                    {                                        
                        $plan = DB::table('plans')->where([['id',$subscription->plan_id],['status','active'],['product_id',$product_id]])->first();                    
                        if($plan && !$plan_found)
                        {                                
                            $plan_found = true;
                            if($this->subscription_validation($subscription->created_at,$free_trial,$subscription->status))
                            {                                                                                    
                                $response['is_valid'] = true;
                                // for mobile tracker go for imei validation
                                if($product_id==$imei_product_id)
                                {                                
                                    // update agent count
                                    $featureID='-1';
                                    $feature = DB::table('features')->where([['name', 'Number of Users'],['product_id',$product_id]])->first();
                                    if($feature) $featureID=$feature->id;
                                    $feature_plan = DB::table('feature_plan')->where([['plan_id',$plan->id],['feature_id',$featureID]])->first();
                                    if($feature_plan) $agent_count = $feature_plan->value;
                                    $response['is_valid'] = $this->imei_validation($webhook,$imei_number,$agent_count,$email,$product_id,$phone_number,$agent_email);
                                }
                                break;
                            }                        
                        }
                    }                                
                    if(!$plan_found)
                    {
                        $valid_instance_flag = $this->local_table_manage($email,$webhook,$free_trial,$product_id,$phone_number);
                        if($valid_instance_flag)
                        {
                            $response['is_valid'] = true;
                            if($product_id==$imei_product_id)                    
                                $response['is_valid'] = $this->imei_validation($webhook,$imei_number,$agent_count,$email,$product_id,$phone_number,$agent_email);
                        }
                    }                
                }
                else
                {                
                    $valid_instance_flag = $this->local_table_manage($email,$webhook,$free_trial,$product_id,$phone_number);
                    if($valid_instance_flag)
                    {
                        $response['is_valid'] = true;
                        if($product_id==$imei_product_id)                    
                            $response['is_valid'] = $this->imei_validation($webhook,$imei_number,$agent_count,$email,$product_id,$phone_number,$agent_email);
                    }
                }
            }
        }
        else
        {                                                
            
            if($product_id==$plugin_product_id)
            {
                $valid_instance_flag = $this->local_table_manage($email,$webhook,$free_trial,$product_id,$phone_number,$plugin_plan_id,$request->plan_name);
                if($valid_instance_flag)
                {
                    $response['is_valid'] = true;
                }
            }
            else
            {
                $valid_instance_flag = $this->local_table_manage($email,$webhook,$free_trial,$product_id,$phone_number);            
                if($valid_instance_flag)
                {
                    $response['is_valid'] = true;
                    if($product_id==$imei_product_id)
                        $response['is_valid'] = $this->imei_validation($webhook,$imei_number,$agent_count,$email,$product_id,$phone_number,$agent_email);
                }
            }
        }
        return $response;
    }

    public function testing()
    {        
        \Log::info(json_encode($_REQUEST));
        //  print_r($_REQUEST);
        // $maildata = array(
        //     'email' => "yuvrajhingerakola@gmail.com",
        //     'plan_name' => "1"
        // );                
        // try{
        //     Mail::send('email.ExpireSubscriptionMail', $maildata, function ($emailMessage) use ($maildata) {
        //         $emailMessage->subject('Dialplug Free Trial Subscription Expire Mail');
        //         $emailMessage->to("yuvrajhingerakola@gmail.com");
        //         $emailMessage->cc("");
        //     });
        // }
        // catch(\Exception $e){
        //     return true;
        // }                        
    }

    public function is_valid_mobile_subscriber_email($subscriber_email,$webhook){
        // check already exists or not
        $result = BitrixMobile::where([['email',$subscriber_email],['webhook_url',$webhook],['product_id',5]])->first();
        if($result) return true;
        
        // check email or webhook exist for call tracker
        $result = BitrixMobile::where([['email',$subscriber_email],['product_id',5]])->orWhere([['webhook_url',$webhook],['product_id',5]])->get();
        if(count($result)) return false;
                
        return true;
    }

    public function mobile_validation(Request $request)
    {
        $webhook = $request->webhook;
        $subscriber_email = $request->subscriber_email;
        $agent_email = $request->agent_email;

        $message = array();
        $message['scope'] = false;
        $message['webhook'] = false;
        $message['subscriber_email'] = false;
        $message['agent_email'] = false;        
        $message['agent_id'] = false;        
        
        // webhook is valid or not
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $webhook."scope",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=2; PHPSESSID=bOXgYGmBZ8Imc3BsqvV4y1SKSIa0a3SC'
            ),
        ));

        $response = curl_exec($curl);        
        curl_close($curl);        
        $response = json_decode($response,true);
        
        if(isset($response['result'])){
            if(in_array('crm',$response['result']) && in_array('user',$response['result']) && in_array('telephony',$response['result'])){
                $message['scope'] = true;
            }
        }
        if($message['scope']==false){            
            return $message;
        }



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $webhook."user.get",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=2; PHPSESSID=2C8xO97PAeiGrGcPQHcXnAZib4vYqV7n'
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response,true);
        curl_close($curl);
        
        
        if(isset($response['result'])){
            $message['webhook'] = true;
            foreach($response['result'] as $agents){
                if($agents['EMAIL']==$agent_email){
                    $message['agent_email'] = true;
                    $message['agent_id'] = $agents['ID'];
                }
            }            
        }
        if($message['agent_email']==true){
            if($this->is_valid_mobile_subscriber_email($subscriber_email,$webhook)){
                $message['subscriber_email'] = true;
            }
        }
        return $message;
    }

    public function presubscription_check($email,$product_id)
    {        
        $user = $this->is_user_exists($email);                 
        if($user){
            $outputs = DB::table('subscriptions')->where([['user_id',$user->id],['status','active']])->get('plan_id');
            foreach($outputs as $output){
                $result = DB::table('plans')->where('id',$output->plan_id)->first();
                if($result->product_id==$product_id){
                    return false;            
                }
            }
        }        
        return true;
    }

}