<?php

namespace Corals\Modules\BT\Http\Controllers\API;

require '../vendor/twilio-php-main/src/Twilio/autoload.php';

use Corals\Foundation\Http\Controllers\APIBaseController;
use Illuminate\Http\Request;
use Corals\Modules\BT\Services\BitrixTelephonyService;
use Corals\Modules\BT\Transformers\API\BitrixTelephonyPresenter;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\TwiML\VoiceResponse;
use Twilio\Jwt\ClientToken;

class TwilioBitrixController extends APIBaseController
{   
    
    private $bitrix_webhook;
    
    public function __construct(BitrixTelephonyService $bitrixtelephonyService)
    {
        $this->bitrixtelephonyService = $bitrixtelephonyService;
        $this->bitrixtelephonyService->setPresenter(new BitrixTelephonyPresenter());
    
    }            

    /* 
    ** use to trigger call to client sdk extension, trigger from bitrix24, define as outbound handler in bitrix
    */    
    public function outgoing_call()
    {        
        $this->twilio_logs("OUTGOING CALL TRIGGER ".json_encode($_REQUEST)."\n");

        $this->bitrix_validate_request();

        $bitrix_domain=$_REQUEST['auth']['domain'];
        $bitrix_call_id=$_REQUEST['data']['CALL_ID'];
        $bitrix_user_id=$_REQUEST['data']['USER_ID'];
        $to = $_REQUEST['data']['PHONE_NUMBER'];
        $bitrix_twilio_detail = \DB::table('bitrix24_twilio.bitrix_twilio_detail')->where('bitrix_domain',$bitrix_domain)->first();
		if(!$bitrix_twilio_detail)		
			abort(401);
        
        $bitrix_twilio_agent = \DB::table('bitrix24_twilio.bitrix_twilio_agent')->where([['bitrix_twilio_id',$bitrix_twilio_detail->id],['bitrix_user_id',$bitrix_user_id]])->first();
        if(!$bitrix_twilio_agent)		
            abort(401);		

        
        $param = array(
            'bitrix_domain'=>$bitrix_domain,
            'bitrix_call_id'=>$bitrix_call_id,
            'bitrix_user_id'=>$bitrix_user_id
        );
        $param = http_build_query($param);
        $this->twilio_api($bitrix_twilio_detail,$bitrix_twilio_agent,$to,$param);
    }

    /* 
    ** use to close bitrix call card + attach recording, define as twilio callback 
    */    
    public function outgoing_callback()
    {
        $this->twilio_logs("OUTGOING CALLBACK TRIGGER ".json_encode($_REQUEST)."\n");   
        $this->twilio_validate_request();

        $row['user_id'] = $_REQUEST['bitrix_user_id'];
        $row['call_id'] = $_REQUEST['bitrix_call_id'];        
        $row['bitrix_domain'] = $_REQUEST['bitrix_domain'];        
        
        $bitrix_twilio_detail = \DB::table('bitrix24_twilio.bitrix_twilio_detail')->where('bitrix_domain',$row['bitrix_domain'])->first();
		if(!$bitrix_twilio_detail)		
			abort(401);
        
        $bitrix_twilio_agent = \DB::table('bitrix24_twilio.bitrix_twilio_agent')->where([['bitrix_twilio_id',$bitrix_twilio_detail->id],['bitrix_user_id',$row['user_id']]])->first();
        if(!$bitrix_twilio_agent)		
            abort(401);		

        $this->bitrix_webhook = $bitrix_twilio_detail->bitrix_webhook;

        if(isset($_REQUEST['recordingstatuscallback']))
        {
            $row['recordingurl'] = $_REQUEST['RecordingUrl'];
            $this->attach_recording($row);
        }
        else
        {
            $row['duration'] = $_REQUEST['CallDuration'];        
            $row['disposition'] = $_REQUEST['CallStatus'];        
            $this->bitrix_finish_api($row);
        }
    }

    /* 
    ** return access token to dialplug twilio sdk client extension 
    */    
    public function extension_token_request(Request $request)
	{
		$bitrix_domain = $request->bitrix_domain;
		$dialplug_user = $request->dialplug_user;
		$dialplug_secret = $request->dialplug_secret;

		/* check details validation */
		$bitrix_twilio_detail = \DB::table('bitrix24_twilio.bitrix_twilio_detail')->where('bitrix_domain',$bitrix_domain)->first();
		if(!$bitrix_twilio_detail)		
			abort(401);
		
		$bitrix_twilio_agent = \DB::table('bitrix24_twilio.bitrix_twilio_agent')->where([['bitrix_twilio_id',$bitrix_twilio_detail->id],['username',$dialplug_user],['password',$dialplug_secret]])->first();
		if(!$bitrix_twilio_agent)		
			abort(401);		

		return $this->generate_access_token($bitrix_twilio_detail,$bitrix_twilio_agent);
	}


    /* 
    ** default twiml trigger when call place from sdk client extension
    */    
    public function default_twiml_response()
	{
		if(isset($_REQUEST['To']) && isset($_REQUEST["callerId"])){
			$to=$_REQUEST['To'];
			$callerId=$_REQUEST["callerId"];
			echo "<Response><Dial callerId=\"$callerId\">$to</Dial></Response>";
		}
		else{
			echo "<Response><Say>Something went wrong, Please check with support.!</Say></Response>";
		}
		return;
	}

    /*
    ** helper functions **
    */

    /* 
    ** use to place call using twilio rest api
    */
    private function twilio_api($bitrix_twilio_detail,$bitrix_twilio_agent,$to,$param)
    {
        $client_sdk = "client:".$bitrix_twilio_agent->username;
        $account_sid = $bitrix_twilio_detail->twilio_account_sid;
        $account_token = $bitrix_twilio_detail->twilio_auth_token;
        
        $statuscallback = "https://dialplug.com/api/v1/bitrix-twilio/outgoing_callback?$param";        
        $recordingstatuscallback = $statuscallback."&recordingstatuscallback=true";

        // $this->outgoing_rule($to);
        $caller_id = $bitrix_twilio_agent->caller_id;

        $twilio = new Client($account_sid, $account_token);                

        $call = $twilio->calls
        ->create($client_sdk, // to
            $caller_id, // from            
            [
                "record" => true,
                "twiml" => '<Response><Dial callerId="'.$caller_id.'">'.$to.'</Dial></Response>',
                "statusCallback" => $statuscallback,
                'recordingStatusCallback' => $recordingstatuscallback,
            ]
        );      

        $this->twilio_logs("TWILIO CALL API LOG ".json_encode($call)."  \n REQUEEST DATA:".json_encode($_REQUEST));

    }

    /* 
    ** return access token to dialplug twilio sdk client extension 
    */
    private function generate_access_token($bitrix_twilio_detail,$bitrix_twilio_agent)
	{
		$twilioAccountSid = $bitrix_twilio_detail->twilio_account_sid;
		$twilioApiKey = $bitrix_twilio_detail->twilio_api_key;
		$twilioApiSecret = $bitrix_twilio_detail->twilio_api_secret;		
		$outgoingApplicationSid = $bitrix_twilio_detail->twiml_sid;
		
		$identity = $bitrix_twilio_agent->username; 
		
		$token = new AccessToken(
		    $twilioAccountSid,
		    $twilioApiKey,
		    $twilioApiSecret,
		    3600,
		    $identity
		);
		
		$voiceGrant = new VoiceGrant();
		$voiceGrant->setOutgoingApplicationSid($outgoingApplicationSid);		
		$voiceGrant->setIncomingAllow(true);		
		
		$token->addGrant($voiceGrant);

		return response()->json([
			"caller_id" => $bitrix_twilio_agent->caller_id,
			"identity" => $identity,
			"token" => $token->toJWT() 
		]);
	}


    /* 
    ** use to place recording from twilio to bitrix, trigger from twilio recording call back function
    */
    private function attach_recording($row)
    {
        $attachResult = $this->bitrix_execution("POST", "telephony.externalCall.attachRecord", array(
            "CALL_ID" => $row['call_id'],
            "FILENAME" => "twiliorecording_".strtotime("now").".wav",
            "RECORD_URL" => $row['recordingurl'],
        ));               

        $this->twilio_logs("BITRIX RECORDING API RESPONSE ".json_encode($attachResult)."\n");
    }


    /* 
    ** use to close bitrix call card, trigger from twilio call back function
    */
    private function bitrix_finish_api($row)
    {                
        $finishResult = $this->bitrix_execution("POST", "telephony.externalCall.finish", array(
            "USER_ID" => $row['user_id'],
            "CALL_ID" => $row['call_id'],
            "DURATION" => intval($row['duration']),
            "STATUS_CODE" => ($row['disposition'] != "completed") ? "603-S" : "200",           
        ));

        $this->twilio_logs("BITRIX FINISH API RESPONSE ".json_encode($finishResult)."\n");       
    }

    /* 
    ** all bitrix rest api can execute using this helper method
    */
    private function bitrix_execution($method_type, $method_name, $data = null)
    {        
        $url = $this->bitrix_webhook.$method_name;        
    
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER => true,
        );
    
        if ($method_type == "POST") {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
        } elseif (!empty($data)) {
            $url .= strpos($url, "?") > 0 ? "&" : "?";
            $url .= http_build_query($data);
        }
        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOptions);
        $result = curl_exec($curl);
        return $result;
    
    }

    /* 
    ** validation of request comes from bitrix outbound handler
    */
    private function bitrix_validate_request()
    {
        if((
            isset($_REQUEST['data']['PHONE_NUMBER']) && 
            isset($_REQUEST['auth']['domain']) && 
            isset($_REQUEST['data']['CALL_ID']) && 
            isset($_REQUEST['data']['USER_ID'])
        ))
            return true;
        
        abort(401);                
    }

    /* 
    ** validation of request comes from twilio callback handler
    */
    private function twilio_validate_request()
    {
        if(isset($_REQUEST['bitrix_domain']) && isset($_REQUEST['bitrix_call_id']))        
            return true;
        
        abort(401);
    }

    /* 
    ** rules to get caller_id for outgoing calls
    */
    private function outgoing_rule($to)
    {        
        $area_codes = array(
            "+16109368967" => [484,610], 
            "+18177847213" => [817],
            "+18438063360" => [843],
            "+13195194624" =>[319],
            "+13187032109" =>[318],
            "+14153198739" =>[415],
            "+14124363768" =>[412],
            "+18602007026" =>[860],
            "+16173156204" =>[617],
            "+12053033897" =>[205],
            "+15154417582" =>[515],
            "+12489857205" =>[248],
            "+18042970869" =>[804],
            "+12697434793" =>[269],
            "+19378136228" =>[937],
            "+12153986483" =>[215],
            "+19799778900" =>[979],
            "+15592061747" =>[559],
            "+19168273937" =>[916],
            "+12489234432" =>[530],
            "+15305136411" =>[530],
            "+18015286829" =>[801],
            "+15413590974" =>[541],
            "+18646332895" =>[864],
            "+18772608345" =>[877]
        );        
        $this->twilio_logs("TO NUMBER ".json_encode($to)."\n");
        $to = str_replace(array( '(', ')' ,'-',' '), '', $to);
        $to = ltrim($to,'+');
        $count=strlen($to);
        if($count=='11')
        {
            $to=$to-10000000000;
            $to=intdiv($to,10000000);
        }
        elseif($count=='10')
        {
            $to=intdiv($to,10000000);
        }
        $caller_id = array_search([$to],$area_codes);
        if($caller_id) $this->twilio_caller_id = $caller_id;
        return true;
    }


    /*
    ** FUNCTION LOGS
    */
    private function twilio_logs($log)
    {
        $date = date('Y-m-d');
        $date = strtotime($date);
        $message = '['.date('Y-m-d H-i-s').'] => ';        
        $message .= json_encode($log);
        $message .= "\n";
        $dir = storage_path().'/logs/twilio/';
        $path = $dir."twiliobitrix_$date.log";
        if(!is_dir($dir)) mkdir($dir);        
        file_put_contents($path,$message,FILE_APPEND);
    }    



    
}
