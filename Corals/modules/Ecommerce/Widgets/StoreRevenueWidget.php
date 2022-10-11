<?php

namespace Corals\Modules\Ecommerce\Widgets;

use Corals\Modules\Payment\Common\Models\Transaction;

class StoreRevenueWidget
{

    function __construct()
    {
    }

    function run($args)
    {


        $transactions_total = Transaction::where('status', 'completed')->sum('payment_transactions.amount');

        if (!$transactions_total) {
            $transactions_total = 0;
        }

        return ' <!-- small box -->
            <div class="card">
                <div class="small-box bg-success card-body">
                    <div class="inner">
                        <h3>' . \Payments::admin_currency($transactions_total) . '</h3>
                        <p>' . trans('Ecommerce::labels.widget.revenue_order_sum') . '</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="' . url('transactions') . '" class="small-box-footer">
                        ' . trans('Corals::labels.more_info') . '
                    </a>
                </div>
            </div>';
    }

}
