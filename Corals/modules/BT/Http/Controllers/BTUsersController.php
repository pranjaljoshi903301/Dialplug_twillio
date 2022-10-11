<?php

namespace Corals\Modules\BT\Http\Controllers;

use Corals\Foundation\Facades\Hashids;
use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\BT\DataTables\BTUsersDataTable;
use Corals\Modules\BT\Http\Requests\BTUsersRequest;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Models\BTUsers;
use Corals\Modules\BT\Services\BitrixTelephonyService;
use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Settings\Models\Setting;
use Corals\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class BTUsersController extends BaseController
{
    protected $bitrixtelephonyService;

    public function __construct(BitrixTelephonyService $bitrixtelephonyService)
    {
        $this->bitrixtelephonyService = $bitrixtelephonyService;

        $this->resource_url = config('bt.models.user.resource_url');
        $this->dashboard_url = config('bt.models.dashboard.resource_url');

        $this->title = trans('BT::module.user.title');
        $this->title_singular = trans('BT::module.user.title_singular');

        parent::__construct();
    }

    /**
     * @param BTUsersRequest $request
     * @param BTUsersDataTable $dataTable
     * @return mixed
     */

    public function checkProductInfo()
    {
        $isValid = false;
        $plan_id = null;
        $subscriptions = Subscription::where('user_id', user()->id)->where('status', 'active')->get();
        $product_setting = Setting::where('code', 'bt_product_id')->first();
        $feature_setting = Setting::where('code', 'bt_feature_id')->first();
        if (!$product_setting || !$feature_setting) {
            $isValid = false;
            return $isValid;
        }

        $featureID = (int) $feature_setting->value;
        foreach ($subscriptions as $subscription) {
            $plan = Plan::where('id', $subscription->plan_id)->first();
            if ($plan->product_id == (int) $product_setting->value) { // Static Product ID
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

    public function checkAgentsLimit($users_limit)
    {
        $isValid = false;
        $users = BTUsers::where('user_id', user()->id)->count();

        if ($users < (int) $users_limit) {
            $isValid = true;
        }

        return $isValid;
    }

    public function checkSubscription()
    {
        $isValid = false;
        $subscriptions = Subscription::where('user_id', user()->id)->where('status', 'active')->get();
        $product_setting = Setting::where('code', 'bt_product_id')->first();
        if (!$product_setting) {
            flash("Please contact Administrator regarding Product error")->info();
            return redirectTo($this->resource_url);
        }
        foreach ($subscriptions as $subscription) {
            $plan = Plan::where('id', $subscription->plan_id)->first();
            if ($plan->product_id == (int) $product_setting->value) { // Static Product ID
                $isValid = true;
            }
        }

        if (isSuperUser()) {
            $isValid = true;
        }else{
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
        }

        return $isValid;
    }

    public function index(BTUsersRequest $request, BTUsersDataTable $dataTable)
    {
        if (!$this->checkSubscription()) {
            flash("Your Bitrix Telephony Subscription is not active yet")->info();
            return redirectTo($this->dashboard_url);
        }
        $config = BitrixTelephony::where('user_id', user()->id)->first();

        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        
        if ($config || isSuperUser() || $isValid) {
            return $dataTable->render('BT::btusers.index');
        } else {            
            flash("Please create config first")->info();
            return redirectTo(config('bt.models.bitrixtelephony.resource_url'));
        }
    }

    /**
     * @param BTUsersRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(BTUsersRequest $request)
    {
        try {
            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
	        if (isSuperUser() || $isValid) {
                $id = Hashids::decode($request->route('id'));
                if (!$id) {
                    flash("Please create Bitrix Telephony User from Bitrix Config")->info();
                    return redirectTo(config('bt.models.bitrixtelephony.resource_url'));
                }
                $id = $id[0];
                $bitrixtelephony = BitrixTelephony::where('id', $id)->first();
                $user_id = $bitrixtelephony->user_id;
            } else {
                $user_id = user()->id;
            }

            if (!$user_id) {
                flash("User ID Not Defined")->warning();
                return redirectTo(config('bt.models.bitrixtelephony.resource_url'));
            }

            if (!isSuperUser() && !(BitrixTelephony::where('user_id', $user_id)->first())) {
                flash("Please create config first")->info();
                return redirectTo($this->resource_url);
            }
            $usersLimit = $this->checkProductInfo();

            if (!isSuperUser() && !$usersLimit) {
                flash("Please contact Administrator regarding Product error")->info();
                return redirectTo($this->resource_url);
            }

            if (!isSuperUser() && !$this->checkAgentsLimit($usersLimit)) {
                flash("You have reached the maximum users limit")->info();
                return redirectTo($this->resource_url);
            }

            $config = BitrixTelephony::where('user_id', $user_id)->first();
            $default_user = BTUsers::where('user_id', $user_id)->where('is_default', 1)->first();

            $bitrixtelephony = new BTUsers();

            $users_list = file_get_contents(
                $config->webhook_url . "user.get",
                false,
                stream_context_create([
                    'http' => [
                        'ignore_errors' => true,
                        'method' => 'GET',
                        'timeout' => 10,
                    ],
                ])
            );

            if (json_decode($users_list, true)) {
                if (array_key_exists('error', json_decode($users_list, true))) {
                    flash("Please Check your Webhook URL once")->info();
                    return redirectTo($this->resource_url);
                }
            }

            $users = [];
            foreach (json_decode($users_list)->result as $user) {
                if ($user->UF_PHONE_INNER) {
                    $users[$user->ID] = $user->NAME . " " . $user->LAST_NAME;
                }
            }
            $bitrixtelephony->users = $users;
            $bitrixtelephony->default_user = $default_user;
	    $bitrixtelephony->user_id = $user_id;


            $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);
        } catch (\Exception $exception) {
	    if ((strpos($exception->getMessage(), 'file_get_contents') !== false)) {
                flash("Please Check your Webhook URL once")->info();
                return redirectTo($this->resource_url);
            } else {
                log_exception($exception, BTUsers::class, 'store');
            }
        }

        return view('BT::btusers.create_edit')->with(compact('bitrixtelephony'));
    }

    /**
     * @param BTUsersRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(BTUsersRequest $request)
    {
        try {

            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
	        if (isSuperUser() || $isValid) {
                $user_id = $request['user_id'];
            } else {
                $user_id = user()->id;
            }

            if (!$user_id) {
                flash("User ID Not Defined")->info();
                return redirectTo(config('bt.models.bitrixtelephony.resource_url'));
            }

            if (!isSuperUser() && !(BitrixTelephony::where('user_id', $user_id)->first())) {
                flash("Please create config first")->info();
                return redirectTo($this->resource_url);
            }

            $usersLimit = $this->checkProductInfo();

            if (!isSuperUser() && !$usersLimit) {
                flash("Please contact Administrator regarding Product error")->info();
                return redirectTo($this->resource_url);
            }

            if (!isSuperUser() && !$this->checkAgentsLimit($usersLimit)) {
                flash("You have reached the maximum users limit")->info();
                return redirectTo($this->resource_url);
            }

	        $company = DB::select('select * from user_company where user_id = ?', [$user_id])[0];
            $company_name = $company ? $company->company_name : '';

            $config = BitrixTelephony::where('user_id', $user_id)->first();

            $request['user_id'] = $user_id;
	        $request['company_name'] = $company_name;

            $bitrix_user = json_decode(file_get_contents($config->webhook_url . "user.get/?ID=" . $request->bitrix_user_id))->result[0];
            $request['bitrix_user_name'] = $bitrix_user->NAME . " " . $bitrix_user->LAST_NAME;
            $request['inbound_route'] = $bitrix_user->UF_PHONE_INNER;

	        $request['is_default'] = $request['is_default'] ? 1 : 0;

            $bitrixtelephony = $this->bitrixtelephonyService->store($request, BTUsers::class);

            /*            
                if (!isSuperUser()) {
                $user_email = user()->email;
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                );
                Mail::send('email.registeringBtUser', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Creating Bitrix User');
                    $emailMessage->to($user_email);
                });
            }*/

            if (!isSuperUser()) {
                $user_email = 'service-desk@dialplug.com';
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                    'body' => user()->name . " " . user()->last_name . ' has Created his/her Bitrix Telephony User',
                    'webhook_url' => $config->webhook_url,
                    'logged_user_id' => user()->id,

                );
                Mail::send('email.btEmailTemplate', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Bitrix Telephony User Update');
                    $emailMessage->to($user_email);
                });
            }

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, BTUsers::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BTUsersRequest $request
     * @param BTUsers $bitrixtelephony
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(BTUsersRequest $request, BTUsers $bitrixtelephony)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $bitrixtelephony->getIdentifier()])]);

        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $bitrixtelephony->hashed_id . '/edit']);

        return view('BT::bitrixtelephony.show')->with(compact('bitrixtelephony'));
    }

    /**
     * @param BTUsersRequest $request
     * @param BTUsers $bitrixtelephony
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(BTUsersRequest $request, BTUsers $bitrixtelephony)
    {
        try {

            $id = Hashids::decode($request->route('bt_user'))[0];
            $bitrixtelephony = BTUsers::where('id', $id)->first();
	        $user_id = $bitrixtelephony->user_id;
            $config = BitrixTelephony::where('user_id', $user_id)->first();
            $default_user = BTUsers::where('user_id', $user_id)->where('is_default', 1)->first();

            $users_list = file_get_contents(
                $config->webhook_url . "user.get",
                false,
                stream_context_create([
                    'http' => [
                        'ignore_errors' => true,
                        'method' => 'GET',
                        'timeout' => 10,
                    ],
                ])
            );

            if (json_decode($users_list, true)) {
                if (array_key_exists('error', json_decode($users_list, true))) {
                    flash("Please Check your Webhook URL once")->info();
                    return redirectTo($this->resource_url);
                }
            }

            $users = [];
            foreach (json_decode($users_list)->result as $user) {
                if ($user->UF_PHONE_INNER) {
                    $users[$user->ID] = $user->NAME . " " . $user->LAST_NAME;
                }
            }
            $bitrixtelephony->users = $users;
            $bitrixtelephony->default_user = $default_user;

            $bitrixtelephony->default_user_id = $default_user ? $default_user->id : null;
            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
            if (isSuperUser() || ($bitrixtelephony->user_id == user()->id) || $isValid) {
                $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $bitrixtelephony->getIdentifier()])]);
                return view('BT::btusers.create_edit')->with(compact('bitrixtelephony'));
            } else {
                flash("Not Authorised")->warning();
                return redirectTo($this->resource_url);
            }
        } catch (\Exception $exception) {
	        if ((strpos($exception->getMessage(), 'file_get_contents') !== false)) {
                flash("Please Check your Webhook URL once")->info();
                return redirectTo($this->resource_url);
            } else {
                log_exception($exception, BTUsers::class, 'store');
            }
        }

    }

    /**
     * @param BTUsersRequest $request
     * @param BTUsers $bitrixtelephony
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(BTUsersRequest $request, BTUsers $bitrixtelephony)
    {
        try {

            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
	        if (isSuperUser() || $isValid) {
                $user_id = $request['user_id'];
            } else {
                $user_id = user()->id;
            }

            if (!$user_id) {
                flash("User ID Not Defined")->info();
                return redirectTo(config('bt.models.bitrixtelephony.resource_url'));
            }

            $id = Hashids::decode($request->route('bt_user'))[0];
            $bitrixtelephony = BTUsers::where('id', $id)->first();

            $config = BitrixTelephony::where('user_id', $user_id)->first();
            $bitrix_user = json_decode(file_get_contents($config->webhook_url . "user.get/?ID=" . $request->bitrix_user_id))->result[0];

            $request['bitrix_user_name'] = $bitrix_user->NAME . " " . $bitrix_user->LAST_NAME;
            $request['inbound_route'] = $bitrix_user->UF_PHONE_INNER;
            $bitrixtelephony['is_default'] = $request->is_default ? 1 : 0;
	        $request['is_default'] = $request->is_default ? 1 : 0;
	        $company = DB::select('select * from user_company where user_id = ?', [$user_id])[0];
            $company_name = $company ? $company->company_name : '';
            $request['company_name'] = $company_name;

            if (!isSuperUser() && ($bitrixtelephony->user_id != $user_id) && !$isValid) {
                $message = ['level' => 'error', 'message' => 'Not Authorised'];
                return response()->json($message);
            }

            $this->bitrixtelephonyService->update($request, $bitrixtelephony);

            if (!isSuperUser()) {
                $user_email = user()->email;
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                );
                Mail::send('email.registeringBtUser', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Editing Bitrix User');
                    $emailMessage->to($user_email);
                });
            }

            /*            
                if (!isSuperUser()) {
                $user_email = 'service-desk@dialplug.com';
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                    'body' => user()->name . " " . user()->last_name . ' has Edited his/her Bitrix Telephony User',
                    'webhook_url' => $config->webhook_url,
                    'logged_user_id' => user()->id,

                );
                Mail::send('email.btEmailTemplate', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Bitrix Telephony User Update');
                    $emailMessage->to($user_email);
                });
            }*/

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, BTUsers::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BTUsersRequest $request
     * @param BTUsers $bitrixtelephony
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BTUsersRequest $request, BTUsers $bitrixtelephony)
    {
        try {
            $id = Hashids::decode($request->route('bt_user'))[0];
            $bitrixtelephony = BTUsers::where('id', $id)->first();

            $config = BitrixTelephony::where('user_id', user()->id)->first();

            if (!isSuperUser() && ($bitrixtelephony->user_id != user()->id)) {
                $message = ['level' => 'error', 'message' => 'Not Authorised'];
                return response()->json($message);
            }

            $this->bitrixtelephonyService->destroy($request, $bitrixtelephony);

            /*            
            if (!isSuperUser()) {
                $user_email = user()->email;
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                    'body' => user()->name . " " . user()->last_name . ' has Deleted his/her Bitrix Telephony User',
                    'webhook_url' => $config->webhook_url,
                    'logged_user_id' => user()->id,

                );
                Mail::send('email.btEmailTemplate', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Bitrix Telephony User Update');
                    $emailMessage->to($user_email);
                });
            }*/

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, BTUsers::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

    public function editSoftphonePassword(BTUsersRequest $request, $id)
    {
      $id = Hashids::decode($id)[0];
      $bitrixtelephony = BTUsers::where('id', $id)->first();

        if (!$bitrixtelephony->sync_status) {
          flash("User not approved yet")->warning();
          return redirectTo($this->resource_url);
        }

      if (isSuperUser() || user()->id !== (int) $bitrixtelephony->user_id) {
        flash("Not Authorised")->warning();
        return redirectTo($this->resource_url);
      }
      return view('BT::btusers.edit_softphone_password')->with(compact('bitrixtelephony'));
    }

    public function addPassword(BTUsersRequest $request, $id)
    {
        $id = Hashids::decode($id)[0];
        $bitrixtelephony = BTUsers::where('id', $id)->first();
        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if (!isSuperUser() && !$isValid) {
            flash("Not Authorised")->warning();
            return redirectTo($this->resource_url);
        }
        return view('BT::btusers.add_password')->with(compact('bitrixtelephony'));
    }

    public function storePassword(Request $request, $id)
    {
        try {
            $id = Hashids::decode($id)[0];
            $bitrixtelephony = BTUsers::where('id', $id)->first();
            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
            if (!isSuperUser() && !$isValid) {
                flash("Not Authorised")->warning();
                return redirectTo($this->resource_url);
            }
            if ($request->password !== $request->confirm_password) {
                flash("Password Doesnt match")->warning();
                $url = "/bt_users/$bitrixtelephony->hashed_id/addPassword";
                return redirectTo($url);
            }
            BTUsers::where('id', $id)->update(['freepbx_password' => $request->password]);
            flash("Password Added Successfully")->success();
            return redirectTo($this->resource_url);
        } catch (\Exception $exception) {
            log_exception($exception, BTUsers::class, 'storePassword');
        }
    }

    public function updateSoftphonePassword(Request $request, $id)
    {
      $id = Hashids::decode($id)[0];
      $bitrixtelephony = BTUsers::where('id', $id)->first();

      if (!$bitrixtelephony->sync_status) {
          flash("User not approved yet")->warning();
          return redirectTo($this->resource_url);
        }

      if (isSuperUser() || user()->id !== (int) $bitrixtelephony->user_id) {
        flash("Not Authorised")->warning();
        return redirectTo($this->resource_url);
      }
      if ($request->password !== $request->confirm_password) {
        flash("Password Doesnt match")->warning();
        $url = "/bt_users/$bitrixtelephony->hashed_id/editSoftphonePassword";
        return redirectTo($url);
      }
      if (strlen($request->password) < 6 || !$request->password) {
        flash("Password must be 6 characters long")->warning();
        $url = "/bt_users/$bitrixtelephony->hashed_id/editSoftphonePassword";
        return redirectTo($url);
      }

      $subdomain = strtolower(user()->name) . "-" . user()->id;
      $url = "https://$subdomain.dialplug.com/manage_extension.php";

	$headers = @get_headers("https://$subdomain.dialplug.com");
        if(!$headers || !strpos( $headers[0], '302')) {
                flash("Something Went Wrong")->warning();
        return redirectTo($this->resource_url);
        }

      $data = array('id' => $bitrixtelephony->inbound_route, 'password' => $request->password);

      $options = array(
          'http' => array(
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'method'  => 'POST',
              'content' => http_build_query($data),
          )
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($url, false, $context);

      if ($result == "") {
        flash("Password Changed Successfully")->success();
        return redirectTo($this->resource_url);
      }
      else {
        flash("Something Went Wrong")->warning();
        return redirectTo($this->resource_url);
      }

    }

    public function toggleSyncStatus(BTUsersRequest $request, $id) {
        if (!isSuperUser()) {
            flash("Not Authorised")->warning();
            return redirectTo($this->resource_url);
        }
        $id = Hashids::decode($id)[0];
        $bitrixtelephony = BTUsers::where('id', $id)->first();
        BTUsers::where('id', $id)->update(["sync_status" => $bitrixtelephony->sync_status ? 0 : 1]);
        flash("Sync Status Updated Successfully")->success();
        return redirectTo($this->resource_url);
    }

    public function sendPasswordMail(Request $request, $id)
    {
        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if (!isSuperUser() && !$isValid) {
            flash("Not Authorised")->warning();
            return redirectTo($this->resource_url);
        }
        $id = Hashids::decode($id)[0];
        $bt_user = BTUsers::where('id', $id)->first();

        if (!$bt_user->freepbx_password) {
          flash("Please Fill Password for user")->warning();
          return redirectTo($this->resource_url);
        }

        $bitrixtelephony = BitrixTelephony::where('user_id', $bt_user->user_id)->first();
        $user = User::where('id', $bitrixtelephony->user_id)->first();
        $phone_number = $bitrixtelephony->phone_number;

        if (!$bitrixtelephony->phone_number) {
          flash("Please Fill Phone Number First")->warning();
          return redirectTo($this->resource_url);
        }

        $bt_user['dialplug_domain'] = $bitrixtelephony->freepbx_domain;
        $bt_user['bitrix_username'] = $bt_user->bitrix_user_name;
        $bt_user['password'] = $bt_user->freepbx_password;
        $bt_user['extension'] = $bt_user->inbound_route;

        $user_email = $user->email;
        $domain = env('APP_URL');
        $maildata = array(
            'domain' => $domain . '/dashboasrd',
            'name' => $user->name . " " . $user->last_name,
            'bt_user' => $bt_user,
            'phone_number' => $bitrixtelephony->phone_number,
        );
        Mail::send('BT::btusers.emails.password', $maildata, function ($emailMessage) use ($maildata, $user_email) {
            $emailMessage->subject('Dialplug Dialer Login Credentials');
            $emailMessage->to($user_email);
        });

        flash("Password Mail Sent Successfully")->success();
        return redirectTo($this->resource_url);
    }
}

