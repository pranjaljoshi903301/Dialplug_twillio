<?php

namespace Corals\Modules\Ecommerce\Widgets;

use \Corals\Modules\Ecommerce\Models\Order;

class ProcessingOrdersWidget
{

    function __construct()
    {
    }

    function run($args)
    {

        $orders = Order::processing()->count();
        return ' <!-- small box -->
                <div class="card">
                <div class="small-box bg-green card-body">
                    <div class="inner">
                        <h3>' . $orders . '</h3>
                        <p>'.trans('Ecommerce::labels.widget.processing_orders').'</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <a href="' . url('e-commerce/orders') . '" class="small-box-footer">
                       '.trans('Corals::labels.more_info').'
                    </a>
                </div>
                </div>';
    }

}
