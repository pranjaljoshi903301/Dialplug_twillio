<?php

namespace Corals\Modules\BM\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\BM\Services\BitrixMobileService;
use Corals\Modules\Exotel\Services\BarService;
use Corals\Modules\BM\Transformers\API\BitrixMobilePresenter;
use Illuminate\Http\Request;
use Corals\Modules\Exotel\Http\Controllers\API\BarsController as Exotel;
use Corals\Modules\Exotel\Models\Bar as ExotelDetails;
use Corals\Modules\Exotel\Models\AgentDetail;
class CloudCallController extends APIBaseController
{
    //
    protected $API_KEY = 'yJRovJMwGB7sf8Pn5S0VnMSr2nGA0KdyoiIl1Lb0';

    public function __construct(BitrixMobileService $bitrixmobileService)
    {        
        
    }

    public function key_validation(Request $request)
    {
        if ($request->hasHeader('X-API-KEY')){
            $value = $request->header('X-API-KEY');
            if($value==$this->API_KEY)
                return true;            
        }
        return false;
    }
    
    public function cloud_call(Request $request)
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

        if($this->key_validation($request))
        {   
            if($request->has('subscriber_email') && $request->has('agent_email') && $request->has('webhook') && $request->has('phone_number'))
            {
                $subscriber_email = $request->get('subscriber_email');
                $agent_email = $request->get('agent_email');
                $webhook = $request->get('webhook');
                $phone_number = $request->get('phone_number');
                
                $exotelrequest = new Exotel(new BarService);
                                
                $result = ExotelDetails::where('subscriber_email',$subscriber_email)->first();
                if(!$result){
                    $api_response['message'] = "CloudCall Details not found. Please contact support.!";
                    return response($api_response, 400);
                }

                $agent_detail = AgentDetail::where([['subscriber_id',$result->id],['agent_email',$agent_email]])->first();
                if(!$agent_detail){
                    $api_response['message'] = "Agent details not found. Please contact support.!";
                    return response($api_response, 400);
                }

                $user_id = $agent_detail->agent_bitrix_id;
                $bitrix_domain = $result->bitrix_domain;
                
                $response = $exotelrequest->get_access_token($result->refresh_token);
                if($response['is_valid']==false){                    
                    $api_response['message'] = "Access Token Error. Please contact support.!";
                    return response($api_response, 400);
                }
                $access_token = $response['access_token'];
                $refresh_token = $response['refresh_token'];
                $result->access_token = $access_token;                
                $result->refresh_token = $refresh_token;
                $result->save();
                
                $url = "https://$bitrix_domain/rest/user.get?ID=$user_id&auth=$access_token";
                
                $extension = file_get_contents($url);                
                $extension = json_decode($extension,true);

                $extension = isset($extension['result'][0]['UF_PHONE_INNER'])?$extension['result'][0]['UF_PHONE_INNER']:false;

                if($extension==false){
                    $api_response['message'] = "Agent Extension not found. Please contact support.!";
                    return response($api_response, 400);                       
                }
                
                $call_id = $this->register_bitrix_call_card($bitrix_domain,$access_token,$user_id,$phone_number,$extension);                
                if($call_id==false)
                {
                    $api_response['code'] = 412;
                    $api_response['message'] = 'Something went wrong. Please contact support.';
                    return response($api_response, 412);                        
                }
                $_REQUEST['event'] = true;                
                $_REQUEST['data'] = array(
                    'USER_ID'=> $user_id,
                    'PHONE_NUMBER'=> $phone_number,
                    'CALL_ID' => $call_id
                );
                $_REQUEST['auth'] = array('domain' => $bitrix_domain);
                return $exotelrequest->click_to_call();
            }
            else
            {
                $api_response['code'] = 412;
                $api_response['message'] = "Insufficient field.!";
                return response($api_response, 412);    
            }

        }
        else
        {
            $api_response['code'] = 403;
            $api_response['message'] = "Invalid API Key.!";
            return response($api_response, 403);
        }
    }

    public function register_bitrix_call_card($bitrix_domain,$access_token,$user_id,$phone_number,$extension)
    {        
        $url = 'https://'.$bitrix_domain.'/rest/telephony.externalCall.register?auth='.$access_token;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "PHONE_NUMBER" : "'.$phone_number.'",
                "USER_ID" : "'.$user_id.'",
                "USER_PHONE_INNER": "'.$extension.'",
                "TYPE" : 1,
                "SHOW" : "1",
                "CRM_CREATE" : "1"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: BITRIX_SM_SALE_UID=5'
            ),
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);        
        $response = json_decode($response,true);

        return isset($response['result']['CALL_ID'])?$response['result']['CALL_ID']:false;

    }


}
