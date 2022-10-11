<?php

namespace Corals\Modules\BT\Http\Controllers;

use Corals\Foundation\Facades\Hashids;
use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\BT\DataTables\BitrixTelephonyDataTable;
use Corals\Modules\BT\Http\Requests\BitrixTelephonyRequest;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Models\BTUsers;
use Corals\Modules\BT\Services\BitrixTelephonyService;
use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Settings\Models\Setting;
use Illuminate\Http\Request;
use Corals\User\Models\User;
use Illuminate\Support\Facades\Mail;

class BitrixTelephonyController extends BaseController
{
    protected $bitrixtelephonyService;

    public function __construct(BitrixTelephonyService $bitrixtelephonyService)
    {
        $this->bitrixtelephonyService = $bitrixtelephonyService;

        $this->resource_url = config('bt.models.bitrixtelephony.resource_url');
        $this->dashboard_url = config('bt.models.dashboard.resource_url');

        $this->title = trans('BT::module.bitrixtelephony.title');
        $this->title_singular = trans('BT::module.bitrixtelephony.title_singular');

        parent::__construct();
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephonyDataTable $dataTable
     * @return mixed
     */

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
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if (isSuperUser()) {
            $isValid = true;
        }

        return $isValid;
    }

    public function index(BitrixTelephonyRequest $request, BitrixTelephonyDataTable $dataTable)
    {
        if (!$this->checkSubscription()) {
            flash("Your Bitrix Telephony Subscription is not active yet")->info();
            return redirectTo($this->dashboard_url);
        }

        return $dataTable->render('BT::bitrixtelephony.index');
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(BitrixTelephonyRequest $request)
    {        
        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if (isSuperUser() || !(BitrixTelephony::where('user_id', user()->id)->first()) || $isValid) {
            $bitrixtelephony = new BitrixTelephony();
            $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);
            return view('BT::bitrixtelephony.create_edit')->with(compact('bitrixtelephony'));
        } else {
            flash("Config Already Created")->info();
            return redirectTo($this->resource_url);
        }
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(BitrixTelephonyRequest $request)
    {
        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        try {
            if (!isSuperUser() && !$isValid) {
                $request['user_id'] = user()->id;
                $request['freepbx_domain'] = user()->name.'-'.user()->id;
            }else{
                $request['freepbx_domain'] = User::find($request['user_id'])->name.'-'.$request['user_id'];
            }
            $request['webhook_url'] = str_replace("/profile", "", $request['webhook_url']);

            if (substr($request['webhook_url'], -1) !== '/') {
                $request['webhook_url'] .= '/';
            }            
            $bitrixtelephony = $this->bitrixtelephonyService->store($request, BitrixTelephony::class);

            if (!isSuperUser()) {
                $user_email = 'service-desk@dialplug.com';
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                    'body' => user()->name . " " . user()->last_name . ' has Created his/her Bitrix Telephony Config',
                    'webhook_url' => $request->webhook_url,
                    'logged_user_id' => user()->id,

                );
                Mail::send('email.btEmailTemplate', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Bitrix Telephony Config Update');
                    $emailMessage->to($user_email);
                });
            }
            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, BitrixTelephony::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephony $bitrixtelephony
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(BitrixTelephonyRequest $request, BitrixTelephony $bitrixtelephony)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $bitrixtelephony->getIdentifier()])]);

        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $bitrixtelephony->hashed_id . '/edit']);

        return view('BT::bitrixtelephony.show')->with(compact('bitrixtelephony'));
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephony $bitrixtelephony
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(BitrixTelephonyRequest $request, BitrixTelephony $bitrixtelephony)
    {

        $id = Hashids::decode($request->route('bt_config'))[0];
        $bitrixtelephony = BitrixTelephony::where('id', $id)->first();

        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        if (isSuperUser() || ($bitrixtelephony->user_id == user()->id) || $isValid) {
            $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $bitrixtelephony->getIdentifier()])]);
            return view('BT::bitrixtelephony.create_edit')->with(compact('bitrixtelephony'));
        } else {
            flash("Not Authorised")->warning();
            return redirectTo($this->resource_url);
        }
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephony $bitrixtelephony
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(BitrixTelephonyRequest $request, BitrixTelephony $bitrixtelephony)
    {
        try {
            $id = Hashids::decode($request->route('bt_config'))[0];
            $bitrixtelephony = BitrixTelephony::where('id', $id)->first();

            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
            if (!isSuperUser() && $bitrixtelephony['user_id'] != user()->id && !$isValid) {
                $message = ['level' => 'error', 'message' => 'Not Authorised'];
                return response()->json($message);
            }
            $request['webhook_url'] = str_replace("/profile", "", $request['webhook_url']);

            if (substr($request['webhook_url'], -1) !== '/') {
                $request['webhook_url'] .= '/';
            }
            $this->bitrixtelephonyService->update($request, $bitrixtelephony);
            if (!isSuperUser()) {
                $user_email = 'service-desk@dialplug.com';
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                    'body' => user()->name . " " . user()->last_name . ' has Edited his/her Bitrix Telephony Config',
                    'webhook_url' => $bitrixtelephony->webhook_url,
                    'logged_user_id' => user()->id,

                );
                Mail::send('email.btEmailTemplate', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Bitrix Telephony Config Update');
                    $emailMessage->to($user_email);
                });
            }
            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();

        } catch (\Exception $exception) {
            log_exception($exception, BitrixTelephony::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BitrixTelephonyRequest $request
     * @param BitrixTelephony $bitrixtelephony
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BitrixTelephonyRequest $request, BitrixTelephony $bitrixtelephony)
    {
        try {
            $id = Hashids::decode($request->route('bt_config'))[0];
            $bitrixtelephony = BitrixTelephony::where('id', $id)->first();
            $isValid = false;
            foreach (user()->roles as $role) {
                if($role->name == 'operations') {
                    $isValid = true;
                }
            }
            if (!isSuperUser() && $bitrixtelephony['user_id'] != user()->id && !$isValid) {
                $message = ['level' => 'error', 'message' => 'Not Authorised'];
                return response()->json($message);
            }
            $webhook_url = $bitrixtelephony->webhook_url;
            $this->bitrixtelephonyService->destroy($request, $bitrixtelephony);
            if (!isSuperUser()) {
                $user_email = 'service-desk@dialplug.com';
                $domain = env('APP_URL');
                $maildata = array(
                    'domain' => $domain,
                    'name' => user()->name . " " . user()->last_name,
                    'body' => user()->name . " " . user()->last_name . ' has Deleted his/her Bitrix Telephony Config',
                    'webhook_url' => $webhook_url,
                    'logged_user_id' => user()->id,

                );
                Mail::send('email.btEmailTemplate', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                    $emailMessage->subject('Bitrix Telephony Config Update');
                    $emailMessage->to($user_email);
                });
            }
            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, BitrixTelephony::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }
    public function toggleSetupStatus(BitrixTelephonyRequest $request, $id) {
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
        $bitrixtelephony = BitrixTelephony::where('id', $id)->first();
        BitrixTelephony::where('id', $id)->update(["setup_status" => $bitrixtelephony->setup_status ? 0 : 1]);

	$user = User::where('id', $bitrixtelephony->user_id)->first();

	if ($bitrixtelephony->setup_status == 0) {
	    $user_email = $user->email;
            $domain = env('APP_URL');
            $maildata = array(
                'domain' => $domain . '/dashboard',
                'name' => $user->name . " " . $user->last_name,
            );
            Mail::send('BT::bitrixtelephony.emails.setup_successfull', $maildata, function ($emailMessage) use ($maildata, $user_email) {
                $emailMessage->subject('Dialplug Setup Successful');
                $emailMessage->to($user_email);
            });
	}

        flash("Setup Status Updated Successfully")->success();
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
        $bitrixtelephony = BitrixTelephony::where('id', $id)->first();
	if (!$bitrixtelephony->phone_number) {
          flash("Please Fill Phone Number First")->warning();
          return redirectTo($this->resource_url);
        }
        $bt_users = BTUsers::where('user_id', $bitrixtelephony->user_id)->get();
        $user = User::where('id', $bitrixtelephony->user_id)->first();

	$phone_number = $bitrixtelephony->phone_number;

        $users_list = [];

        foreach ($bt_users as $bt_user) {
            if (!$bt_user->freepbx_password) {
                flash("Please Fill Password for every user")->warning();
                return redirectTo($this->resource_url);
            } else {
                $users_list[strval($bt_user->id)] = array(
                    'bitrix_username' => $bt_user->bitrix_user_name,
                    'password' => $bt_user->freepbx_password,
                    'extension' => $bt_user->inbound_route,
                    'dialplug_domain' => $bitrixtelephony->freepbx_domain,
                );
            }
        }

        // dd($users_list);

        $user_email = $user->email;
        $domain = env('APP_URL');
        $maildata = array(
            'domain' => $domain . '/dashboard',
            'name' => $user->name . " " . $user->last_name,
            'bt_users' => $users_list,
	    'phone_number' => $phone_number
        );
        Mail::send('BT::bitrixtelephony.emails.password', $maildata, function ($emailMessage) use ($maildata, $user_email) {
            $emailMessage->subject('Dialplug Dialer Login Credentials');
            $emailMessage->to($user_email);
        });

        flash("Password Mail Sent Successfully")->success();
        return redirectTo($this->resource_url);
    }

}

