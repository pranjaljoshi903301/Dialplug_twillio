<?php

namespace Corals\Modules\Ecommerce\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Ecommerce\Models\Order;

class OrderTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('ecommerce.models.order.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Order $order
     * @return array
     * @throws \Throwable
     */
    public function transform(Order $order)
    {
        $levels = [
            'pending' => 'info',
            'submitted' => 'info',
            'processing' => 'success',
            'completed' => 'primary',
            'failed' => 'danger',
            'canceled' => 'warning'
        ];

        $payment_levels = [
            'pending' => 'info',
            'paid' => 'success',
            'canceled' => 'danger',
            'refunded' => 'danger',
            'partial_refund' => 'warning',
            'failed' => 'danger',
        ];

        $payment_status = $order->billing['payment_status'] ?? '';
        $user_id = null;


        if ($order->user) {
            if (user() && user()->can('view', $order->user)) {
                $user_id = "<a target='_blank' href='" . url('users/' . $order->user->hashed_id) . "'> {$order->user->full_name}</a>";

            } else {
                $user_id = $order->user->full_name;
            }
        } else {
            $user_id = trans('Ecommerce::labels.order.guest');
        }

        $currency = strtoupper($order->currency);

        $transformedArray = [
            'status' => formatStatusAsLabels($order->status, ['level' => $levels[$order->status], 'text' => trans('Ecommerce::status.order.' . $order->status)]),

            'order_number' => '<a href="' . url($this->resource_url . '/' . $order->hashed_id) . '">' . $order->order_number . '</a>',
            'id' => $order->id,
            'checkbox' => $this->generateCheckboxElement($order),
            'currency' => $currency,
            'amount' => \Payments::currency_convert($order->amount, null, $currency, true),
            'user_id' => $user_id,
            'payment_status' => $payment_status ? formatStatusAsLabels($payment_status, ['level' => $payment_levels[$payment_status], 'text' => trans('Ecommerce::status.payment.' . $payment_status)]) : ' -  ',
            'created_at' => format_date($order->created_at),
            'updated_at' => format_date($order->updated_at),
            'action' => $this->actions($order)
        ];

        return parent::transformResponse($transformedArray);
    }
}
