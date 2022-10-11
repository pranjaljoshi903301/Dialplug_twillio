<?php

namespace Corals\Modules\BM\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\BM\Services\BitrixMobileService;
use Corals\Modules\BM\Transformers\API\BitrixMobilePresenter;

use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\Subscriptions\Models\Subscription as Subscriptions;

use Corals\Modules\Subscriptions\Classes\Subscription;
use Corals\Modules\Subscriptions\Services\SubscriptionService;
use Corals\Modules\Subscriptions\Http\Requests\API\SubscriptionCheckoutRequest;
use Corals\Modules\Subscriptions\Http\Controllers\API\SubscriptionsController;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


use Corals\User\Models\User;
use Corals\User\Services\UserService;
use Corals\User\Http\Requests\UserRequest;
use Corals\User\Http\Controllers\API\UsersController;


class PluginController extends APIBaseController
{
    protected $bitrixmobileService;
    protected $PLUGIN_KEY = 'yJRovJMwGB7sf8Pn5S0VnMSr2nGA0KdyoiIl1Lb0';
    /**
     * BitrixMobileController constructor.
     * @param BitrixMobileService $bitrixmobileService
     * @throws \Exception
     */
    public function __construct(BitrixMobileService $bitrixmobileService)
    {        
        
    }
    
    public function register_user($email,$phone_number)
    {
        $name = explode('@',$email);
        $password = md5(rand(1000,9999));
        $user_obj = array(
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'name' => isset($name[0])?$name[0]:'NULL',
            'last_name' => isset($name[1])?$name[1]:'NULL',
            'roles' => '2',
            'phone_country_code' => '+',
            'phone_number' => $phone_number,
            'confirmed' => 'true'            
        );
        $user = new UsersController(new UserService);
        $response = $user->store(new UserRequest($user_obj));
        $response = json_decode(json_encode($response),true);

        // send mail to user 
        if($response['original']['status']=='success')
        {
            // $maildata = array(
            //     'name'=>$name[0],
            //     'email'=>$email,
            //     'password'=>$password,
            //     'domain'=>'https://dialplug.com'
            // );
            // Mail::send('email.autoregister_details', $maildata, function ($emailMessage) use ($maildata, $email) {
            //     $emailMessage->subject('Dialplug Registeration Details');
            //     $emailMessage->to($email);
            //     $emailMessage->cc("service-desk@dialplug.com");
            // });
        }
        return $response['original'];
    }

    public function subscribe_user($user,$plan)
    {                
        $subscription_data = array(            
            "gateway"=>"Cash",
            "billing_address"=>array(
                "address_1"=>"address_1",
                "city"=>"city",
                "state"=>"state",
                "country"=> "country",
                "zip"=>"zip"
            ),
            "integration_id"=>"integration_id",
            "subscription_reference"=>"unique_reference"            
        );              
        
        Auth::login($user);
        
        $subscription = new SubscriptionsController(new SubscriptionService);
        $response = $subscription->subscribe(new SubscriptionCheckoutRequest($subscription_data), $plan);                
        $response = json_decode(json_encode($response),true);

        if($response['original']['status']=='success')
        {
            $email = $user->email;
            $maildata = array(                
                'email'=>$email,                
                'plan_name' => $plan->id
            );
            try{
                Mail::send('email.PreSubscriptionMail', $maildata, function ($emailMessage) use ($maildata, $email) {
                    $emailMessage->subject('Dialplug Free Trial Subscription Mail');
                    $emailMessage->to($email);
                    $emailMessage->cc("service-desk@dialplug.com");
                });
            }
            catch(\Exception $e){
            }
        }

        return $response['original'];
    }

    public function unique_id_data(Request $request)
    {
        $response = array(
            'is_valid' => false,
            'data'=> false,
            'message'=>false
        );

        if($this->key_validation($request))
        {                            

            $unique_id = $request->unique_id;
            $plan_id = $request->plan_id;
            $exist = BitrixMobile::where([['unique_id',$unique_id],['plan_id',$plan_id]])->first();
            if($exist)
            {
                $plan = Plan::find($plan_id);
                $free_trial = $plan->trial_period;
                
                $user = User::where('email',$exist->email)->first();            
                $user_id = $user->id;
                $subscription = Subscriptions::where([['plan_id',$plan_id],['user_id',$user_id]])->first();    
                if($subscription)
                {
                    if($this->subscription_validation($subscription->created_at,$free_trial,$subscription->status))                
                    {
                        $response['is_valid']=true;
                        $response['data']=array(
                            'email'=>$exist->email,
                            'webhook'=>$exist->webhook_url,
                            'unique_id'=>$exist->unique_id,
                            'created_at'=>$exist->created_at
                        );
                    }                
                    else{
                        $response['message']='Subscription Expired';
                    }
                }
                else
                {
                    $response['message']='Subscription Not Found';
                }
                
            }
            else
            {            
                $response['message']='Invalid Unique Id';            
            }        
            return response($response, 200);
        }
        else
        {
            return response($response, 403);
        }

    }

    public function user_validation(Request $request)
    {
        $response = array(
            'is_valid' => false,
            'unique_id'=> false,
            'message'=>false
        );

        if($this->key_validation($request))
        {                            
            $email = $request->user_email_id;
            $phone_number = $request->user_phone_number;
            $webhook = $request->webhook_url;
            $plan_id = $request->plan_id;

            if(substr($webhook, -1)=='/')
            {
                $webhook = substr($webhook, 0, -1);
                $request->webhook_url = $webhook;
            }
            
            $domain = $this->extractDomain($webhook);
                        
            $webhook_exist = BitrixMobile::where([['domain',$domain],['plan_id',$plan_id]])->first();
            if($webhook_exist)
            {
                if($email!=$webhook_exist->email)
                {
                    $response['message'] = 'Invalid Webhook';
                    return $response;
                }                
            }   
                        
            $user = User::where('email',$email)->first();

            $plan = Plan::find($plan_id);
            if(!$plan)
            {
                $response['message'] = 'Invalid Plan';
                return $response;
            }
            
            if($user)
            {                
                $free_trial = isset($plan->trial_period)?$plan->trial_period:'0';
                $user_id = $user->id;
                $subscription = Subscriptions::where([['plan_id',$plan_id],['user_id',$user_id]])->first();
                
                if($subscription)
                {
                    if($this->subscription_validation($subscription->created_at,$free_trial,$subscription->status))
                    {
                        $exist = BitrixMobile::where([['email',$user->email],['plan_id',$plan_id]])->first();
                        
                        if($exist)
                        {
                            if($exist->webhook_url==$webhook)
                            {
                                $response['is_valid']=true;
                                $response['unique_id']=$exist->unique_id;
                            }                        
                            else
                            {                                
                                $response['message']="Invalid Webhook";
                            }
                        }
                        else
                        {
                            $bm_config = new BitrixMobile();
                            $bm_config->domain = $this->extractDomain($webhook);
                            $bm_config->email = $email;
                            $bm_config->plan_id = $plan_id;
                            $bm_config->webhook_url = $webhook;                        
                            $bm_config->product_id = $plan->product_id;                        
                            $bm_config->unique_id = $this->generate_unique_id();                        
                            $bm_config->save();
        
                            $response['unique_id'] = $bm_config->unique_id;
                            $response['is_valid']=true;
                        }
                    }                    
                    else
                    {
                        $response['is_valid']=false;
                        $response['message'] = "Subscription Expired";                                                    
                    }
                }                
                else
                {
                    $subscription_response = $this->subscribe_user($user,$plan);    
                    $response['message'] = $subscription_response['message'];
                    if($subscription_response['status']=='success')
                    {
                        $exist = BitrixMobile::where([['email',$user->email],['plan_id',$plan_id]])->first();
                        if($exist)
                        {
                            if($exist->webhook_url==$webhook)
                            {
                                $response['is_valid']=true;
                                $response['unique_id']=$exist->unique_id;
                            }                        
                            else
                            {                                
                                $response['message']="Invalid Webhook";
                            }
                        }
                        else
                        {
                            $bm_config = new BitrixMobile();
                            $bm_config->domain = $this->extractDomain($webhook);
                            $bm_config->email = $email;
                            $bm_config->plan_id = $plan_id;
                            $bm_config->webhook_url = $webhook;                        
                            $bm_config->product_id = $plan->product_id;                        
                            $bm_config->unique_id = $this->generate_unique_id();                        
                            $bm_config->save();        
                            $response['unique_id'] = $bm_config->unique_id;
                            $response['is_valid']=true;
                        }                        
                    }
                }
            }
            else
            {
                $phone_exist = User::where('phone_number',$phone_number)->first();
                if($phone_exist)
                {
                    $response['message'] = "Phone Number Already Exists.";
                }                    
                else
                {
                    $user_response = $this->register_user($email,$phone_number);
                    $response['message'] = $user_response['message'];
                    if($user_response['status']=='success')
                    {
                        $user_id = $user_response['data']['id'];
                        $user = User::find($user_id);
                        $subscription_response = $this->subscribe_user($user,$plan);
                        $response['message'] = $subscription_response['message'];
                        if($subscription_response['status']=='success')
                        {
                            $exist = BitrixMobile::where([['email',$user->email],['plan_id',$plan_id]])->first();
                            if($exist)
                            {
                                if($exist->webhook_url==$webhook)
                                {
                                    $response['is_valid']=true;
                                    $response['unique_id']=$exist->unique_id;
                                }                        
                                else
                                {                                
                                    $response['message']="Invalid Webhook";
                                }
                            }
                            else
                            {
                                $bm_config = new BitrixMobile();
                                $bm_config->domain = $this->extractDomain($webhook);
                                $bm_config->email = $email;
                                $bm_config->plan_id = $plan_id;
                                $bm_config->webhook_url = $webhook;                        
                                $bm_config->product_id = $plan->product_id;                        
                                $bm_config->unique_id = $this->generate_unique_id();                        
                                $bm_config->save();        
                                $response['unique_id'] = $bm_config->unique_id;
                                $response['is_valid']=true;
                            }                                                
                        }
                    }
                }            
            }

            return response($response, 200);
        }
        else
        {
            $response['message'] = 'Invalid Key';
            return response($response, 403);
        }
    }

    public function key_validation(Request $request)
    {
        if ($request->hasHeader('X-API-KEY')){
            $value = $request->header('X-API-KEY');
            if($value==$this->PLUGIN_KEY)
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

    public function generate_unique_id()
    {
        $flag = true;
        $unique_key = false;
        while($flag)
        {
            $unique_key = implode( '-', str_split( substr( strtoupper( md5( time() . rand( 1000, 9999 ) ) ), 0, 20 ), 4 ) );
            $bm_config = BitrixMobile::where('unique_id',$unique_key)->first();
            if(!$bm_config) $flag=false;
        }
        return $unique_key;        
    }

    public function subscription_validation($order_time,$free_trial,$subscription_status)
    {
        $current_time = \Carbon\Carbon::now();
        $order_time =  \Carbon\Carbon::parse($order_time);        
        $time_interval = $order_time->diffInDays($current_time,false);
        if($subscription_status=="active" || $time_interval<=$free_trial)
        {            
            return true;
        }        
        return false;
    }    

    public function test(Request $request)
    {                       
    }

} 