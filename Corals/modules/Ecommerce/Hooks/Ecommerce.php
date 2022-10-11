<?php

namespace Corals\Modules\Ecommerce\Hooks;


use Corals\Modules\Ecommerce\DataTables\MyOrdersDataTable;
use Corals\Modules\Payment\Common\DataTables\MyInvoicesDataTable;

class Ecommerce
{
    /**
     * Subscription constructor.
     */
    function __construct()
    {
    }

    public function show_cart_icon()
    {
        $defaultCart = \ShoppingCart::get('default');

        if (user() && user()->hasPermissionTo('Ecommerce::cart.access')) {
            echo '<li class="cart cart-menu" >
                        <a href = "' . url('e-commerce/cart') . '" style = "padding: 10px 15px;" >
                            <i class="fa fa-2x fa-shopping-cart" style = "" ></i >
                            <span class="label label-success cart_total_label"
                                  id = "cart-header-total" >' . $defaultCart->total() . '</span >
                        </a >
                    </li >';
        }
    }

    /**
     * @param $user
     * @param $active_tab
     * @throws \Throwable
     */
    public function show_profile_tabs_items($user, $active_tab)
    {
        if ($user->hasPermissionTo('Payment::invoices.view') || $user->hasPermissionTo('Ecommerce::my_orders.access')) {
            $profile_subscription_tabs = view('Ecommerce::shop.partials.tabs_items')->with(compact('user', 'active_tab'))->render();
            echo $profile_subscription_tabs;
        }
    }

    public function show_profile_tabs_content($user, $active_tab)
    {
        if ($user->hasPermissionTo('Payment::invoices.view') || $user->hasPermissionTo('Ecommerce::my_orders.access')) {
            \Actions::add_action('admin_footer_js', function () {
            }, 9);
        }

        if ($user->hasPermissionTo('Ecommerce::my_orders.access')) {
            $dataTable = new MyOrdersDataTable();
            $dataTable->setResourceUrl(url('e-commerce/orders/my'));

            $profile_ecommerce_tabs = $dataTable->render('Ecommerce::shop.partials.my_orders_tab_content', compact('user', 'active_tab'))->render();
            echo $profile_ecommerce_tabs;
            echo $dataTable->html()->assets();
            \Actions::add_action('admin_footer_js', function () use ($dataTable) {
                echo $dataTable->html()->scripts();
            }, 11);

        }

        if ($user->hasPermissionTo('Payment::invoices.view')) {
            $dataTable = new MyInvoicesDataTable();
            $dataTable->setResourceUrl(url('my-invoices'));
            $profile_ecommerce_tabs = $dataTable->render('Ecommerce::shop.partials.my_invoices_tab_content', compact('user', 'active_tab'))->render();
            echo $profile_ecommerce_tabs;
            echo $dataTable->html()->assets();
            \Actions::add_action('admin_footer_js', function () use ($dataTable) {
                echo $dataTable->html()->scripts();
            }, 12);
        }

    }

    /**
     * @param $dashboard_content
     * @return string
     * @throws \Throwable
     */
    public function dashboard_content($dashboard_content, $active_tab)
    {
        if (user()->hasRole('superuser')) {
            $dashboard_content .= view('Ecommerce::dashboard.superuser')->with(compact('active_tab'))->render();
        } else {
            $dashboard_content .= view('Ecommerce::dashboard.user')->with(compact('active_tab'))->render();

        }


        return $dashboard_content;
    }
}

