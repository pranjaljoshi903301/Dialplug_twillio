<?php

namespace Corals\Modules\Ecommerce\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Ecommerce\Models\Shipping;

class ShippingTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('ecommerce.models.shipping.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Shipping $shipping
     * @return array
     * @throws \Throwable
     */
    public function transform(Shipping $shipping)
    {
        $transformedArray = [
            'id' => $shipping->id,
            'priority' => $shipping->priority,
            'exclusive' => $shipping->exclusive ? '<i class="fa fa-check text-success"></i>' : '-',
            'name' => $shipping->name,
            'shipping_method' => $shipping->shipping_method,
            'rate' => $shipping->rate ? \Currency::format($shipping->rate, \Payments::admin_currency_code()) : '-',
            'min_order_total' => $shipping->min_order_total ? \Currency::format($shipping->min_order_total, \Payments::admin_currency_code()) : '-',
            'country' => $shipping->country ?? 'All Countries',
            'action' => $this->actions($shipping)
        ];

        return parent::transformResponse($transformedArray);
    }
}
