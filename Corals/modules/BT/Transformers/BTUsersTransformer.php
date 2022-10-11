<?php

namespace Corals\Modules\BT\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\BT\Models\BTUsers;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\User\Models\User;

class BTUsersTransformer extends BaseTransformer
{
    public function __construct()
    {
        $this->resource_url = config('bt.models.user.resource_url');

        parent::__construct();
    }

    /**
     * @param BTUsers $bitrixtelephony
     * @return array
     * @throws \Throwable
     */
    public function transform(BTUsers $bitrixtelephony)
    {
        $show_url = url($this->resource_url . '/' . $bitrixtelephony->hashed_id);
        $user = User::where('id', $bitrixtelephony->user_id)->first();
	$config = BitrixTelephony::where('user_id', $bitrixtelephony->user_id)->first();

	$actions['toggleSyncStatus'] = [
            'href' => url('/bt_users/' . $bitrixtelephony->hashed_id . '/toggleSyncStatus'),
	        'class' => 'text-center btn-success',
            'label' => trans('Toggle Sync Status'),
        ];

	$actions['sendPasswordMail'] = [
            'href' => url('/bt_users/' . $bitrixtelephony->hashed_id . '/sendPasswordMail'),
            'class' => 'text-center btn-success',
            'label' => trans('Send Password Mail'),
        ];

	$actions['addPassword'] = [
            'href' => url('/bt_users/' . $bitrixtelephony->hashed_id . '/addPassword'),
            'class' => 'text-center btn-success',
            'label' => trans('Add Password'),
        ];

	$userActions = [];

        if ($bitrixtelephony->sync_status) {

	$userActions['editSoftphonePassword'] = [
          'href' => url('/bt_users/' . $bitrixtelephony->hashed_id . '/editSoftphonePassword'),
          'class' => 'text-center btn-success',
          'label' => trans('Change Softphone Password'),
        ];

	}

    $isValid = false;
    foreach (user()->roles as $role) {
        if($role->name == 'operations') {
            $isValid = true;
        }
    }

	if(isSuperUser() || $isValid) {

        $transformedArray= [
            'id' => $bitrixtelephony->id,
            'name' => HtmlElement('a', ['href' => $bitrixtelephony->getShowURL()], $bitrixtelephony->name),
            'user' => $user->name . " " . $user->last_name,
            // Dialplug Fields
            'bitrix_user_name' => $bitrixtelephony->bitrix_user_name,
            'is_default' => $bitrixtelephony->is_default ? 'Yes' : 'No',
	    'sip_uri' => strtolower($user->name) . "-"  . $user->id . ".dialplug.com:5060",
            'inbound_route' => $bitrixtelephony->inbound_route,
	    'company_name' => $bitrixtelephony->company_name,
	    'sync_status' => $bitrixtelephony->sync_status
              ? '<span class="label label-success">Successful</span>'
              : '<span class="label label-danger">Under Review</span>',
            'action' => $this->actions($bitrixtelephony, $actions)
        ];
	} else {
	$transformedArray= [
            'id' => $bitrixtelephony->id,
            'name' => HtmlElement('a', ['href' => $bitrixtelephony->getShowURL()], $bitrixtelephony->name),
            // Dialplug Fields
            'bitrix_user_name' => $bitrixtelephony->bitrix_user_name,
            'is_default' => $bitrixtelephony->is_default ? 'Yes' : 'No',
	    'sip_uri' => strtolower($user->name) . "-"  . $user->id . ".dialplug.com:5060",
            'inbound_route' => $bitrixtelephony->inbound_route,
	    'sync_status' => $bitrixtelephony->sync_status
              ? '<span class="label label-success">Successful</span>'
              : '<span class="label label-danger">Under Review</span>',
            'action' => $this->actions($bitrixtelephony, $userActions)
        ];
	}

        return parent::transformResponse($transformedArray);
    }
}
