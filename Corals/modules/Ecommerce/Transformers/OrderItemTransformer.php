<?php

namespace Corals\Modules\Ecommerce\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Models\OrderItem;
use Corals\Modules\Ecommerce\Models\SKU;

class OrderItemTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {

        parent::__construct($extras);
    }

    /**
     * @param OrderItem $order_item
     * @return array
     * @throws \Throwable
     */
    public function transform(OrderItem $order_item)
    {

        $order_item_description = $order_item->description;

        if ($order_item->type == "Product") {
            $sku = SKU::where('code', $order_item->sku_code)->first();
            if ($sku) {
                $order_item_description = '<a href="' . url('shop/' . $sku->product->slug) . '" target="_blank">' . $sku->product->name . ' (' . $sku->code . ')' . '</a>';
            }
        }

        $transformedArray = [
            'amount' => \Payments::currency_convert($order_item->amount, null, $order_item->order->currency, true),
            'description' => $order_item_description,
        ];

        return parent::transformResponse($transformedArray);
    }
}
