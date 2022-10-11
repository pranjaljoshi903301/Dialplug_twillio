<?php

namespace Corals\Modules\Ecommerce\Http\Requests\API;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\Ecommerce\Classes\Coupons\Advanced;
use Corals\Modules\Ecommerce\Models\Coupon;

class OrderSubmitRequest extends BaseRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     * @throws \Corals\Modules\Ecommerce\Exceptions\CouponException
     */
    public function rules()
    {
        $rules = [
            'amount' => 'required',
            'order_items' => 'required',
            'status' => 'required|in:processing,submitted',
            'payment_reference' => 'required',
            'gateway' => 'required',
            'payment_status' => 'required|in:pending,paid',
            'billing_address.first_name' => 'required',
            'billing_address.last_name' => 'required',
            'billing_address.email' => 'required',
            'billing_address.address_1' => 'required',
            'billing_address.city' => 'required',
            'billing_address.state' => 'required',
            'billing_address.country' => 'required',
            'billing_address.zip' => 'required',
        ];

        if ($this->get('enable_shipping')) {
            $rules = array_merge($rules, [
                'shipping_address.first_name' => 'required',
                'shipping_address.last_name' => 'required',
                'shipping_address.address_1' => 'required',
                'shipping_address.city' => 'required',
                'shipping_address.state' => 'required',
                'shipping_address.country' => 'required',
                'shipping_address.zip' => 'required',
            ]);
        }

        $rules = array_merge($rules, [
            'order_items.*.amount' => 'required',
            'order_items.*.quantity' => 'required',
            'order_items.*.description' => 'required',
            'order_items.*.type' => 'required',
            'order_items.*.tax_ids' => 'nullable|array',
            'order_items.*.properties' => 'nullable|array',
        ]);

        foreach ($this->get('order_items', []) ?? [] as $item) {
            if ($item['type'] === 'Discount') {
                $code = $item['sku_code'];

                $coupon = Coupon::where('code', $code)->first();

                if (!$coupon) {
                    throw new \Exception(trans('Ecommerce::exception.checkout.invalid_coupon', ['code' => $code]));
                }

                $coupon_class = new Advanced($code, $coupon, []);

                $coupon_class->validate(true);
            }
        }

        return $rules;
    }
}
