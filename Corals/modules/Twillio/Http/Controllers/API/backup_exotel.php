<?php

namespace Corals\Modules\Twillio\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Twillio\DataTables\BarsDataTable;
use Corals\Modules\Twillio\Http\Requests\BarRequest;
use Corals\Modules\Twillio\Models\Bar as TwillioDetails;
use Corals\Modules\Twillio\Models\AgentDetail;
use Corals\Modules\Twillio\Models\UsageDetail;
use Corals\Modules\Twillio\Services\BarService;
use Corals\Modules\Twillio\Transformers\API\BarPresenter;
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
    protected $client_id=false; // get when register the app on partners portal
    protected $client_secret=false; // get when register the app on partners portal
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


    // twillio click to call methods
    
    /*  User Interface Methods
        1. twillio_view_index            =>  default call + general view
        2. store_details                =>  default call
        3. store_twillio_details         =>  store general detail
        4. twillio_agent_view            =>  agent view
        5. store_twillio_agent_details   =>  store agent detail
        6. twillio_default_view          =>  common view
        7. validate_webhook             =>  bitrix_api
        8. get_exophones                =>  twillio_api
        9. bitrix_agents                =>  bitrix_api
    */

    public function twillio_view_index()
    {           
        if(isset($_REQUEST['DOMAIN']))
        {            
            
            // event: get general details if already exist
            $twillio_detail = TwillioDetails::where('bitrix_domain',$_REQUEST['DOMAIN'])->first(); 

            if($twillio_detail)
            {
                
                // event: get agents details if already exist
                $twillio_agents = AgentDetail::where([['status','0'],['subscriber_id',$twillio_detail->id]])->get(); 
                
                if(count($twillio_agents))
                    return $this->twillio_default_view($twillio_detail,$twillio_agents);

                return $this->twillio_agent_view($twillio_detail,$twillio_agents);

            }                        
            return view('Twillio::twillio.home',['bitrix_domain'=>$_REQUEST['DOMAIN']]);
        }    
        return "Method not Allowed";                
    }

    public function store_details(Request $request)
    {                
        if($request->has('store_twillio_details'))
        {
            return $this->store_twillio_details($request);
        }
        elseif($request->has('store_twillio_agent_details'))
        {
            return $this->store_twillio_agent_details($request);
        }        
        else if($request->has('set_session'))
        {            
            $twillio_details = TwillioDetails::find($request->subscriber_id);
            $twillio_details->last_step_detail = $request->set_session;
            $twillio_details->save();
            return true;
        }                
                    
    }

    public function store_twillio_details(Request $request)
    {        
        // Webhook Validation
        if(!$this->validate_webhook($request->webhook))
        {
            $msg = "Webhook";
            return $msg;
        }

        $twillio_obj['sid'] =  $request->twillio_sid;
        $twillio_obj['api_key'] =  $request->twillio_api_key;
        $twillio_obj['api_token'] =  $request->twillio_api_token;
        if(!$this->get_exophones($twillio_obj))
        {
            $msg = "Twillio";            
            return $msg;            
        }

        // Insert/Update General Details
        if($request->subscriber_id==-1)     
        {
            $twillio_detail = new TwillioDetails();
            $twillio_detail->subscriber_email = $request->email;
            $twillio_detail->bitrix_domain = $request->bitrix_domain;

            // register user and subscribe to twillio
            $user = User::where('email',$twillio_detail->subscriber_email)->first();
            if(!$user){
                $user_response = $this->register_user($twillio_detail->subscriber_email,'0123456789');
                $user_id = $user_response['data']['id'];
                $user = User::find($user_id);
            }
            $plan_id = $this->plan_id;
            $plan = Plan::find($plan_id);
            $subscription_response = $this->subscribe_user($user,$plan);
        }                       
        else
            $twillio_detail = TwillioDetails::find($request->subscriber_id);                                    
        
        $twillio_detail->webhook = $request->webhook;
        $twillio_detail->twillio_sid = $request->twillio_sid;
        $twillio_detail->twillio_api_key = $request->twillio_api_key;
        $twillio_detail->twillio_api_token = $request->twillio_api_token;
        $twillio_detail->save(); 
        
        return true;
        /* $query_data = "UPDATE twillio_db.twillio_details SET webhook='$request->webhook',twillio_sid='$request->twillio_sid',twillio_api_key='$request->twillio_api_key',twillio_api_token='$request->twillio_api_token' WHERE id='$request->subscriber_id';";$result = DB::select($query_data);       */                        
        /* $query_data = 'INSERT INTO twillio_db.twillio_details (subscriber_email,webhook,twillio_sid,twillio_api_key,twillio_api_token,bitrix_domain) VALUES ("'.$request['email'].'","'.$request['webhook'].'","'.$request['twillio_sid'].'","'.$request['twillio_api_key'].'","'.$request['twillio_api_token'].'","'.$request['bitrix_domain'].'")';$result = DB::select($query_data);  */
            
    }

    public function twillio_agent_view($result,$twillio_agents)
    {    
        // get all bitrix agents (extensions)
        $bitrix_agents = $this->bitrix_agents($result->webhook);        
        
        if(!$bitrix_agents)
            return view("Twillio::twillio.home",['bitrix_domain'=>$result->bitrix_domain,'twillio_detail'=>$result,'error'=>'Invalid Webhook']);        
        
        // map bitrix agent with twillio agent 
        $all_agents=[];
        foreach($bitrix_agents as $bitrix_agent)
        {
            foreach($twillio_agents as $twillio_agent)
            {
                if($twillio_agent->agent_email==$bitrix_agent['EMAIL'])
                {
                    $bitrix_agent["EXOPHONE"] = $twillio_agent->exophone;
                    $bitrix_agent["agent_phone"] = $twillio_agent->agent_phone;
                    $bitrix_agent["status"] = "1";                            
                    break;
                }
            }      
            $all_agents[] = $bitrix_agent;
        }                        

        $twillio_obj['sid'] =  $result->twillio_sid;
        $twillio_obj['api_key'] =  $result->twillio_api_key;
        $twillio_obj['api_token'] =  $result->twillio_api_token;
        
        $exophones = $this->get_exophones($twillio_obj);
        
        if(!$exophones)
            return view("Twillio::twillio.home",['bitrix_domain'=>$result->bitrix_domain,'twillio_detail'=>$result,'error'=>'Invalid Twillio Details']);        

        // Agent Homepage
        return view('Twillio::twillio.agent',['bitrix_domain'=>$result->bitrix_domain,'bitrix_agents'=>$all_agents,'subscriber_id'=>$result->id,'exophones'=>$exophones]);
    }

    public function store_twillio_agent_details(Request $request)
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
        $agent_detail->save(); /*$query_data = "UPDATE twillio_db.twillio_agent_details SET exophone='$request->exophone',agent_phone='$request->agent_phone' WHERE  id='$agent_id'";*/     /*$query_data = "INSERT INTO twillio_db.twillio_agent_details (subscriber_id,agent_email,agent_phone,exophone,agent_bitrix_id) VALUES ('$request->subscriber_id','$request->agent_email','$request->agent_phone','$request->exophone','$request->agent_id')";*/            
        
        return true;
    }

    public function twillio_default_view($result,$twillio_agents)
    {        
        $twillio_detail = TwillioDetails::find($result->id);
        $twillio_detail->last_step_detail='-1';
        $twillio_detail->save();
        
        // basic guide detail
        if($result->last_step_detail=='0')
            return view("Twillio::twillio.last_step_detail",['bitrix_domain'=>$twillio_detail->bitrix_domain]);        
        
        // manage twillio details
        else if($result->last_step_detail=='1')
            return view("Twillio::twillio.home",['bitrix_domain'=>$twillio_detail->bitrix_domain,'twillio_detail'=>$twillio_detail]);
        
        // manage agent details
        else if($result->last_step_detail=='2'){                        
            $twillio_agents = AgentDetail::where([['status','0'],['subscriber_id',$twillio_detail->id]])->get();
            return $this->twillio_agent_view($twillio_detail,$twillio_agents);
        }

        $subscription_status = $this->twillio_subscription_validation($twillio_detail->subscriber_email);            
        return view("Twillio::twillio.twillio_default_view",['bitrix_domain'=>$twillio_detail->bitrix_domain,'subscriber_id'=>$twillio_detail->id,'subscription_status'=>$subscription_status]);
    }

    public function validate_webhook($webhook)
    {
        try
        {
            $users_list = file_get_contents(
                $webhook."user.get",
                false,
                stream_context_create([
                    'http' => [
                        'ignore_errors' => true,
                        'method' => 'GET',
                        'timeout' => 10,
                    ],
                ])
            );                    
            $users_list = json_decode($users_list);
            if(isset($users_list->error)) return false;
            return $users_list;
        }
        catch(\Exception $e)
        {            
            return false;
        }        
    }

    public function get_exophones($twillio_obj)
    {                    
        try
        {
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://'.$twillio_obj['sid'].':'.$twillio_obj['api_token'].'@api.twillio.com/v2_beta/Accounts/'.$twillio_obj['sid'].'/IncomingPhoneNumbers',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                curl_setopt($curl, CURLOPT_USERPWD, $twillio_obj['api_key'].':'.$twillio_obj['api_token'])            
            ));

            $response = curl_exec($curl);

            curl_close($curl);                

            $exophones = json_decode($response,true);
            $exophones = $exophones['incoming_phone_numbers'];
            $phones=[];
            foreach ($exophones as $exophone) 
                $phones[] = $exophone['sid'];            
            return $phones;
        }
        catch(\Exception $e)
        {            
            return false;
        }                
    }    

    public function bitrix_agents($webhook)
    {
        try{            
            $users_list = $this->validate_webhook($webhook);
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
        2. twillio_connect_api       => twillio_api
        3. callback_url             => outgoing callback
        4. bitrix_finish_api        => close bitrix24 card [outgoing/incoming]
        5. incoming_click_to_call   => incoming 
        6. bitrix_register_api      => show bitrix24 card
        7. bitrix_execution         => bitrix_api
        8. twillio_call_logs_api     => twillio_api
        9. upload_recording         => bitrix_api
    */

    
    /* outgoing calls */
    public function click_to_call()
    {                
        // $this->twillio_logs("data_check",json_encode($_REQUEST));
        if(isset($_REQUEST['event']))
        {
            $data = $_REQUEST['data'];            

            // get twillio detail from bitrix domain
            $result = TwillioDetails::where('bitrix_domain',$_REQUEST['auth']['domain'])->first();
            if(!$result) return;
            
            // check subscription validation
            if(!$this->twillio_subscription_validation($result->subscriber_email)) return;

            // get user email
            $agent_detail = AgentDetail::where([['agent_bitrix_id',$data['USER_ID']],['subscriber_id',$result->id]])->first();            
            if(!$agent_detail) return;            
            
            $twillio_obj['sid'] =  $result->twillio_sid;
            $twillio_obj['api_key'] =  $result->twillio_api_key;
            $twillio_obj['api_token'] =  $result->twillio_api_token;
            
            $twillio_obj['from'] =  $agent_detail->agent_phone;
            $twillio_obj['to'] =  $data['PHONE_NUMBER'];
            $twillio_obj['callerid'] =  $agent_detail->exophone;
            $twillio_obj['bitrix_call_id'] =  $data['CALL_ID'];
            $twillio_obj['bitrix_user_id'] =  $data['USER_ID'];
            $twillio_obj['bitrix_domain'] =  $result->bitrix_domain;

             // sid,api_key,api_token,from,to,callerid,bitrix_caller_id,bitrix_user_id
            $this->twillio_connect_api($twillio_obj);
        }               
    }
    public function twillio_connect_api($twillio_obj)
    {            
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://'.$twillio_obj['sid'].':'.$twillio_obj['api_token'].'@api.twillio.com/v1/Accounts/'.$twillio_obj['sid'].'/Calls/connect',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'From' => $twillio_obj['from'],
                'To' => $twillio_obj['to'],
                'CallerId' => $twillio_obj['callerid'],                            
                'StatusCallback' => 'https://dialplug.com/api/v1/twillio/callback-url?bitrix_domain='.$twillio_obj['bitrix_domain'].'&bitrix_call_id='.$twillio_obj['bitrix_call_id'].'&bitrix_user_id='.$twillio_obj['bitrix_user_id'].'&call_start_at='.urlencode(date("Y-m-d H:i:s"))
            ),
            curl_setopt($curl, CURLOPT_USERPWD, $twillio_obj['api_key'].':'.$twillio_obj['api_token'])            
        ));

        $response = curl_exec($curl);

        curl_close($curl);        
        
        /* check response and notify user if error */
    }
    public function callback_url()
    {
        // calldate,webhook,user_id,call_id,duration,disposition,recordingurl
                
        if(!isset($_REQUEST['bitrix_domain'])) return;
        $result = TwillioDetails::where('bitrix_domain',$_REQUEST['bitrix_domain'])->first();
        if(!$result) return;        
                    
        $row['webhook'] = $result->webhook;                
        $row['duration'] = strtotime(date('Y-m-d H:i:s')) - strtotime(urldecode($_REQUEST['call_start_at']));
        $row['user_id'] = $_REQUEST['bitrix_user_id'];
        $row['call_id'] = $_REQUEST['bitrix_call_id'];        
        $row['disposition'] = $_REQUEST['Status'];
        $row['recordingurl'] = "";
        if(isset($_REQUEST['RecordingUrl'])) $row['recordingurl'] = $_REQUEST['RecordingUrl'];
        $this->bitrix_finish_api($row);

        //usage monitoring
        $this->usage_monitoring($_REQUEST['bitrix_domain'],$_REQUEST['bitrix_user_id'],$row['duration'],$_REQUEST['Status'],'outbound');

    }
    public function bitrix_finish_api($row)
    {                
        $finishResult = $this->bitrix_execution($row['webhook'], "POST", "telephony.externalCall.finish", array(
            "USER_ID" => $row['user_id'],
            "CALL_ID" => $row['call_id'],
            "DURATION" => intval($row['duration']),
            "STATUS_CODE" => ($row['disposition'] != "completed") ? "603-S" : "200",
        ));

        if ($row['disposition'] == "completed" && $row['recordingurl']!="") 
        {            
            $attachResult = $this->bitrix_execution($row['webhook'], "POST", "telephony.externalCall.attachRecord", array(
                "CALL_ID" => $row['call_id'],
                "FILENAME" => $row['recordingurl'],
                "RECORD_URL" => $row['recordingurl'],
            ));            
        }
        
    }
    /* incoming calls */
    public function incoming_click_to_call()
    {
        
        $this->twillio_logs("data_check",json_encode($_REQUEST));

        if(!isset($_REQUEST['bitrix_domain'])) return;
        $result = TwillioDetails::where('bitrix_domain',$_REQUEST['bitrix_domain'])->first();
        if(!$result) return;

        // check subscription validation
        if(!$this->twillio_subscription_validation($result->subscriber_email)) return;
            
        $subscriber_id = $result->id;
        $row['webhook'] = $result->webhook;                
        $row['CallSid'] = $_REQUEST['CallSid'];
        $row['disposition'] = "failed";
        
        $event_type = $_REQUEST['EventType'];
        if($event_type == "Dial")
        {            
            $row['agent_email'] = $_REQUEST['AgentEmail'];
            $row['CurrentTime'] = $_REQUEST['CurrentTime'];
            $row['CallFrom'] = $_REQUEST['CallFrom'];                        
            $result = $this->bitrix_register_api($row);            

            /* check result and notify user if error */
            // $this->twillio_logs("data_check","--------call dial---------\n".json_encode($result));
            
            // store bitrix_call_id and bitrix_user_id and CallSid and subscriber_id
            $result = DB::select("insert into twillio_db.track_incoming_record(bitrix_call_id,bitrix_user_id,twillio_callsid,call_start_at,subscriber_id) values('".$result['bitrix_call_id']."','".$result['bitrix_user_id']."','".$row['CallSid']."','".$row['CurrentTime']."','".$subscriber_id."')");
        }        
        elseif($event_type == "Terminal")
        {                                    
            if(isset($_REQUEST['RecordingAvailableBy']))
                $row['disposition'] = "completed";            

            // get bitrix_call_id and bitrix_user_id from CallSid
            $result = DB::select("select * from twillio_db.track_incoming_record where twillio_callsid='".$row['CallSid']."' order by created_at desc");
            
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
                 
                
                $finishResult = $this->bitrix_execution($row['webhook'], "POST", "telephony.externalCall.finish", array(
                    "USER_ID" => $row['bitrix_user_id'],
                    "CALL_ID" => $row['bitrix_call_id'],
                    "DURATION" => intval($row['duration']),
                    "STATUS_CODE" => ($row['disposition'] != "completed") ? "603-S" : "200",
                ));                

                if(isset($_REQUEST['RecordingAvailableBy']))
                {
                    $track_id=$result[0]['id'];
                    $result = DB::select("UPDATE twillio_db.track_incoming_record SET recording_status='0' WHERE id='$track_id'");
                }

                //usage monitoring
                $this->usage_monitoring($_REQUEST['bitrix_domain'],$row['bitrix_user_id'],$row['duration'],$row['disposition'],'inbound');

            }            
                                                
        }

    }
    public function bitrix_register_api($row)
    {

        $user_info = $this->bitrix_execution($row['webhook'], "POST", "user.get", array("email" => $row['agent_email']));
                
        if(!isset($user_info->result[0]->ID)) return false;
        $bitrix_user_id = $user_info->result[0]->ID;
        

        $registerResult = $this->bitrix_execution($row['webhook'], "POST", "telephony.externalCall.register", array(
            "USER_PHONE_INNER" => $user_info->result[0]->UF_PHONE_INNER,
            "CALL_START_DATE" => date(DATE_ISO8601, strtotime(date('Y-m-d H:i:s'))),
            "PHONE_NUMBER" => $row['CallFrom'],
            "USER_ID" => $bitrix_user_id,
            "TYPE" => 2,
            "SHOW" => "1",
            "CRM_CREATE" => "1",
        ));

        $this->twillio_logs("data_check",json_encode($registerResult));

        if(!isset($registerResult->result->CALL_ID)) return false;
        $bitrix_call_id = $registerResult->result->CALL_ID;

        $output['bitrix_call_id'] = $bitrix_call_id;
        $output['bitrix_user_id'] = $bitrix_user_id;

        return $output;
    }
    public function bitrix_execution($webhook, $method, $url, $data = null)
    {
        $query_data = "";
        $url = $webhook . $url;
    
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
    public function twillio_call_logs_api($twillio_obj)
    {            
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://'.$twillio_obj['sid'].':'.$twillio_obj['api_token'].'@api.twillio.com/v1/Accounts/'.$twillio_obj['sid'].'/Calls.json?Sid='.$twillio_obj['CallSid'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',            
            curl_setopt($curl, CURLOPT_USERPWD, $twillio_obj['api_key'].':'.$twillio_obj['api_token'])            
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
        $call_logs = DB::select("select * from twillio_db.track_incoming_record where recording_status in ('0','1')");
        if(count($call_logs)){
            $call_logs = json_decode(json_encode($call_logs),true);
            foreach($call_logs as $call_log){
                // GET EXOTEL DETAIL     
                $twillio_detail = TwillioDetails::find($call_log['subscriber_id']);
                
                // get call recording url
                $twillio_obj['sid'] = $twillio_detail->twillio_sid;
                $twillio_obj['api_key'] = $twillio_detail->twillio_api_key;
                $twillio_obj['api_token'] = $twillio_detail->twillio_api_token;
                $twillio_obj['CallSid'] = $call_log['twillio_callsid'];
                $recording_url = $this->twillio_call_logs_api($twillio_obj);                
                if($recording_url)
                {
                    
                    $attachResult = $this->bitrix_execution($twillio_detail['webhook'], "POST", "telephony.externalCall.attachRecord", array(
                        "CALL_ID" => $call_log['bitrix_call_id'],
                        "FILENAME" => $recording_url,                        
                        "RECORD_URL" => $recording_url,
                    ));                     
                    $output = DB::select("UPDATE twillio_db.track_incoming_record SET recording_status='2' where id=".$call_log['id']);
                }                
                else
                {
                    $output = DB::select("UPDATE twillio_db.track_incoming_record SET recording_status=recording_status+1 where id=".$call_log['id']);
                }                                
            }
        }

    }
    /* Click to Call Methods  [9] */

    /* Dev */
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
                'domain'=>'https://dialplug.com'
            );
            try{
                Mail::send('Twillio::twillio.welcome_mail', $maildata, function ($emailMessage) use ($maildata, $email) {
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
    public function twillio_subscription_validation($email)
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
    public function twillio_logs($log_filename,$log){
        $message = '['.date('Y-m-d H-i-s').'] => ';
        $message .= json_encode($log);
        $message .= "\n";
        $dir = storage_path().'/logs/twillio/';
        $path = $dir.$log_filename.".log";
        if(!is_dir($dir)) mkdir($dir);        
        file_put_contents($path,$message,FILE_APPEND);
    }
    public function bitrix_notification_api($webhook,$message,$user_id)
    {        

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $webhook."im.notify.json?message="."&to=".$user_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=2; PHPSESSID=povzIuwo2rZ6tIHfgNUj3sWXu7jlPRNf'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);        

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
    public function testing()
    {                                
    }
    /* Dev */
}
