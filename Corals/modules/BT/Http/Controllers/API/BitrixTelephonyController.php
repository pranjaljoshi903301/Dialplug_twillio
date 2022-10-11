<?php

namespace Corals\Modules\BT\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\BT\DataTables\BitrixTelephonyDataTable;
use Corals\Modules\BT\Http\Requests\BitrixTelephonyRequest;
use Illuminate\Http\Request;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Models\BTUsers;
use Corals\Modules\BT\Models\DialplugLines;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\BT\Services\BitrixTelephonyService;
use Corals\Modules\BT\Transformers\API\BitrixTelephonyPresenter;
use DB;
use Session;

class BitrixTelephonyController extends APIBaseController
{
    protected $bitrixtelephonyService;

    /**
     * BitrixTelephonyController constructor.
     * @param BitrixTelephonyService $bitrixtelephonyService
     * @throws \Exception
     */
    public function __construct(BitrixTelephonyService $bitrixtelephonyService)
    {
        $this->bitrixtelephonyService = $bitrixtelephonyService;
        $this->bitrixtelephonyService->setPresenter(new BitrixTelephonyPresenter());

        $this->resource_url = config('bt.models.bitrixtelephony.resource_url');

        if (!in_array('bitrix_auth', $this->corals_middleware_except)) {
            array_push($this->corals_middleware_except, "bitrix_auth");
        }
        // parent::__construct();
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephonyDataTable $dataTable
     * @return mixed
     * @throws \Exception
     */
    public function index(BitrixTelephonyRequest $request, BitrixTelephonyDataTable $dataTable)
    {
        // $bitrixtelephony = $dataTable->query(new BitrixTelephony());
        // return $this->bitrixtelephonyService->index($bitrixtelephony, $dataTable);

        if (isSuperUser()) {
            $bitrixtelephony = BitrixTelephony::all();
            foreach ($bitrixtelephony as $bitrixtelephony) {
                $bitrixtelephony->subscription = Subscription::where('user_id', $bitrixtelephony->user_id)->where('status', 'active')->first();
                $bitrixtelephony->subscriptionActive = $bitrixtelephony->subscription->next_billing_at > date('Y-m-d');
                $bitrixtelephony->plan = Plan::where('id', $bitrixtelephony->subscription->plan_id)->get();
                $bitrixtelephony->bitrix_users = BTUsers::where('dialplug_id', $bitrixtelephony->id)->get();
                $bitrixtelephony->dialplug_lines = DialplugLines::where('dialplug_id', $bitrixtelephony->id)->get();
            }
            return response()->json($bitrixtelephony, 200);
        } else {
            return response()->json('Not Authorized', 403);
        }

    }

    // BM-Mobile Validation

    public function is_valid_mobile_subscriber_email($subscriber_email,$webhook){
        // check already exists or not
        $result = DB::table('bm_config')->where([['email',$subscriber_email],['webhook_url',$webhook],['product_id',5]])->first();
        if($result) return true;
        
        // check email or webhook exist for call tracker
        $result = DB::table('bm_config')->where([['email',$subscriber_email],['product_id',5]])->orWhere([['webhook_url',$webhook],['product_id',5]])->get();
        if(count($result)) return false;
                
        return true;
    }

    public function mobile_validation(Request $request){
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

    // BM-Mobile Validation

    // Bitrix Auth Start
    public function bitrix_auth (Request $request) {
        $code = $request->code;
        $domain = $request->domain;
        $config = BitrixTelephony::where('bitrix_url', "https://$domain/")->first();
        $client_secret = $config->secret_key;
        $client_id = $config->application_id;

        $result=file_get_contents("https://oauth.bitrix.info/oauth/token/?client_secret=$client_secret&grant_type=authorization_code&code=$code&client_id=$client_id");
        $result = (array)json_decode($result);

        $config->access_token = $result['access_token'];
        $config->refresh_token = $result['refresh_token'];
        $config->expires_in = $result['expires'];
        $config->member_id = $result['member_id'];

        $config->save();

        return redirect($this->resource_url);
    }
    // Bitrix Auth End
    
    public function get_by_url ($request) {
        $bitrixtelephony = BitrixTelephony::where('bitrix_url', $request)->first();
        if ($bitrixtelephony) {
            $bitrixtelephony->subscription = Subscription::where('user_id', $bitrixtelephony->user_id)->where('status', 'active')->first();
            $bitrixtelephony->plan = Plan::where('id', $bitrixtelephony->subscription->plan_id)->get();
            $bitrixtelephony->bitrix_users = BTUsers::where('dialplug_id', $bitrixtelephony->id)->get();
            $bitrixtelephony->dialplug_lines = DialplugLines::where('dialplug_id', $bitrixtelephony->id)->get();
            return response()->json($bitrixtelephony, 200);
        } else {
            return response()->json('Not Found', 404);
        }
    }

    public function update_by_url (Request $request, $url) {
        $bitrixtelephony = BitrixTelephony::where('bitrix_url', $url)->first();
        if ($bitrixtelephony) {
            $this->bitrixtelephonyService->update($request, $bitrixtelephony);
            return response()->json($bitrixtelephony, 200);
        } else {
            return response()->json('Not Found', 404);
        }
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BitrixTelephonyRequest $request)
    {
        try {
            $bitrixtelephony = $this->bitrixtelephonyService->store($request, BitrixTelephony::class);
            return apiResponse($this->bitrixtelephonyService->getModelDetails(), trans('Corals::messages.success.created', ['item' => $bitrixtelephony->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephony $bitrixtelephony
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BitrixTelephonyRequest $request, BitrixTelephony $bitrixtelephony)
    {
        try {
            return apiResponse($this->bitrixtelephonyService->getModelDetails($bitrixtelephony));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephony $bitrixtelephony
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BitrixTelephonyRequest $request, BitrixTelephony $bitrixtelephony)
    {
        try {
            $this->bitrixtelephonyService->update($request, $bitrixtelephony);

            return apiResponse($this->bitrixtelephonyService->getModelDetails(), trans('Corals::messages.success.updated', ['item' => $bitrixtelephony->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephony $bitrixtelephony
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BitrixTelephonyRequest $request, BitrixTelephony $bitrixtelephony)
    {
        try {
            $this->bitrixtelephonyService->destroy($request, $bitrixtelephony);

            return apiResponse([], trans('Corals::messages.success.deleted', ['item' => $bitrixtelephony->name]));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }
    

/* exotel methods move to exotel module
    // exotel click to call methods

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

    // calldate,webhook,user_id,call_id,duration,disposition,recordingurl
    public function bitrix_finish_api($row)
    {                
        $finishResult = $this->bitrix_execution($row['webhook'], "POST", "telephony.externalCall.finish", array(
            "USER_ID" => $row['user_id'],
            "CALL_ID" => $row['call_id'],
            "DURATION" => intval($row['duration']),
            "STATUS_CODE" => ($row['disposition'] != "completed") ? "603-S" : "200",
        ));

        $subscriber_email = "yuvrajhingerakola@digiclave.com";
        $this->exotel_logs($subscriber_email,$finishResult);

        if ($row['disposition'] == "completed") {
            $fileContent = file_get_contents($row['recordingurl']);
            $attachResult = $this->bitrix_execution($row['webhook'], "POST", "telephony.externalCall.attachRecord", array(
                "CALL_ID" => $row['call_id'],
                "FILENAME" => $row['recordingurl'],
                "FILE_CONTENT" => base64_encode($fileContent),
            ));
            $subscriber_email = "yuvrajhingerakola@digiclave.com";
            $this->exotel_logs($subscriber_email,$attachResult);
        }
        
    }

    // sid,api_key,api_token,CallSid
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

    // sid,api_key,api_token,from,to,callerid,bitrix_caller_id,bitrix_user_id
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

        curl_close($curl);        
        $subscriber_email = "yuvrajhingerakola@digiclave.com";
        $this->exotel_logs($subscriber_email,"--------exotel click to call api response ------");
        $this->exotel_logs($subscriber_email,$response);

    }
    public function callback_url()
    {
        // calldate,webhook,user_id,call_id,duration,disposition,recordingurl
        
        $result = $this->get_exotel_detail($_REQUEST['bitrix_domain']);
        if(!count($result)){
            $file = "custom_log";
            $this->exotel_logs($file,$_REQUEST);
            return;
        }
            
        $this->exotel_logs($_REQUEST['bitrix_domain'],"------Call Back URL--------");
        $this->exotel_logs($_REQUEST['bitrix_domain'],$result);
        $result = json_decode(json_encode($result), true);        
        $result = $result[0];

        $row['webhook'] = $result['webhook'];        
        $row['calldate'] = $_REQUEST['DateUpdated'];                
        $row['duration'] = strtotime(date('Y-m-d H:i:s')) - strtotime(urldecode($_REQUEST['call_start_at']));
        $row['user_id'] = $_REQUEST['bitrix_user_id'];
        $row['call_id'] = $_REQUEST['bitrix_call_id'];        
        $row['disposition'] = $_REQUEST['Status'];
        $row['recordingurl'] = "";
        if(isset($_REQUEST['RecordingUrl'])) $row['recordingurl'] = $_REQUEST['RecordingUrl'];
        $this->bitrix_finish_api($row);

    }    

    public function get_exotel_detail($bitrix_domain,$subscriber_id=false){
        if($subscriber_id) return DB::select("select * from exotel_db.exotel_details where id='".$subscriber_id."';");
        return DB::select("select * from exotel_db.exotel_details where bitrix_domain='".$bitrix_domain."';");
    }
    public function get_agent_detail($agent_id,$subscriber_id){
        return DB::select("select * from exotel_db.exotel_agent_details where agent_bitrix_id ='".$agent_id."' and subscriber_id='".$subscriber_id."';");
    }

    public function click_to_call()
    {                
        if(isset($_REQUEST['event'])){
            $data = $_REQUEST['data'];

            // get exotel detail from bitrix domain
            $result = $this->get_exotel_detail($_REQUEST['auth']['domain']);
            if(!count($result)){
                return;
            }
            
            $result = json_decode(json_encode($result), true);        
            $result = $result[0];
            
            // get user email
            $agent_detail = $this->get_agent_detail($data['USER_ID'],$result['id']);
            if(!count($agent_detail)){
                return;
            }
            $agent_detail = json_decode(json_encode($agent_detail), true);        
            $agent_detail = $agent_detail[0];

            $exotel_obj['sid'] =  $result['exotel_sid'];
            $exotel_obj['api_key'] =  $result['exotel_api_key'];
            $exotel_obj['api_token'] =  $result['exotel_api_token'];
            
            $exotel_obj['from'] =  $agent_detail['agent_phone'];
            $exotel_obj['to'] =  $data['PHONE_NUMBER'];
            $exotel_obj['callerid'] =  $agent_detail['exophone'];
            $exotel_obj['bitrix_call_id'] =  $data['CALL_ID'];
            $exotel_obj['bitrix_user_id'] =  $data['USER_ID'];
            $exotel_obj['bitrix_domain'] =  $result['bitrix_domain'];

             // sid,api_key,api_token,from,to,callerid,bitrix_caller_id,bitrix_user_id
            $this->exotel_connect_api($exotel_obj);
        }               
    
    }

    public function incoming_click_to_call()
    {
        $this->exotel_logs($_REQUEST['bitrix_domain'],"------incoming_click_to_call--------");
        $this->exotel_logs($_REQUEST['bitrix_domain'],$_REQUEST);

        $result = $this->get_exotel_detail($_REQUEST['bitrix_domain']);
        if(!count($result)){
            $file = "custom_log";
            $this->exotel_logs($file,$_REQUEST);
            return;
        }
            
        $this->exotel_logs($_REQUEST['bitrix_domain'],"------Incoming Call --------");
        $this->exotel_logs($_REQUEST['bitrix_domain'],$result);
        $result = json_decode(json_encode($result), true);        
        $result = $result[0];

        $subscriber_id = $result['id'];
        $row['webhook'] = $result['webhook'];                
        $row['CallSid'] = $_REQUEST['CallSid'];
        $row['disposition'] = "";
        
        $event_type = $_REQUEST['EventType'];
        if($event_type == "Dial"){            
            $row['agent_email'] = $_REQUEST['AgentEmail'];
            $row['CurrentTime'] = $_REQUEST['CurrentTime'];
            $row['CallFrom'] = $_REQUEST['CallFrom'];            

            $this->exotel_logs($_REQUEST['bitrix_domain'],"--- SHOW INCOMING CALL CARD ----");
            $result = $this->bitrix_register_api($row);
            $this->exotel_logs($_REQUEST['bitrix_domain'],$result);
            
            // store bitrix_call_id and bitrix_user_id and CallSid and subscriber_id
            $result = DB::select("insert into exotel_db.track_incoming_record(bitrix_call_id,bitrix_user_id,exotel_callsid,call_start_at,subscriber_id) values('".$result['bitrix_call_id']."','".$result['bitrix_user_id']."','".$row['CallSid']."','".$row['CurrentTime']."','".$subscriber_id."')");
        }        
        elseif($event_type == "Terminal"){                        

            $this->exotel_logs($_REQUEST['bitrix_domain'],"------Terminal--------");
            $this->exotel_logs($_REQUEST['bitrix_domain'],$_REQUEST);

            if(isset($_REQUEST['RecordingAvailableBy'])){
                $row['disposition'] = "completed";
            }

            // get bitrix_call_id and bitrix_user_id from CallSid
            $result = DB::select("select * from exotel_db.track_incoming_record where exotel_callsid='".$row['CallSid']."' order by created_at desc");
            $this->exotel_logs($_REQUEST['bitrix_domain'],$result);
            
            if(count($result)){                
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

                $this->exotel_logs($_REQUEST['bitrix_domain'],$finishResult);                

                if(isset($_REQUEST['RecordingAvailableBy'])){
                    $track_id=$result[0]['id'];
                    $result = DB::select("UPDATE exotel_db.track_incoming_record SET recording_status='0' WHERE id='$track_id'");                
                }

            }            
                                                
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
    
    // show call card for incoming calls 
    // webhook, agent_email, CurrentTime, CallFrom
    public function bitrix_register_api($row)
    {

        $user_info = $this->bitrix_execution($row['webhook'], "POST", "user.get", array("email" => $row['agent_email']));
        
        $subscriber_email = "yuvraj.hinger@digiclave.com";
        $this->exotel_logs($subscriber_email,$user_info);

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

        $subscriber_email = "yuvraj.hinger@digiclave.com";
        $this->exotel_logs($subscriber_email,$registerResult);
            
        $bitrix_call_id = $registerResult->result->CALL_ID;

        $output['bitrix_call_id'] = $bitrix_call_id;
        $output['bitrix_user_id'] = $bitrix_user_id;

        return $output;
    }

    public function exotel_default_view($result,$exotel_agents)
    {
        if($result['last_step_detail']=='0'){
            $result1 = DB::select("UPDATE exotel_db.exotel_details SET last_step_detail='-1' WHERE id=".$result['id']);
            return view("exotel.last_step_detail",['bitrix_domain'=>$result['bitrix_domain']]);
        }
        
        // manage exotel details
        if($result['last_step_detail']=='1'){
            $result1 = DB::select("UPDATE exotel_db.exotel_details SET last_step_detail='-1' WHERE id=".$result['id']);
            return view("exotel.home",['bitrix_domain'=>$result['bitrix_domain'],'exotel_detail'=>$result]);
        }

        // manage agent details
        if($result['last_step_detail']=='2'){
            $result1 = DB::select("UPDATE exotel_db.exotel_details SET last_step_detail='-1' WHERE id=".$result['id']);
            $exotel_agents = DB::select("select * from exotel_db.exotel_agent_details where status='0' and subscriber_id=".$result['id'].";");
            return $this->exotel_agent_view($result,$exotel_agents);
        }
        return view("exotel.exotel_default_view",['bitrix_domain'=>$result['bitrix_domain'],'subscriber_id'=>$result['id']]);
    }

    public function exotel_agent_view($result,$exotel_agents)
    {
        $exotel_agents = json_decode(json_encode($exotel_agents),true);
        $bitrix_agents = $this->bitrix_agents($result['webhook']);

        $exotel_obj['sid'] =  $result['exotel_sid'];
        $exotel_obj['api_key'] =  $result['exotel_api_key'];
        $exotel_obj['api_token'] =  $result['exotel_api_token'];
        $exophones = $this->get_exophones($exotel_obj);
        
        $all_agents=[];
        foreach($bitrix_agents as $bitrix_agent){
            foreach($exotel_agents as $exotel_agent){
                if($exotel_agent['agent_email']==$bitrix_agent['EMAIL']){
                    $bitrix_agent["EXOPHONE"] = $exotel_agent['exophone'];
                    $bitrix_agent["agent_phone"] = $exotel_agent['agent_phone'];
                    $bitrix_agent["status"] = "1";                            
                    break;
                }
            }      
            $all_agents[] = $bitrix_agent;            
        }                

        $this->exotel_logs($result['bitrix_domain'],$all_agents);

        // Agent Homepage
        return view('exotel.agent',['bitrix_domain'=>$result['bitrix_domain'],'bitrix_agents'=>$all_agents,'subscriber_id'=>$result['id'],'exophones'=>$exophones]);
    }

    public function exotel_view_index()
    {           
        if(isset($_REQUEST['DOMAIN'])){            
            
            // store complete request to logs
            $domain = $_REQUEST['DOMAIN'];
            $this->exotel_logs($domain,$_REQUEST);

            // get domain if registered
            $result = DB::select("select * from exotel_db.exotel_details where bitrix_domain='".$_REQUEST['DOMAIN']."';");            
            if(count($result)){
                $result = json_decode(json_encode($result), true);        
                $result = $result[0];
                $exotel_agents = DB::select("select * from exotel_db.exotel_agent_details where status='0' and subscriber_id=".$result['id'].";");

                if(!count($exotel_agents)) return $this->exotel_agent_view($result,$exotel_agents);                   
                return $this->exotel_default_view($result,$exotel_agents);                   
            }                        
            return view('exotel.home',['bitrix_domain'=>$_REQUEST['DOMAIN']]);            
        }                
        return false;        
    }
    
    public function store_exotel_details(Request $request)
    {        
        if(!$this->validate_webhook($request->webhook)){
            $msg = "Webhook";            
            return $msg;
        }

        $exotel_obj['sid'] =  $request->exotel_sid;
        $exotel_obj['api_key'] =  $request->exotel_api_key;
        $exotel_obj['api_token'] =  $request->exotel_api_token;
        if(!$this->get_exophones($exotel_obj)){
            $msg = "Exotel";            
            return $msg;            
        }

        if($request->subscriber_id!=-1){
            $query_data = "UPDATE exotel_db.exotel_details SET 
                webhook='$request->webhook',
                exotel_sid='$request->exotel_sid',
                exotel_api_key='$request->exotel_api_key',
                exotel_api_token='$request->exotel_api_token' 
                WHERE id='$request->subscriber_id';";
            $result = DB::select($query_data);        
            $this->exotel_logs($request->bitrix_domain,"-------Update Exotel Details------");
            $this->exotel_logs($request->bitrix_domain,$request);
            return true;
        }

        $query_data = 'INSERT INTO exotel_db.exotel_details (subscriber_email,webhook,exotel_sid,exotel_api_key,exotel_api_token,bitrix_domain) VALUES 
            ("'.$request['email'].'","'.$request['webhook'].'","'.$request['exotel_sid'].'","'.$request['exotel_api_key'].'","'.$request['exotel_api_token'].'","'.$request['bitrix_domain'].'")';
        $result = DB::select($query_data);        
        $this->exotel_logs($request->bitrix_domain,"-------Insert Exotel Details------");
        $this->exotel_logs($request->bitrix_domain,$request);
        return true;
    }

    public function store_exotel_agent_details(Request $request)
    {
        $query_data = "SELECT * FROM exotel_db.exotel_agent_details WHERE  agent_email='$request->agent_email' && subscriber_id='$request->subscriber_id'";
        $result = DB::select($query_data);
        if(count($result)){
            $result = json_decode(json_encode($result), true);        
            $agent_id = $result[0]['id'];
            $query_data = "UPDATE exotel_db.exotel_agent_details SET exophone='$request->exophone',agent_phone='$request->agent_phone' WHERE  id='$agent_id'";
            $result = DB::select($query_data);            
        }else{
            $query_data = "INSERT INTO exotel_db.exotel_agent_details (subscriber_id,agent_email,agent_phone,exophone,agent_bitrix_id) VALUES ('$request->subscriber_id','$request->agent_email','$request->agent_phone','$request->exophone','$request->agent_id')";
            $result = DB::select($query_data);
        }
        return true;
    }

    public function store_details(Request $request)
    {
        
        $this->exotel_logs("dialplug.com",$request);

        if(isset($request['set_session'])){            
            $result1 = DB::select("UPDATE exotel_db.exotel_details SET last_step_detail=".$request['set_session']." WHERE id=".$request['subscriber_id']);
            return true;
        }
        else if(isset($request['store_exotel_details'])){
            return $this->store_exotel_details($request);
        }
        elseif(isset($request['store_exotel_agent_details'])){
            return $this->store_exotel_agent_details($request);
        }        
                    
    }

    public function get_exophones($exotel_obj)
    {                    
        try{
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
            foreach ($exophones as $exophone) {                
                $phones[] = $exophone['sid'];
            }             
            return $phones;
        }
        catch(\Exception $e){            
            return false;
        }                
    }    

    public function validate_webhook($webhook){
        try{
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
        catch(\Exception $e){            
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

    // cron run in every n min
    public function upload_recording()
    {        
        // get all callsid whose status is 0: means require to upload recording
        $call_logs = DB::select("select * from exotel_db.track_incoming_record where recording_status='0' GROUP by subscriber_id");
        if(count($call_logs)){
            $call_logs = json_decode(json_encode($call_logs),true);
            foreach($call_logs as $call_log){
                // GET EXOTEL DETAIL     
                $exotel_detail = $this->get_exotel_detail(false,$call_log['subscriber_id']);
                $exotel_detail = json_decode(json_encode($exotel_detail),true);
                $exotel_detail = $exotel_detail[0];

                // get call recording url
                $exotel_obj['sid'] = $exotel_detail['exotel_sid'];
                $exotel_obj['api_key'] = $exotel_detail['exotel_api_key'];
                $exotel_obj['api_token'] = $exotel_detail['exotel_api_token'];
                $exotel_obj['CallSid'] = $call_log['exotel_callsid'];
                $recording_url = $this->exotel_call_logs_api($exotel_obj);
                $flag=2;
                if($recording_url){

                    $fileContent = file_get_contents($recording_url);
                    $attachResult = $this->bitrix_execution($exotel_detail['webhook'], "POST", "telephony.externalCall.attachRecord", array(
                        "CALL_ID" => $call_log['bitrix_call_id'],
                        "FILENAME" => $recording_url,
                        "FILE_CONTENT" => base64_encode($fileContent),
                    )); 
                    $flag=1;                   
                }                
                $output = DB::select("UPDATE exotel_db.track_incoming_record SET recording_status='$flag' where id=".$call_log['id']);
            }
        }

    }

    public function testing()
    {                
        echo "12";
    }
*/
}
