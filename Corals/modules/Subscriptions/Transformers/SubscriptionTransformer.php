<?php

namespace Corals\Modules\Subscriptions\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Modules\BT\Models\BitrixTelephony;

class SubscriptionTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('subscriptions.models.subscription.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Subscription $subscription
     * @return array
     * @throws \Throwable
     */
    public function transform(Subscription $subscription)
    {

        $plan = $subscription->plan;
        
        $bitrix_telephony = BitrixTelephony::where('user_id', $subscription->user->id)->first();
        $setup_status = $bitrix_telephony
            ? $bitrix_telephony->setup_status
            ? '<span class="label label-success">Successful</span>'
            : '<span class="label label-danger">Pending</span>'
            : '<span class="label label-info">Configuration Pending</span>';

        $invoice_status = count($subscription->invoices) > 0
            ? $subscription->invoices[count($subscription->invoices) - 1]->status == 'paid'
            ? '<span class="label label-success">Paid</span>'
            : '<span class="label label-info">Pending</span>'
            : '<span class="label label-danger">Not Created</span>';

        $product = $subscription->plan->product;
        if ($subscription->pending()) {
            $active = '<span class="label label-info">' . trans('Subscriptions::attributes.subscription.subscription_statuses.pending') . '</span>';
        } elseif ($subscription->active()) {
            $active = '<span class="label label-success">' . trans('Subscriptions::attributes.subscription.subscription_statuses.active') . '</span>';
        } else {
            $active = '<span class="label label-danger">' . trans('Subscriptions::attributes.subscription.subscription_statuses.cancelled') . '</span>';

        }

        $transformedArray = [
            'id' => $subscription->id,
            'user_id' => "<a href='" . url('users/' . $subscription->user->hashed_id) . "'> {$subscription->user->full_name}</a>",
            'plan_id' => $plan->name,
            'gateway' => $subscription->gateway,
            'product_id' => "<a href='" . url('subscriptions/products/' . $product->hashed_id) . "'> {$product->name}</a>",
            'trial_ends_at' => format_date($subscription->trial_ends_at) ?: '-',
            'ends_at' => format_date($subscription->ends_at) ?: '-',
            'status' => $active,
            'invoice_status' => $invoice_status,
            'setup_status' => $setup_status,
            'on_trial' => $subscription->onTrial() ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-remove text-danger"></i>',
            'created_at' => format_date($subscription->created_at),
            'updated_at' => format_date($subscription->updated_at),
            'subscription_reference' => "<a href='" . url('subscriptions/subscriptions/' . $subscription->hashed_id) . "'> {$subscription->subscription_reference}</a>",
            'action' => $this->actions($subscription)
        ];

        return parent::transformResponse($transformedArray);
    }
}
