<?php

namespace Corals\Modules\BT\Http\Requests;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\BT\Models\BTUsers;

class BTUsersRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->setModel(BTUsers::class);

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
        $this->setModel(BTUsers::class);
        $rules = parent::rules();

        if ($this->isUpdate() || $this->isStore()) {
            if(isSuperUser()) {
                $rules = array_merge($rules, [
                    'user_id' => 'required',
                    'bitrix_user_id' => 'required',
                ]);
            } else {
                $rules = array_merge($rules, [
                    'bitrix_user_id' => 'required',
                ]);

            }
        }

        // if ($this->isStore()) {
        //     if(isSuperUser()) {
        //         $rules = array_merge($rules, [
        //             'user_id' => 'required',
        //             'bitrix_user_id' => 'required',
        //         ]);
        //     } else {
        //         $rules = array_merge($rules, [
        //             'bitrix_user_id' => 'required',
        //         ]);
        //     }
        // }

        // if ($this->isUpdate()) {
        //     $bitrixtelephony = $this->route('bitrixtelephony');
        //     if(isSuperUser()) {
        //         $rules = array_merge($rules, [
        //             'user_id' => 'required',
        //             'bitrix_user_id' => 'required',
        //         ]);
        //     } else {
        //         $rules = array_merge($rules, [
        //             'bitrix_user_id' => 'required',
        //         ]);
        //     }

        // }

        return $rules;
    }
}
