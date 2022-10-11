<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Traits\CheckoutControllerCommonFunctions;
use Illuminate\Http\Request;

class CheckoutController extends BaseController
{
    use CheckoutControllerCommonFunctions;

    public $urlPrefix = 'e-commerce/';

    /**
     * CartController constructor.
     */

    public function __construct()
    {
        $this->title = 'Ecommerce::module.checkout.title';
        $this->title_singular = 'Ecommerce::module.checkout.title_singular';
        $this->setViewSharedData(['urlPrefix' => $this->urlPrefix]);


        parent::__construct();
    }


    /**
     * @param Request $request
     * @param Order|null $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\View\View|mixed
     */
    public function index(Request $request, Order $order = null)
    {

        $enable_shipping = false;
        \ShoppingCart::removeCoupons();
        \ShoppingCart::removeFees('Shipping');

        $order_has_shipping_item = false;
        if (request()->has('order')) {
            if ($order = Order::findByHash(request()->input('order'))) {
                \ShoppingCart::setInstance($order->hashed_id);
                $loadOrderResult = $this->loadOrderToCart($order);
                $order_has_shipping_item = $loadOrderResult['has_shipping_item'];
                if (isset($order->billing['payment_status']) && $order->billing['payment_status'] == 'paid') {
                    abort(403);
                }
                $this->setViewSharedData(['order' => $order]);
            } else {
                abort(404);
            }
        }
        $cart_items = \ShoppingCart::getItems();
        $cart_fees = \ShoppingCart::getFees();


        if (sizeof($cart_items) == 0 && sizeof($cart_fees) == 0) {
            return redirectTo('cart');
        }


        if (\Shipping::hasShippableItems($cart_items)) {
            $enable_shipping = true;
        }

        \ShoppingCart::setAttribute('enable_shipping', $enable_shipping);
        \ShoppingCart::setAttribute('order_has_shipping_item', $order_has_shipping_item);


        \Assets::add(asset('assets/corals/plugins/smartwizard/css/smart_wizard.min.css'));
        \Assets::add(asset('assets/corals/plugins/smartwizard/css/smart_wizard_theme_arrows.css'));
        \Assets::add(asset('assets/corals/plugins/smartwizard/js/jquery.smartWizard.min.js'));

        $this->setViewSharedData(['title', 'Checkout']);

        return view('Ecommerce::checkout.checkout')->with(compact('enable_shipping', 'order_has_shipping_item'));
    }

    public function showOrderSuccessPage(Order $order)
    {
        $this->setViewSharedData(['title', 'Congratulations !']);
        return view('Ecommerce::orders.order-success')->with(['order' => $order]);
    }

}
