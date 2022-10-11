<?php

namespace Corals\Modules\Ecommerce\Services;

use Corals\Foundation\Services\BaseServiceClass;

class CouponService extends BaseServiceClass
{
    protected $excludedRequestParams = ['users', 'products'];

    public function postStoreUpdate($request, $additionalData = [])
    {
        $coupon = $this->model;

        $coupon->users()->sync($request->get('users', []));

        $coupon->products()->sync($request->get('products', []));
    }
}
