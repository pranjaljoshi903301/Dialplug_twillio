<?php

namespace Corals\Modules\Ecommerce\Http\Requests;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\Ecommerce\Models\Order;

class OrderRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->setModel(Order::class);

        return $this->isAuthorized();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->setModel(Order::class);
        $rules = parent::rules();

        if ($this->isUpdate() || $this->isStore()) {
            $rules = array_merge($rules, [
                'order_number' => 'required|unique:ecommerce_orders,order_number',
                'currency' => 'required',
                'status' => 'required',
                //invoice attributes
                'invoice.code' => 'required',
                'invoice.invoice_date' => 'required|date',
                'invoice.due_date' => 'required|date|after_or_equal:invoice.invoice_date',
                'invoice.status' => 'required',
                'invoice.user_id' => 'required',
                //items
                'items' => 'required',
                'discount_properties.type' => 'required_with:discount_properties.amount,discount_properties.code',
                'discount_properties.code' => 'required_with:discount_properties.amount,discount_properties.type',
                'discount_properties.amount' => 'required_with:discount_properties.type,discount_properties.code',
            ]);
        }

        if ($this->isStore()) {
            $rules = array_merge($rules, [

            ]);
        }

        if ($this->isUpdate()) {
            $order = $this->route('order');

            $rules = array_merge($rules, [
                'order_number' => 'required|unique:ecommerce_orders,order_number,' . $order->id,
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'items.required' => trans('Ecommerce::exception.order.items_required'),
            'discount_properties.type.required_with' => '',
            'discount_properties.code.required_with' => '',
            'discount_properties.amount.required_with' => '',
        ];
    }
}
