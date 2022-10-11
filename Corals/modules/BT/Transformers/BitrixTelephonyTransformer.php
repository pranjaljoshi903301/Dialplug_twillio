<?php

namespace Corals\Modules\BT\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\User\Models\User;

class BitrixTelephonyTransformer extends BaseTransformer
{
    public function __construct()
    {
        $this->resource_url = config('bt.models.bitrixtelephony.resource_url');

        parent::__construct();
    }

    /**
     * @param BitrixTelephony $bitrixtelephony
     * @return array
     * @throws \Throwable
     */
    public function transform(BitrixTelephony $bitrixtelephony)
    {
        $show_url = url($this->resource_url . '/' . $bitrixtelephony->hashed_id);
        $user = User::where('id', $bitrixtelephony->user_id)->first();

	$actions['createUser'] = [
            'href' => url('/bt_users/' . $bitrixtelephony->hashed_id . '/createUser'),
	    'class' => 'text-center btn-success',
            'label' => trans('Create User'),
        ];

	$actions['toggleSetupStatus'] = [
            'href' => url('/bt_config/' . $bitrixtelephony->hashed_id . '/toggleSetupStatus'),
	    'class' => 'text-center btn-success',
            'label' => trans('Toggle Setup Status'),
        ];

	$actions['sendPasswordMail'] = [
            'href' => url('/bt_config/' . $bitrixtelephony->hashed_id . '/sendPasswordMail'),
            'class' => 'text-center btn-success',
            'label' => trans('Send Password Mail'),
        ];

        $isValid = false;
        foreach (user()->roles as $role) {
            if($role->name == 'operations') {
                $isValid = true;
            }
        }
        $transformedArray = [
            'id' => $bitrixtelephony->id,
            'name' => HtmlElement('a', ['href' => $bitrixtelephony->getShowURL()], $bitrixtelephony->name),

            // Dialplug Fields

            'user' => $user->name . " " . $user->last_name,
	    'freepbx_subdomain' => $bitrixtelephony->freepbx_domain,
	    'phone_number' => $bitrixtelephony->phone_number ? $bitrixtelephony->phone_number : 'Pending',
            'webhook_url' => $bitrixtelephony->webhook_url,
            'setup_status' => $bitrixtelephony->setup_status
            ? '<span class="label label-success">Successfull</span>'
            : '<span class="label label-danger">Under Review</span>',
	    'action' => (isSuperUser()||$isValid) ? $this->actions($bitrixtelephony, $actions) : $this->actions($bitrixtelephony),
        ];

        return parent::transformResponse($transformedArray);
    }
}
