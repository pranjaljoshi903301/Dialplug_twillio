<?php

namespace Corals\Modules\BT\Http\Controllers\API;

require '../vendor/twilio-php-main/src/Twilio/autoload.php';

use Corals\Foundation\Http\Controllers\APIBaseController;
use Illuminate\Http\Request;
use Corals\Modules\BT\Services\BitrixTelephonyService;
use Corals\Modules\BT\Transformers\API\BitrixTelephonyPresenter;
use Twilio\Rest\Client;


class TwilioDevController extends APIBaseController
{    
    private $twilio_bitrix_webhook="https://smartcrm.digiclave.com/rest/1/yd5rzh14bcyq3zdj/";
    private $twilio_sid="AC395e8b256e132c15f177812283633da3";
    private $twilio_token="ae544bc443b253e68a8093b349aa1389";
    // private $twilio_sip="sip:101@dialplug.sip.twilio.com";
    private $twilio_sip="client:101";
    private $twilio_caller_id="+13477063925";

    function __construct(BitrixTelephonyService $bitrixtelephonyService)
    {
        $this->bitrixtelephonyService = $bitrixtelephonyService;
        $this->bitrixtelephonyService->setPresenter(new BitrixTelephonyPresenter());
    
    }
    
    function bitrix_validate_request()
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

    function twilio_validate_request()
    {
        if(isset($_REQUEST['bitrix_domain']) && isset($_REQUEST['bitrix_call_id']))        
            return true;
        
        abort(401);
    }

    function outgoing_rule($to)
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

    function twilio_api($to,$param)
    {
        $sid = $this->twilio_sid;
        $token = $this->twilio_token;
        $sip = $this->twilio_sip;        
        $statuscallback = "https://dialplug.com/api/v1/twiliodev/outgoing_callback?$param";        
        $recordingstatuscallback = $statuscallback."&recordingstatuscallback=true";

        $this->outgoing_rule($to);
        $caller_id = $this->twilio_caller_id;

        $twilio = new Client($sid, $token);
        
        $this->twilio_logs("TWILIO CALL BEFORE TRIGGER ".json_encode($twilio)."\n");

        $call = $twilio->calls
        ->create($sip, // to
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

    function outgoing_call()
    {        
        $this->twilio_logs("OUTGOING CALL TRIGGER ".json_encode($_REQUEST)."\n");

        $this->bitrix_validate_request();

        $to = $_REQUEST['data']['PHONE_NUMBER'];        
        $param = array(
            'bitrix_domain'=>$_REQUEST['auth']['domain'],
            'bitrix_call_id'=>$_REQUEST['data']['CALL_ID'],
            'bitrix_user_id'=>$_REQUEST['data']['USER_ID']
        );
        $param = http_build_query($param);
        $this->twilio_api($to,$param);
    }

    function outgoing_callback()
    {
        $this->twilio_logs("OUTGOING CALLBACK TRIGGER ".json_encode($_REQUEST)."\n");   
        $this->twilio_validate_request();

        $row['user_id'] = $_REQUEST['bitrix_user_id'];
        $row['call_id'] = $_REQUEST['bitrix_call_id'];        
        
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

    function attach_recording($row)
    {
        $attachResult = $this->bitrix_execution("POST", "telephony.externalCall.attachRecord", array(
            "CALL_ID" => $row['call_id'],
            "FILENAME" => "twiliorecording_".strtotime("now").".wav",
            "RECORD_URL" => $row['recordingurl'],
        ));               

        $this->twilio_logs("BITRIX RECORDING API RESPONSE ".json_encode($attachResult)."\n");
    }

    function bitrix_finish_api($row)
    {                
        $finishResult = $this->bitrix_execution("POST", "telephony.externalCall.finish", array(
            "USER_ID" => $row['user_id'],
            "CALL_ID" => $row['call_id'],
            "DURATION" => intval($row['duration']),
            "STATUS_CODE" => ($row['disposition'] != "completed") ? "603-S" : "200",           
        ));

        $this->twilio_logs("BITRIX FINISH API RESPONSE ".json_encode($finishResult)."\n");
        
    }

    function bitrix_execution($method_type, $method_name, $data = null)
    {        
        $url = $this->twilio_bitrix_webhook.$method_name;        
    
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

    function twilio_logs($log)
    {
        $message = '['.date('Y-m-d H-i-s').'] => ';
        $message .= "IP: ".$this->getIp()." || ";
        $message .= json_encode($log);
        $message .= "\n";
        $dir = storage_path().'/logs/twilio/';
        $path = $dir."twiliodev.log";
        if(!is_dir($dir)) mkdir($dir);        
        file_put_contents($path,$message,FILE_APPEND);
    }

    function getIp(){
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    
}
