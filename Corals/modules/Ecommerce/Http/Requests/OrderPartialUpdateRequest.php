<?php

namespace Corals\Modules\Ecommerce\Http\Requests;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\Ecommerce\Models\Order;

class OrderPartialUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return user()->hasPermissionTo('Ecommerce::order.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->setModel(Order::class);

        if (!$this->is('*notify-buyer') && ($this->isStore() || $this->isUpdate())) {
            $rules = parent::rules();

            if($this->is('*status')){
                $rules = array_merge($rules, [
                    'status' => 'required',
                ]);
            }

            return $rules;
        }

        return [];
    }
}
