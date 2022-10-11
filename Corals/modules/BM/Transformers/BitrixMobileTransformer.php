<?php

namespace Corals\Modules\BM\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\BM\Models\BitrixMobile;
use Corals\User\Models\User;

class BitrixMobileTransformer extends BaseTransformer
{
    public function __construct()
    {
        $this->resource_url = config('bm.models.bitrixmobile.resource_url');

        parent::__construct();
    }

    /**
     * @param BitrixMobile $bitrixmobile
     * @return array
     * @throws \Throwable
     */
    public function transform(BitrixMobile $bitrixmobile)
    {
        $show_url = url($this->resource_url . '/' . $bitrixmobile->hashed_id);
        
        $user = User::where('email', $bitrixmobile->email)->first();
        $username = "* ".$bitrixmobile->email;
        if($user){
            $username = $bitrixmobile->email;
        }
        $actions['dropimeibtn'] = [
            'href' => url('/bm_config/' . $bitrixmobile->hashed_id . '/manage_details'),
            'class' => 'text-center btn-default',
            'label' => trans('Manage Details'),
        ];
        if(isSuperUser())
        {
            $actions['deletebtn'] = [
                'href' => url('/bm_config/' . $bitrixmobile->hashed_id . '/delete_details'),
                'class' => 'text-center btn-default',
                'label' => trans('Delete')                
            ];  
        }
        $bitrixmobile_imei_obj = json_decode($bitrixmobile->agent_detail,true);
        if($bitrixmobile_imei_obj!='') $bitrixmobile_imei_obj=count($bitrixmobile_imei_obj);
        else $bitrixmobile_imei_obj=0;
        $transformedArray = [
            'id' => $bitrixmobile->id,            
            'user' => $username,            
            'webhook_url' => $bitrixmobile->webhook_url,
            'agent_count' => $bitrixmobile_imei_obj,
            'created_at' => date('d M Y',strtotime($bitrixmobile->created_at)),
            //'action' => $this->actions($bitrixmobile, $actions),
	    'action' => view('components.item_actions', ['actions' => $actions])->render(),
        ];    
        
        return parent::transformResponse($transformedArray);
    }
}
?>
