<?php

namespace Corals\Modules\BM\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\BM\DataTables\BitrixMobileDataTable;
use Corals\Modules\BM\Http\Requests\BitrixMobileRequest;
use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\Subscriptions\Models\Feature;
use Illuminate\Support\Facades\DB;
use Corals\Modules\BM\Services\BitrixMobileService;
use Corals\Foundation\Facades\Hashids;
use Corals\Settings\Models\Setting;
use Illuminate\Http\Request;

class BitrixMobileController extends BaseController
{
    protected $bitrixmobileService;

    public function __construct(BitrixMobileService $bitrixmobileService)
    {
        $this->bitrixmobileService = $bitrixmobileService;

        $this->resource_url = config('bm.models.bitrixmobile.resource_url');
        $this->dashboard_url = config('bm.models.dashboard.resource_url');

        $this->title = trans('BM::module.bitrixmobile.title');
        $this->title_singular = trans('BM::module.bitrixmobile.title_singular');

        parent::__construct();

    }

    /**
     * @param BitrixMobileRequest $request
     * @param BitrixMobileDataTable $dataTable
     * @return mixed
     */

    public function checkSubscription() {
        $isValid = false;
        $subscriptions = Subscription::where('user_id', user()->id)->where('status', 'active')->get();
        $product_setting = Setting::where('code', 'bm_product_id')->first();
        if (!$product_setting) {
            flash("Please contact Administrator regarding Product error")->success();
            return redirectTo($this->resource_url);
        }
        foreach ($subscriptions as $subscription) {
            $plan = Plan::where('id', $subscription->plan_id)->first();
            if ($plan->product_id == (int)$product_setting->value) { // Static Product ID
                $isValid = true;
            }
        }

        if (isSuperUser()) {
            $isValid = true;
        }
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }

        return $isValid;
    }

    public function checkProductInfo(){
        $isValid = false;
        $plan_id = null;
        $subscriptions = Subscription::where('user_id', user()->id)->where('status', 'active')->get();
        $product_setting = Setting::where('code', 'bm_product_id')->first();
        $feature_setting = Setting::where('code', 'bm_feature_id')->first();
        $feature = Feature::where('name', 'Number of Users')->first();
        if (!$product_setting) {
            flash("Please contact Administrator regarding Product error")->success();
            return redirectTo($this->resource_url);
        }
        if (!$feature_setting) {
            flash("Please contact Administrator regarding Feature error")->success();
            return redirectTo($this->resource_url);
        }
        $featureID = (int)$feature_setting->value;
        foreach ($subscriptions as $subscription) {
            $plan = Plan::where('id', $subscription->plan_id)->first();
            if ($plan->product_id == (int)$product_setting->value) { // Static Product ID
                $plan_id = $plan->id;
            }
        }
        if (!$plan_id) {
            $isValid = false;
            return $isValid;
        }
        $feature_plan = DB::select("select * from feature_plan where plan_id=$plan_id AND feature_id=$featureID");
        
        if (empty($feature_plan)) {
            $isValid = false;
            return $isValid;
        }
        $users_limit = $feature_plan[0]->value;
        if ($users_limit) {
            $isValid = $users_limit;
        }

        return $isValid;
    }

    public function checkAgentsLimit($users_limit) {
        $isValid = false;

        $users = BitrixMobile::where('user_id', user()->id)->count();

        if ($users < (int)$users_limit) {
            $isValid = true;
        }
        return $isValid;
    }

    public function index(BitrixMobileRequest $request, BitrixMobileDataTable $dataTable)
    {
        $user = BitrixMobile::where('email' , user()->email)->where('product_id', 5)->first();
        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if (!$this->checkSubscription()) {
            flash("Your Bitrix Mobile Subscription is not active yet")->warning();
            return redirectTo($this->dashboard_url);
        } else if(!$user && !isSuperUser() && !$isValid) {
	    flash("No user found, start making calls to manage users")->info();
            return redirectTo($this->dashboard_url);
	}

        return $dataTable->render('BM::bitrixmobile.index');
    }

    /**
     * @param BitrixMobileRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(BitrixMobileRequest $request)
    {
	
	flash("Not Authorized.!")->info();
        return redirectTo($this->resource_url);

        if (!$this->checkSubscription()) {
            flash("Your Bitrix Mobile Subscription is not active yet")->success();
            return redirectTo($this->dashboard_url);
        }

        $usersLimit = $this->checkProductInfo();

        if (!isSuperUser() && !$usersLimit) {
            flash("Please contact Administrator regarding Product error")->success();
            return redirectTo($this->resource_url);
        }

        if (!isSuperUser() && !$this->checkAgentsLimit($usersLimit)) {
            flash("You have reached the maximum users limit")->success();
            return redirectTo($this->resource_url);
        }

        $bitrixmobile = new BitrixMobile();
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);
        return view('BM::bitrixmobile.create_edit')->with(compact('bitrixmobile'));
    }

    /**
     * @param BitrixMobileRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(BitrixMobileRequest $request)
    {
        try {

            if (!$this->checkSubscription()) {
                flash("Your Bitrix Mobile Subscription is not active yet")->success();
                return redirectTo($this->dashboard_url);
            }

            $usersLimit = $this->checkProductInfo();

            if (!isSuperUser() && !$usersLimit) {
                flash("Please contact Administrator regarding Product error")->success();
                return redirectTo($this->resource_url);
            }

            if (!isSuperUser() && !$this->checkAgentsLimit($usersLimit)) {
                flash("You have reached the maximum users limit")->success();
                return redirectTo($this->resource_url);
            }

            if (!isSuperUser() && !$this->checkAgentsLimit($usersLimit)) {
                flash("You have reached the maximum users limit")->success();
                return redirectTo($this->resource_url);
            }

            if (!isSuperUser()) {
                $request['user_id'] = user()->id;
            }

            $bitrixmobile = $this->bitrixmobileService->store($request, BitrixMobile::class);
            
            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, BitrixMobile::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BitrixMobileRequest $request
     * @param BitrixMobile $bitrixmobile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(BitrixMobileRequest $request, BitrixMobile $bitrixmobile)
    {

        if (!$this->checkSubscription()) {
            flash("Your Bitrix Mobile Subscription is not active yet")->success();
            return redirectTo($this->dashboard_url);
        }

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $bitrixmobile->getIdentifier()])]);

        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $bitrixmobile->hashed_id . '/edit']);

        return view('BM::bitrixmobile.show')->with(compact('bitrixmobile'));
    }

    /**
     * @param BitrixMobileRequest $request
     * @param BitrixMobile $bitrixmobile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(BitrixMobileRequest $request, BitrixMobile $bitrixmobile)
    {

        if (!$this->checkSubscription()) {
            flash("Your Bitrix Mobile Subscription is not active yet")->success();
            return redirectTo($this->dashboard_url);
        }

        $id = Hashids::decode($request->route('bm_config'))[0];
        $bitrixmobile = BitrixMobile::where('id', $id)->first();
        if (isSuperUser() || ($bitrixmobile->user_id == user()->id)) {
            $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $bitrixmobile->getIdentifier()])]);
            return view('BM::bitrixmobile.create_edit')->with(compact('bitrixmobile'));
        } else {
            flash("Not Authorised")->success();
            return redirectTo($this->resource_url);
        }
    }

    /**
     * @param BitrixMobileRequest $request
     * @param BitrixMobile $bitrixmobile
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(BitrixMobileRequest $request, BitrixMobile $bitrixmobile)
    {
        try {

            if (!$this->checkSubscription()) {
                flash("Your Bitrix Mobile Subscription is not active yet")->success();
                return redirectTo($this->dashboard_url);
            }

            $id = Hashids::decode($request->route('bm_config'))[0];
            $bitrixmobile = BitrixMobile::where('id', $id)->first();

            if (!isSuperUser() && $bitrixmobile['user_id'] != user()->id) {
                $message = ['level' => 'error', 'message' => 'Not Authorised'];
                return response()->json($message);
            }

            $this->bitrixmobileService->update($request, $bitrixmobile);
            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();

        } catch (\Exception $exception) {
            log_exception($exception, BitrixMobile::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BitrixMobileRequest $request
     * @param BitrixMobile $bitrixmobile
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BitrixMobileRequest $request, BitrixMobile $bitrixmobile)
    {
        try {

            if (!$this->checkSubscription()) {
                flash("Your Bitrix Mobile Subscription is not active yet")->success();
                return redirectTo($this->dashboard_url);
            }

            $id = Hashids::decode($request->route('bm_config'))[0];
            $bitrixmobile = BitrixMobile::where('id', $id)->first();
            if (!isSuperUser() && $bitrixmobile['user_id'] != user()->id) {
                $message = ['level' => 'error', 'message' => 'Not Authorised'];
                return response()->json($message);
            }
            $this->bitrixmobileService->destroy($request, $bitrixmobile);

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, BitrixMobile::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
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
    public function webhook_validation($webhook,$subscriber_email)
    {
        // webhook is valid or not
        $flag['scope'] = false;
        $flag['exist'] = true;
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
                $flag['scope'] = true;
            }
        }
        
        $domain = $this->extractDomain($webhook);
        $local_instance = BitrixMobile::where([['email','!=',$subscriber_email],['domain',$domain],['product_id',5]])->first();                        
        if($local_instance) 
            $flag['exist'] = false;
        return $flag; 
    }

    public function update_details($id,Request $request)
    {        
        $id = Hashids::decode($id)[0];

        $isValid = false;
        foreach (user()->roles as $role) 
        {
            if($role->name == 'operations') 
                $isValid = true;            
        }
        
        if (!isSuperUser() && !$isValid)
            $bitrixmobile = BitrixMobile::where([['id',$id],['email',user()->email]])->first();
        else
            $bitrixmobile = BitrixMobile::find($id);
                          
        if(empty($bitrixmobile) || ((!$request->has('webhook_url'))&&(!$request->has('imei'))))
        {
            flash("Not Authorised")->warning();
            return redirectTo($this->resource_url);   
        }

        if($request->has('webhook_url'))
        {
            $webhook = trim($request->get('webhook_url'));
            $response = $this->webhook_validation($webhook,$bitrixmobile->email);
            if($response['scope']==false)
            {
                flash("Webhook with insufficient permission.")->warning();
                return redirectTo($this->resource_url);   
            }
            if($response['exist']==false)
            {
                flash("Webhook already exists with some other email.")->warning();                        
                return redirectTo($this->resource_url);   
            }
            $bitrixmobile->webhook_url = $webhook;
            $bitrixmobile->save();
            flash("Webhook updated successfully.")->success();
        }
        else
        {                            
            $imei_value = $request->imei;
            if($bitrixmobile->agent_detail==null)
            {
                flash("No agent found.!")->warning();
                return redirect('/bm_config');
            }        
            $agent_arr = json_decode($bitrixmobile->agent_detail,true);
            $result = [];
            foreach($agent_arr as $agent) {
                if($agent['imei']!=$imei_value) $result[] = $agent;
            }            
	        // $imei = array_values(array_diff($imei_arr, [$imei_value]));        
            if(count($result)==0)
                $bitrixmobile->agent_detail = null;
            else
                $bitrixmobile->agent_detail = json_encode($result);        
            $bitrixmobile->save();
            flash("IMEI deleted successfully.")->success();                    
        }
        return redirect('/bm_config');
    }
    public function manage_details($id)
    {        
        $id = Hashids::decode($id)[0];
        
        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if (!isSuperUser() && !$isValid) {
            $bitrixmobile = BitrixMobile::where([['id',$id],['email',user()->email]])->first();
        }
        else{
            $bitrixmobile = BitrixMobile::find($id);
        }
        if(empty($bitrixmobile)){
            flash("Not Authorised")->warning();
            return redirectTo($this->resource_url);              
        }
        
        return view('BM::bitrixmobile.manage_imei_edit')->with(compact('bitrixmobile'));
    }
    public function delete_details($id)
    {
        $id = Hashids::decode($id)[0];
        BitrixMobile::where('id',$id)->delete();
        flash("Record Deleted Successfully")->success();
        return redirectTo($this->resource_url);              
    }

}
