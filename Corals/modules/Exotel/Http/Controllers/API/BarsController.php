<?php

namespace Corals\Modules\Exotel\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Exotel\DataTables\BarsDataTable;
use Corals\Modules\Exotel\Http\Requests\BarRequest;
use Corals\Modules\Exotel\Models\Bar as ExotelDetails;
use Corals\Modules\Exotel\Models\AgentDetail;
use Corals\Modules\Exotel\Models\UsageDetail;
use Corals\Modules\Exotel\Services\BarService;
use Corals\Modules\Exotel\Transformers\API\BarPresenter;
use DB;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use Corals\User\Models\User;
use Corals\User\Services\UserService;
use Corals\User\Http\Requests\UserRequest;
use Corals\User\Http\Controllers\API\UsersController;

use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\Subscriptions\Models\Subscription as Subscriptions;
use Corals\Modules\Subscriptions\Classes\Subscription;
use Corals\Modules\Subscriptions\Services\SubscriptionService;
use Corals\Modules\Subscriptions\Http\Requests\API\SubscriptionCheckoutRequest;
use Corals\Modules\Subscriptions\Http\Controllers\API\SubscriptionsController;

class BarsController extends APIBaseController
{
    protected $barService;
    protected $plan_id=26;
    protected $client_id="local.633e8b418834d7.41916983";
    protected $client_secret="2Kg8Uikwm5jp5xybmxzw1IgvoVmBXlu8cHgIvPxdMp6SkCYiZ1";
    // protected $client_id="local.63073e60227d09.42836167";
    // protected $client_secret="KKeq4LhLaVH414rAR3MwDpvSpm2GsbvYFVhVzHDPcerjCYbnb8";
    /**
     * BarsController constructor.
     * @param BarService $barService
     * @throws \Exception
     */
    public function __construct(BarService $barService)
    {
        $this->barService = $barService;
        $this->barService->setPresenter(new BarPresenter());

        // parent::__construct();
    }

    /**
     * @param BarRequest $request
     * @param BarsDataTable $dataTable
     * @return mixed
     * @throws \Exception
     */
    public function index(BarRequest $request, BarsDataTable $dataTable)
    {
        $bars = $dataTable->query(new Bar());

        return $this->barService->index($bars, $dataTable);
    }

    /**
     * @param BarRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BarRequest $request)
    {
        try {
            $bar = $this->barService->store($request, Bar::class);
            return apiResponse($this->barService->getModelDetails(), trans('Corals::messages.success.created', ['item' => $bar->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param BarRequest $request
     * @param Bar $bar
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BarRequest $request, Bar $bar)
    {
        try {
            return apiResponse($this->barService->getModelDetails($bar));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param BarRequest $request
     * @param Bar $bar
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BarRequest $request, Bar $bar)
    {
        try {
            $this->barService->update($request, $bar);

            return apiResponse($this->barService->getModelDetails(), trans('Corals::messages.success.updated', ['item' => $bar->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param BarRequest $request
     * @param Bar $bar
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BarRequest $request, Bar $bar)
    {
        try {
            $this->barService->destroy($request, $bar);

            return apiResponse([], trans('Corals::messages.success.deleted', ['item' => $bar->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }


    // exotel click to call methods
    
    /*  User Interface Methods
        1. exotel_view_index            =>  default call + general view
        2. store_details                =>  default call
        3. store_exotel_details         =>  store general detail
        4. exotel_agent_view            =>  agent view
        5. store_exotel_agent_details   =>  store agent detail
        6. exotel_default_view          =>  common view
        7. validate_webhook             =>  bitrix_api
        8. get_exophones                =>  exotel_api
        9. bitrix_agents                =>  bitrix_api
    */

    public function exotel_view_index()
    {           

        $this->exotel_logs("exoteltest",$_REQUEST);

        if(isset($_REQUEST['DOMAIN']))
        {            
            
            $bitrix_domain = $_REQUEST['DOMAIN'];
            $access_token = $_REQUEST['AUTH_ID'];
            $refresh_token = $_REQUEST['REFRESH_ID'];

            // $this->bind_telephony_event($bitrix_domain,$access_token);

            // event: get general details if already exist
            $exotel_detail = ExotelDetails::where('bitrix_domain',$_REQUEST['DOMAIN'])->first(); 

            if($exotel_detail)
            {

                $is_valid = $this->check_admin_privilage($bitrix_domain,$access_token);
                if($is_valid==false)
                {
                    return "Bitrix24 Administrator Account Required to Install this Application.!";
                }

                $exotel_detail->access_token = $access_token;                
                $exotel_detail->refresh_token = $refresh_token;                
                $exotel_detail->save();                
                
                if($exotel_detail->exotel_sid)
                {
                    // event: get agents details if already exist
                    $exotel_agents = AgentDetail::where([['status','0'],['subscriber_id',$exotel_detail->id]])->get();                 
                    if(count($exotel_agents))
                        return $this->exotel_default_view($exotel_detail,$exotel_agents);
                    return $this->exotel_agent_view($exotel_detail,$exotel_agents);
                }
                return view('Exotel::exotel.home',['exotel_detail'=>$exotel_detail]);
            }
            else
            {                
                $is_valid = $this->check_admin_privilage($bitrix_domain,$access_token);
                if($is_valid==false)
                {
                    return "Bitrix24 Administrator Account Required to Install this Application.!";
                }
                
                $response = $this->get_access_token($refresh_token);
                if($response['is_valid']==false)
                {
                    return json_encode($response);
                }
                $access_token = $response['access_token'];
                $refresh_token = $response['refresh_token'];
                $subscriber_email = $this->get_subscriber_email($bitrix_domain,$access_token);
                if($subscriber_email==false)
                {
                    return "Fetching Account Email Error.!";
                }

                $exotel_detail = new ExotelDetails();
                $exotel_detail->subscriber_email = $subscriber_email;
                $exotel_detail->bitrix_domain = $bitrix_domain;                
                $exotel_detail->access_token = $access_token;                
                $exotel_detail->refresh_token = $refresh_token;                
                $exotel_detail->save();                

                // register user and subscribe to exotel                
                $user = User::where('email',$exotel_detail->subscriber_email)->first();
                if(!$user){
                    $user_response = $this->register_user($exotel_detail->subscriber_email,'0123456789');
                    $user_id = $user_response['data']['id'];
                    $user = User::find($user_id);
                }
                $plan_id = $this->plan_id;
                $plan = Plan::find($plan_id);
                $subscription_response = $this->subscribe_user($user,$plan);                

                return view('Exotel::exotel.home',['exotel_detail'=>$exotel_detail]);
            }            
        }    
        return "Method not Allowed";                
    }
    public function bind_telephony_event($bitrix_domain,$access_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://'.$bitrix_domain.'/rest/event.bind.json?auth='.$access_token.'&event=ONEXTERNALCALLSTART&handler=https://dialplug.com/api/v1/exotel/click-to-call',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Cookie: BITRIX_SM_SALE_UID=3'
          ),
        ));
        
        $response = curl_exec($curl);

        $this->exotel_logs("exotel"," Bitrix: ".$bitrix_domain."  \n Bind response:".$response);
        
        curl_close($curl);
        
    }
    public function check_admin_privilage($bitrix_domain,$access_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://'.$bitrix_domain.'/rest/user.admin.json?auth='.$access_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=3; PHPSESSID=J7CvNR4vgyPwrqj7QpEzr0bcEO0c2k8N'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response,true);        
        $is_valid = false;
        if(isset($response['result']))
        {
            if($response['result']) $is_valid = true;
        }            
        return $is_valid;

    }

    public function store_details(Request $request)
    {                        
        if($request->has('store_exotel_details'))
        {
            return $this->store_exotel_details($request);
        }
        elseif($request->has('store_exotel_agent_details'))
        {
            return $this->store_exotel_agent_details($request);
        }        
        else if($request->has('set_session'))
        {            
            $exotel_details = ExotelDetails::find($request->subscriber_id);
            $exotel_details->last_step_detail = $request->set_session;
            $exotel_details->save();
            return true;
        }                
                    
    }

    public function store_exotel_details(Request $request)
    {        
        $exotel_obj['sid'] =  $request->exotel_sid;
        $exotel_obj['api_key'] =  $request->exotel_api_key;
        $exotel_obj['api_token'] =  $request->exotel_api_token;
        if(!$this->get_exophones($exotel_obj))
        {
            $msg = "Exotel";            
            return $msg;            
        }
        
        $exotel_detail = ExotelDetails::find($request->subscriber_id);                                    
        $exotel_detail->exotel_sid = $request->exotel_sid;
        $exotel_detail->exotel_api_key = $request->exotel_api_key;
        $exotel_detail->exotel_api_token = $request->exotel_api_token;
        $exotel_detail->save(); 
        
        return true;            
    }

    public function exotel_agent_view($result,$exotel_agents)
    {    
        // get all bitrix agents (extensions)        

        $bitrix_agents = $this->bitrix_agents($result->bitrix_domain,$result->access_token);                
        if(!$bitrix_agents)
            return "                
                    <div class='container'>
                        <br>
                        <h4>No Bitrix24 User(with extension number) found.!</h4>
                        <br>Please provide extension number to user and try again.! <br>
                        <a href='https://helpdesk.bitrix24.com/open/9245679/' target='_blank'>click here</a> to see <b>How to configure extension numbers</b>
                    </div>
                ";
        
        // map bitrix agent with exotel agent 
        $all_agents=[];
        $exotelAgents = $this->exotel_users_details($result);        
        foreach($bitrix_agents as $bitrix_agent)
        {
            foreach($exotel_agents as $exotel_agent)
            {
                if($exotel_agent->agent_email==$bitrix_agent['EMAIL'])
                {
                    $bitrix_agent["EXOPHONE"] = $exotel_agent->exophone;
                    $bitrix_agent["agent_phone"] = $exotel_agent->agent_phone;
                    $bitrix_agent["status"] = "1";
                    break;
                }
            }      
            if(isset($exotelAgents[$bitrix_agent['EMAIL']])){
                $bitrix_agent["agent_phone"]=$exotelAgents[$bitrix_agent['EMAIL']];
                $all_agents[] = $bitrix_agent;
            }            
        }            

        $exotel_obj['sid'] =  $result->exotel_sid;
        $exotel_obj['api_key'] =  $result->exotel_api_key;
        $exotel_obj['api_token'] =  $result->exotel_api_token;
        
        $exophones = $this->get_exophones($exotel_obj);
        
        if(!$exophones)
            return view('Exotel::exotel.home',['exotel_detail'=>$result]);

        // Agent Homepage
        return view('Exotel::exotel.agent',['bitrix_domain'=>$result->bitrix_domain,'bitrix_agents'=>$all_agents,'subscriber_id'=>$result->id,'exophones'=>$exophones]);
    }

    public function store_exotel_agent_details(Request $request)
    {
        $agent_detail = AgentDetail::where([['agent_email',$request->agent_email],['subscriber_id',$request->subscriber_id]])->first();
        if(!$agent_detail)
        {
            $agent_detail = new AgentDetail();            
            $agent_detail->subscriber_id = $request->subscriber_id;
            $agent_detail->agent_email = $request->agent_email;
            $agent_detail->agent_bitrix_id = $request->agent_id;
        }
        else
            $agent_detail = AgentDetail::find($agent_detail->id);            
        
        $agent_detail->exophone = $request->exophone;
        $agent_detail->agent_phone = $request->agent_phone; 
        $agent_detail->save(); /*$query_data = "UPDATE exotel_db.exotel_agent_details SET exophone='$request->exophone',agent_phone='$request->agent_phone' WHERE  id='$agent_id'";*/     /*$query_data = "INSERT INTO exotel_db.exotel_agent_details (subscriber_id,agent_email,agent_phone,exophone,agent_bitrix_id) VALUES ('$request->subscriber_id','$request->agent_email','$request->agent_phone','$request->exophone','$request->agent_id')";*/            
        
        return true;
    }

    public function exotel_default_view($result,$exotel_agents)
    {        
        $exotel_detail = ExotelDetails::find($result->id);
        $exotel_detail->last_step_detail='-1';
        $exotel_detail->save();
        
        // basic guide detail
        if($result->last_step_detail=='0')
            return view("Exotel::exotel.last_step_detail",['bitrix_domain'=>$exotel_detail->bitrix_domain]);        
        
        // manage exotel details
        else if($result->last_step_detail=='1')
            return view('Exotel::exotel.home',['exotel_detail'=>$exotel_detail]);
        
        // manage agent details
        else if($result->last_step_detail=='2'){                        
            $exotel_agents = AgentDetail::where([['status','0'],['subscriber_id',$exotel_detail->id]])->get();

            $response = $this->get_access_token($exotel_detail->refresh_token);
            if($response['is_valid']==false)
            {
                return json_encode($response);
            }
            $access_token = $response['access_token'];
            $refresh_token = $response['refresh_token'];
            $exotel_detail->access_token = $access_token;                
            $exotel_detail->refresh_token = $refresh_token;                
            $exotel_detail->save();
            return $this->exotel_agent_view($exotel_detail,$exotel_agents);
        }

        $subscription_status = $this->exotel_subscription_validation($exotel_detail->subscriber_email);            
        return view("Exotel::exotel.exotel_default_view",['bitrix_domain'=>$exotel_detail->bitrix_domain,'subscriber_id'=>$exotel_detail->id,'subscription_status'=>$subscription_status]);
    }

    public function validate_webhook($bitrix_domain,$access_token)
    {
        try
        {                   
            $users_list = file_get_contents(
                'https://'.$bitrix_domain."/rest/user.get?auth=".$access_token,
                false,
                stream_context_create([
                    'http' => [
                        'ignore_errors' => true,
                        'method' => 'GET',
                        'timeout' => 10,
                    ],
                ])
            );                    
            $this->exotel_logs('exotel',"Bitrix: ".$bitrix_domain."\n User List Response: ".json_encode($users_list));
            $users_list = json_decode($users_list);            
            if(isset($users_list->error)) return false;
            return $users_list;
        }
        catch(\Exception $e)
        {            
            return false;
        }        
    }

    public function get_exophones($exotel_obj)
    {                    
        try
        {
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://'.$exotel_obj['sid'].':'.$exotel_obj['api_token'].'@api.exotel.com/v2_beta/Accounts/'.$exotel_obj['sid'].'/IncomingPhoneNumbers',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                curl_setopt($curl, CURLOPT_USERPWD, $exotel_obj['api_key'].':'.$exotel_obj['api_token'])            
            ));

            $response = curl_exec($curl);

            curl_close($curl);                

            $exophones = json_decode($response,true);
            $exophones = $exophones['incoming_phone_numbers'];
            $phones=[];
            foreach ($exophones as $exophone) 
                $phones[] = $exophone['phone_number'];
            return $phones;
        }
        catch(\Exception $e)
        {            
            return false;
        }                
    }    

    public function bitrix_agents($bitrix_domain,$access_token)
    {
        try{            
            $users_list = $this->validate_webhook($bitrix_domain,$access_token);            
            $userlist = [];
            foreach ($users_list->result as $user) {
                if ($user->UF_PHONE_INNER) {
                    $users["ID"] = $user->ID;
                    $users["EMAIL"] = $user->EMAIL;
                    $users["EXTENSION"] = $user->UF_PHONE_INNER;
                    $users["EXOPHONE"] = "";
                    $users["agent_phone"] = "";
                    $users["status"] = "0";
                    $userlist[] = $users;
                }
            }             
            return $userlist;
        }
        catch(\Exception $e){            
            return false;
        }        
    }

    /* User Interface Methods  [9] */

    /* Click to Call Methods  [9]
        1. click_to_call            => outgoing
        2. exotel_connect_api       => exotel_api
        3. callback_url             => outgoing callback
        4. bitrix_finish_api        => close bitrix24 card [outgoing/incoming]
        5. incoming_click_to_call   => incoming 
        6. bitrix_register_api      => show bitrix24 card
        7. bitrix_execution         => bitrix_api
        8. exotel_call_logs_api     => exotel_api
        9. upload_recording         => bitrix_api
    */

    
    /* outgoing calls */
    public function click_to_call()
    {                        
        $api_response = array(
            "code" => 400,
            "message" => false,
            "response" => array(
                "is_valid" => false,
                "message" => false,
                "caller_id"=>false,
                "from"=>false,
                "to"=>false,  
            )                      
        );        

        $this->exotel_logs('exoteldebug',$api_response);

        if(isset($_REQUEST['event']))
        {
            $data = $_REQUEST['data'];            

            // get exotel detail from bitrix domain
            $result = ExotelDetails::where('bitrix_domain',$_REQUEST['auth']['domain'])->first();
            if(!$result){
                $api_response['message'] = "Domain not found. Please contact support.!";
                return response($api_response, 400);
            }

            $response = $this->get_access_token($result->refresh_token);
            if($response['is_valid']==false)
            {
                return json_encode($response);
            }
            $access_token = $response['access_token'];
            $refresh_token = $response['refresh_token'];
            $result->access_token = $access_token;                
            $result->refresh_token = $refresh_token;                
            $result->save();
            
            // check subscription validation
            if(!$this->exotel_subscription_validation($result->subscriber_email)){
                $api_response['message'] = "Subscription validation failed. <a href='https://dialplug.com/contact-us' target='_blank'>Please contact support.</a>";
                $this->b24notify($result,$api_response['message'],$api_response);
                return response($api_response, 400);   
            }

            // get user email
            $agent_detail = AgentDetail::where([['agent_bitrix_id',$data['USER_ID']],['subscriber_id',$result->id]])->first();
            if(!$agent_detail){
                $api_response['message'] = "Agent with user id:".$data['USER_ID']." is not registered yet. Please register agent details first.!";
                $this->b24notify($result,$api_response['message'],$api_response);
                return response($api_response, 400);
            }

            $filter = '&email='.$agent_detail->agent_email;
            $exotelAgent = $this->exotel_users_details($result,$filter);
            if(!isset($exotelAgent[$agent_detail->agent_email])){
                $api_response['message'] = "Agent with user id:".$data['USER_ID']." is not found as exotel co-worker. Please register agent details first.!";
                $this->b24notify($result,$api_response['message'],$api_response);
                return response($api_response, 400);
            }            
            
            $exotel_obj['sid'] =  $result->exotel_sid;
            $exotel_obj['api_key'] =  $result->exotel_api_key;
            $exotel_obj['api_token'] =  $result->exotel_api_token;
            
            $exotel_obj['from'] =  $exotelAgent[$agent_detail->agent_email];
            $exotel_obj['to'] =  $data['PHONE_NUMBER'];
            $exotel_obj['callerid'] =  $agent_detail->exophone;
            $exotel_obj['bitrix_call_id'] =  $data['CALL_ID'];
            $exotel_obj['bitrix_user_id'] =  $data['USER_ID'];
            $exotel_obj['bitrix_domain'] =  $result->bitrix_domain;

             // sid,api_key,api_token,from,to,callerid,bitrix_caller_id,bitrix_user_id
            $status_code = $this->exotel_connect_api($exotel_obj);
            $api_response['code'] = $status_code;
            if($status_code==200){                
                $api_response['response']['is_valid'] = true;                
                $api_response['message'] = "Successfully Call Triggered";                
            }                      
            $api_response['response']['caller_id'] = $exotel_obj['callerid'];
            $api_response['response']['from'] = $exotel_obj['from'];
            $api_response['response']['to'] = $exotel_obj['to'];
            return response($api_response, $status_code);
        }  
        else{
            $api_response['message'] = 'Invalid Request. Please contact support.!';
            return response($api_response, 400);             
        }
    }
    public function exotel_connect_api($exotel_obj)
    {            
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://'.$exotel_obj['sid'].':'.$exotel_obj['api_token'].'@api.exotel.com/v1/Accounts/'.$exotel_obj['sid'].'/Calls/connect',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'From' => $exotel_obj['from'],
                'To' => $exotel_obj['to'],
                'CallerId' => $exotel_obj['callerid'],                            
                'StatusCallback' => 'https://dialplug.com/api/v1/exotel/callback-url?bitrix_domain='.$exotel_obj['bitrix_domain'].'&bitrix_call_id='.$exotel_obj['bitrix_call_id'].'&bitrix_user_id='.$exotel_obj['bitrix_user_id'].'&call_start_at='.urlencode(date("Y-m-d H:i:s"))
            ),
            curl_setopt($curl, CURLOPT_USERPWD, $exotel_obj['api_key'].':'.$exotel_obj['api_token'])            
        ));

        $response = curl_exec($curl);
        $status_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        curl_close($curl);        
        
        /* check response and notify user if error */        
        // $this->exotel_logs('exotel',"Bitrix: ".$exotel_obj['bitrix_domain']."\nExotel ClicktoCall Error Output: ".$response);
        if($status_code!=200){
            $this->exotel_logs('exotel',"Bitrix: ".$exotel_obj['bitrix_domain']."\nExotel ClicktoCall Error Output: ".$response);
        }
        return $status_code;
    }
    public function callback_url()
    {                        
        if(!isset($_REQUEST['bitrix_domain'])) return;
        $result = ExotelDetails::where('bitrix_domain',$_REQUEST['bitrix_domain'])->first();
        if(!$result) return;        
                            
        $response = $this->get_access_token($result->refresh_token);
        if($response['is_valid']==false){
            $this->exotel_logs('exotel','Outgoing CallBack Access Token Error Output: '.json_encode($response));
            return;
        }
        $access_token = $response['access_token'];
        $refresh_token = $response['refresh_token'];
        $result->access_token = $access_token;                
        $result->refresh_token = $refresh_token;                
        $result->save();

        $row['duration'] = strtotime(date('Y-m-d H:i:s')) - strtotime(urldecode($_REQUEST['call_start_at']));
        $row['user_id'] = $_REQUEST['bitrix_user_id'];
        $row['call_id'] = $_REQUEST['bitrix_call_id'];        
        $row['disposition'] = $_REQUEST['Status'];
        $row['bitrix_domain'] = $result->bitrix_domain;                
        $row['access_token'] = $access_token;                
        $row['recordingurl'] = "";
        if(isset($_REQUEST['RecordingUrl'])) $row['recordingurl'] = $_REQUEST['RecordingUrl'];
        $this->bitrix_finish_api($row);

        //usage monitoring
        $this->usage_monitoring($_REQUEST['bitrix_domain'],$_REQUEST['bitrix_user_id'],$row['duration'],$_REQUEST['Status'],'outbound');

    }
    public function bitrix_finish_api($row)
    {                
        $finishResult = $this->bitrix_execution($row['bitrix_domain'],$row['access_token'], "POST", "telephony.externalCall.finish", array(
            "USER_ID" => $row['user_id'],
            "CALL_ID" => $row['call_id'],
            "DURATION" => intval($row['duration']),
            "STATUS_CODE" => ($row['disposition'] != "completed") ? "603-S" : "200",
        ));

        if ($row['disposition'] == "completed" && $row['recordingurl']!="") 
        {            
            $attachResult = $this->bitrix_execution($row['bitrix_domain'],$row['access_token'], "POST", "telephony.externalCall.attachRecord", array(
                "CALL_ID" => $row['call_id'],
                "FILENAME" => $row['recordingurl'],
                "RECORD_URL" => $row['recordingurl'],
            ));            
        }
        
    }
    /* incoming calls */
    public function incoming_click_to_call()
    {
        
        // $this->exotel_logs("data_check",json_encode($_REQUEST));

        if(!isset($_REQUEST['bitrix_domain'])) return;
        $result = ExotelDetails::where('bitrix_domain',$_REQUEST['bitrix_domain'])->first();
        if(!$result) return;

        // check subscription validation
        if(!$this->exotel_subscription_validation($result->subscriber_email)) return;
            
        $subscriber_id = $result->id;
        $row['bitrix_domain'] = $result->bitrix_domain;                
        $row['CallSid'] = $_REQUEST['CallSid'];
        $row['disposition'] = "failed"; // default
        
        $response = $this->get_access_token($result->refresh_token);
        if($response['is_valid']==false){
            $this->exotel_logs('exotel','Incoming Call Access Token Error Output: '.json_encode($response));
            return;
        }
        $access_token = $response['access_token'];
        $refresh_token = $response['refresh_token'];
        $result->access_token = $access_token;                
        $result->refresh_token = $refresh_token;
        $result->save();

        $row['access_token'] = $access_token;

        $event_type = $_REQUEST['EventType'];
        if($event_type == "Dial")
        {            
            $row['agent_email'] = $_REQUEST['AgentEmail'];
            $row['CurrentTime'] = $_REQUEST['CurrentTime'];
            $row['CallFrom'] = $_REQUEST['CallFrom'];                        
            $result = $this->bitrix_register_api($row);            

            /* check result and notify user if error */
            // $this->exotel_logs("data_check","--------call dial---------\n".json_encode($result));
            
            // store bitrix_call_id and bitrix_user_id and CallSid and subscriber_id
            $result = DB::select("insert into exotel_db.track_incoming_record(bitrix_call_id,bitrix_user_id,exotel_callsid,call_start_at,subscriber_id) values('".$result['bitrix_call_id']."','".$result['bitrix_user_id']."','".$row['CallSid']."','".$row['CurrentTime']."','".$subscriber_id."')");
        }        
        elseif($event_type == "Terminal")
        {                                    
            if(isset($_REQUEST['RecordingAvailableBy']))
                $row['disposition'] = "completed";            

            // get bitrix_call_id and bitrix_user_id from CallSid
            $result = DB::select("select * from exotel_db.track_incoming_record where exotel_callsid='".$row['CallSid']."' order by created_at desc");
            
            if(count($result))
            {                
                $result = json_decode(json_encode($result), true);        
                $bitrix_call_id = $result[0]['bitrix_call_id'];
                $bitrix_user_id = $result[0]['bitrix_user_id'];
                $created_at = strtotime($result[0]['call_start_at']);
                $updated_at = strtotime($_REQUEST['CurrentTime']);

                $row['bitrix_user_id'] = $bitrix_user_id;
                $row['bitrix_call_id'] = $bitrix_call_id;
                $row['duration'] = $updated_at - $created_at;            
                 
                
                $finishResult = $this->bitrix_execution($row['bitrix_domain'],$row['access_token'], "POST", "telephony.externalCall.finish", array(
                    "USER_ID" => $row['bitrix_user_id'],
                    "CALL_ID" => $row['bitrix_call_id'],
                    "DURATION" => intval($row['duration']),
                    "STATUS_CODE" => ($row['disposition'] != "completed") ? "603-S" : "200",
                ));                

                if(isset($_REQUEST['RecordingAvailableBy']))
                {
                    $track_id=$result[0]['id'];
                    $result = DB::select("UPDATE exotel_db.track_incoming_record SET recording_status='0' WHERE id='$track_id'");
                }

                //usage monitoring
                $this->usage_monitoring($_REQUEST['bitrix_domain'],$row['bitrix_user_id'],$row['duration'],$row['disposition'],'inbound');

            }            
                                                
        }

    }
    public function bitrix_register_api($row)
    {

        $user_info = $this->bitrix_execution($row['bitrix_domain'],$row['access_token'], "POST", "user.get", array("email" => $row['agent_email']));
                
        if(!isset($user_info->result[0]->ID)) return false;
        $bitrix_user_id = $user_info->result[0]->ID;
        

        $registerResult = $this->bitrix_execution($row['bitrix_domain'],$row['access_token'], "POST", "telephony.externalCall.register", array(
            "USER_PHONE_INNER" => $user_info->result[0]->UF_PHONE_INNER,
            "CALL_START_DATE" => date(DATE_ISO8601, strtotime(date('Y-m-d H:i:s'))),
            "PHONE_NUMBER" => $row['CallFrom'],
            "USER_ID" => $bitrix_user_id,
            "TYPE" => 2,
            "SHOW" => "1",
            "CRM_CREATE" => "1",
        ));
        
        if(!isset($registerResult->result->CALL_ID)) return false;
        $bitrix_call_id = $registerResult->result->CALL_ID;

        $output['bitrix_call_id'] = $bitrix_call_id;
        $output['bitrix_user_id'] = $bitrix_user_id;

        return $output;
    }
    public function bitrix_execution($bitrix_domain,$access_token, $method, $url, $data = null)
    {
        $query_data = "";
        $url = 'https://'.$bitrix_domain.'/rest'.'/'.$url.'?auth='.$access_token;
    
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER => true,
        );
    
        if ($method == "POST") {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
        } elseif (!empty($data)) {
            $url .= strpos($url, "?") > 0 ? "&" : "?";
            $url .= http_build_query($data);
        }

        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOptions);
        $result = curl_exec($curl);
        return (json_decode($result));
    
    }
    public function exotel_call_logs_api($exotel_obj)
    {            
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://'.$exotel_obj['sid'].':'.$exotel_obj['api_token'].'@api.exotel.com/v1/Accounts/'.$exotel_obj['sid'].'/Calls.json?Sid='.$exotel_obj['CallSid'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',            
            curl_setopt($curl, CURLOPT_USERPWD, $exotel_obj['api_key'].':'.$exotel_obj['api_token'])            
        ));

        $response = curl_exec($curl);

        curl_close($curl);        

        $response = json_decode($response,true);
        if(isset($response["Calls"][0]["RecordingUrl"])) return $response["Calls"][0]["RecordingUrl"];
        return false;
    }
    public function upload_recording()
    {        
        // get all callsid whose status is 0 or 1: means require to upload recording
        $call_logs = DB::select("select * from exotel_db.track_incoming_record where recording_status in ('0','1')");
        if(count($call_logs)){
            $call_logs = json_decode(json_encode($call_logs),true);
            foreach($call_logs as $call_log){
                // GET EXOTEL DETAIL     
                $exotel_detail = ExotelDetails::find($call_log['subscriber_id']);
                
                // get call recording url
                $exotel_obj['sid'] = $exotel_detail->exotel_sid;
                $exotel_obj['api_key'] = $exotel_detail->exotel_api_key;
                $exotel_obj['api_token'] = $exotel_detail->exotel_api_token;
                $exotel_obj['CallSid'] = $call_log['exotel_callsid'];
                $recording_url = $this->exotel_call_logs_api($exotel_obj);                
                if($recording_url)
                {
                    
                    $response = $this->get_access_token($exotel_detail->refresh_token);
                    if($response['is_valid']==false){
                        $this->exotel_logs('exotel',' Bitrix: '.$exotel_detail->bitrix_domain."\n Incoming Recording Attach Cron Access Token Error Output: ".json_encode($response));
                        return;
                    }
                    $access_token = $response['access_token'];
                    $refresh_token = $response['refresh_token'];
                    $exotel_detail->access_token = $access_token;                
                    $exotel_detail->refresh_token = $refresh_token;
                    $exotel_detail->save();


                    $attachResult = $this->bitrix_execution($exotel_detail->bitrix_domain,$access_token, "POST", "telephony.externalCall.attachRecord", array(
                        "CALL_ID" => $call_log['bitrix_call_id'],
                        "FILENAME" => $recording_url,                        
                        "RECORD_URL" => $recording_url,
                    ));                     
                    $output = DB::select("UPDATE exotel_db.track_incoming_record SET recording_status='2' where id=".$call_log['id']);
                }                
                else
                {
                    $output = DB::select("UPDATE exotel_db.track_incoming_record SET recording_status=recording_status+1 where id=".$call_log['id']);
                }                                
            }
        }

    }
    /* Click to Call Methods  [9] */

    /* Dev */    
    public function get_subscriber_email($bitrix_domain,$access_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://'.$bitrix_domain.'/rest/user.current?auth='.$access_token,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Cookie: BITRIX_SM_SALE_UID=3; PHPSESSID=3NRtR9MKWP6mTYB2McU3WkU9VsHFKdHd'
          ),
        ));
        
        $response = curl_exec($curl);
        $this->exotel_logs('exotel',"Bitrix: ".$bitrix_domain."\n get current user api response: ".$response);
        curl_close($curl);
        $response = json_decode($response,true);
        return isset($response['result']['EMAIL'])?$response['result']['EMAIL']:false;
        
    }
    public function get_access_token($refresh_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://oauth.bitrix.info/oauth/token/?grant_type=refresh_token&client_id='.$this->client_id.'&client_secret='.$this->client_secret.'&refresh_token='.$refresh_token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $response = json_decode($response,true);
        $response['is_valid'] = false;
        if(isset($response['access_token']) && isset($response['refresh_token']))
            $response['is_valid'] = true;                    
        return $response;
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
            $notify_dialplug = "service-desk@dialplug.com";
            
            $maildata = array(                
                'email'=>$email,                
                'domain'=>'https://dialplug.com'
            );
            try{
                
                /* Debug Purpose */
                // $email = 'yuvraj.hinger@digiclave.com';
                // $notify_dialplug = $email;
                /* Debug Purpose */

                Mail::send('Exotel::exotel.welcome_mail', $maildata, function ($emailMessage) use ($maildata, $email,$notify_dialplug) {
                    $emailMessage->subject('Dialplug Free Trial Subscription Mail');
                    $emailMessage->to($email);
                    $emailMessage->cc($notify_dialplug);
                });
            }
            catch(\Exception $e){
            }
        }
        return $response['original'];
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
    public function exotel_subscription_validation($email)
    {
        $plan_id = $this->plan_id;
        $user = User::where('email',$email)->first();        
        $plan = Plan::find($plan_id);
        if(!$plan || !$user)
        {            
            return false;
        }

        $free_trial = isset($plan->trial_period)?$plan->trial_period:'0';
        $user_id = $user->id;        
        $subscription = Subscriptions::where([['plan_id',$plan_id],['user_id',$user_id],['status','!=','canceled']])->first();            
        if($subscription->status)
        {
            if($this->subscription_validation($subscription->created_at,$free_trial,$subscription->status))                
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function exotel_logs($log_filename,$log){
        $message = '['.date('Y-m-d H-i-s').'] => ';
        $message .= json_encode($log);
        $message .= "\n";
        $dir = storage_path().'/logs/exotel/';
        $path = $dir.$log_filename.".log";
        if(!is_dir($dir)) mkdir($dir);        
        file_put_contents($path,$message,FILE_APPEND);
    }
    public function usage_monitoring($bitrix_domain,$bitrix_user_id,$duration,$disposition,$call_type)
    {
        $usage_details = new UsageDetail();
        $usage_details->bitrix_domain = $bitrix_domain;
        $usage_details->bitrix_user_id = $bitrix_user_id;
        $usage_details->duration = $duration;
        $usage_details->disposition = $disposition;
        $usage_details->call_type = $call_type;
        $usage_details->save();
    }
    public function app_redirect(Request $request)
    {
        return $this->exotel_view_index();
    }

    public function b24notify($result,$message,$log=null)
    {
        if(!empty($log)) $this->exotel_logs('exotel',"Bitrix: ".$result->bitrix_domain."\n LOG: ".print_r($log,true));
        $response = $this->bitrix_execution($result->bitrix_domain,$result->access_token, "GET","user.current");
        $user_id = $response->result->ID;
        if(empty($user_id)) return false;
        $response = $this->bitrix_execution($result->bitrix_domain,$result->access_token, "GET", "im.notify", array(
            "to" => $user_id,
            "message" => $message,
            "type" => 'SYSTEM',
        ));                            
    }

    public function exotel_users_details($exotel_obj,$filter='')
    {
        try
        {
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://'.$exotel_obj->exotel_api_key.':'.$exotel_obj->exotel_api_token.'@ccm-api.exotel.com/v2/accounts/'.$exotel_obj->exotel_sid.'/users?fields=devices&offset=0&limit=50'.$filter,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                curl_setopt($curl, CURLOPT_USERPWD, $exotel_obj->exotel_api_key.':'.$exotel_obj->exotel_api_token)            
            ));

            $response = curl_exec($curl);

            curl_close($curl);                            

            $users = json_decode($response,true);
            $users = $users['response'];
            $exotelusers=[];
            foreach ($users as $user) 
                $exotelusers[$user['data']['email']] = $user['data']['devices'][0]['contact_uri'];
            return $exotelusers;
        }
        catch(\Exception $e)
        {            
            return false;
        }                
    }

    public function exotel_install(Request $request)
    {
        if(isset($_REQUEST['DOMAIN']))
        {                        
            $bitrix_domain = $_REQUEST['DOMAIN'];
            $access_token = $_REQUEST['AUTH_ID'];
            $refresh_token = $_REQUEST['REFRESH_ID'];
            $response = $this->get_access_token($refresh_token);
            if($response['is_valid']==false)
            {
                return json_encode($response);
            }
            $access_token = $response['access_token'];
            $refresh_token = $response['refresh_token'];
            $this->bind_telephony_event($bitrix_domain,$access_token);
            return view('Exotel::exotel.install');
        }
        return false;
    }



    public function testing()
    {
    }
    /* Dev */
}
