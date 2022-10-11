<?php

namespace Corals\Modules\BT\Http\Requests;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\BT\Models\BitrixTelephony;

class BitrixTelephonyRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->setModel(BitrixTelephony::class);

        // return $this->isAuthorized();
        return true; // For Showing Data To Members
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->setModel(BitrixTelephony::class);
        $rules = parent::rules();

        // if ($this->isUpdate() || $this->isStore()) {

        //     if (isSuperUser()) {
        //         $rules = array_merge($rules, [
        //             'bitrix_url' => 'required',
        //             'user_id' => 'required',
        //             'webhook_url' => 'required',
        //             'application_id' => 'required',
        //             'secret_key' => 'required',
        //         ]);

        //     } else {
        //         $bt_config = BitrixTelephony::where('user_id', user()->id)->first();
        //         $rules = array_merge($rules, [
        //             'bitrix_url' => 'required|unique:bt_config,bitrix_url,' . $bt_config->id,
        //             'webhook_url' => 'required',
        //             'application_id' => 'required',
        //             'secret_key' => 'required',
        //         ]);
        //     }
        // }

        if ($this->isStore()) {
            if (isSuperUser()) {
                $rules = array_merge($rules, [
                    'user_id' => 'required',
                    'webhook_url' => 'required|unique:bt_config,webhook_url',
                ]);
            } else {
                $rules = array_merge($rules, [
                    'webhook_url' => 'required|unique:bt_config,webhook_url',
                ]);
            }
        }

        if ($this->isUpdate()) {
            if (!isSuperUser()) {
                if (isSuperUser()) {
                    $rules = array_merge($rules, [
                        'user_id' => 'required',
                        'webhook_url' => 'required',
                    ]);

                } else {
                    $bt_config = BitrixTelephony::where('user_id', user()->id)->first();
                    $rules = array_merge($rules, [
                        'webhook_url' => 'required|unique:bt_config,webhook_url,' . $bt_config->id,
                    ]);
                }

            }
        }
        return $rules;
    }
}
