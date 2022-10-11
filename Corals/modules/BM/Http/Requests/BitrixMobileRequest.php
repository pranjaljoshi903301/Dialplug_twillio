<?php

namespace Corals\Modules\BM\Http\Requests;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\BM\Models\BitrixMobile;

class BitrixMobileRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->setModel(BitrixMobile::class);

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
        $this->setModel(BitrixMobile::class);
        $rules = parent::rules();

        if ($this->isUpdate() || $this->isStore()) {
            $rules = array_merge($rules, [
            ]);
        }

        if ($this->isStore()) {
            $rules = array_merge($rules, [
                'mobile_number' => 'unique:bm_config,mobile_number',
            ]);
        }

        if ($this->isUpdate()) {
            if (!isSuperUser()) {

                $bm_config = BitrixMobile::where('user_id', user()->id)->first();
    
                $rules = array_merge($rules, [
                    'mobile_number' => 'unique:bm_config,mobile_number,' . $bm_config->user_id,
                ]);
            }
        }

        return $rules;
    }
}
