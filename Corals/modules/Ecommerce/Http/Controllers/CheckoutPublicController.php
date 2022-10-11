<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Foundation\Http\Controllers\PublicBaseController;
use Corals\Modules\CMS\Traits\SEOTools;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Traits\CheckoutControllerCommonFunctions;
use Illuminate\Http\Request;

class CheckoutPublicController extends PublicBaseController
{
    use CheckoutControllerCommonFunctions, SEOTools;

    public $urlPrefix = '';

    protected $corals_middleware_except = [];
    protected $corals_middleware = [];

    /**
     * CheckoutPublicController constructor.
     */
    public function __construct()
    {
        $this->corals_middleware = \Filters::do_filter('corals_middleware', $this->corals_middleware, request());

        $this->middleware($this->corals_middleware, ['except' => $this->corals_middleware_except]);

        $this->setViewSharedData(['urlPrefix' => $this->urlPrefix]);

        parent::__construct();
    }



    public function index(Request $request)
    {


        $this->setViewSharedData(['page_type' => 'checkout_page']);


        \ShoppingCart::removeCoupons();
        \ShoppingCart::removeFees('Shipping');

        $cart_items = \ShoppingCart::getItems();

        if (sizeof($cart_items) == 0) {
            return redirectTo('cart');
        }

        $enable_shipping = false;

        if (\Shipping::hasShippableItems($cart_items)) {
            $enable_shipping = true;
        }

        \ShoppingCart::setAttribute('enable_shipping', $enable_shipping);


        \Assets::add(asset('assets/corals/plugins/smartwizard/css/smart_wizard.min.css'));
        \Assets::add(asset('assets/corals/plugins/smartwizard/css/smart_wizard_theme_arrows.css'));
        \Assets::add(asset('assets/corals/plugins/smartwizard/js/jquery.smartWizard.min.js'));

        $item = [
            'title' => 'Checkout',
            'meta_description' => 'Checkout',
            'url' => url('checkout'),
            'type' => 'checkout'
        ];

        $this->setSEO((object)$item);

        return view('templates.checkout')->with(compact('enable_shipping'));
    }

    public function showOrderSuccessPage(Order $order)
    {

        $this->setViewSharedData(['page_type' => 'checkout_success']);

        $item = [
            'title' => 'Congratulations',
            'meta_description' => 'Order success page',
            'url' => url('shop'),
            'type' => 'order-success'
        ];

        $this->setSEO((object)$item);


        $product_order_items = $order->items()->where('type', 'Product')->where('amount', '>', 0)->get();


        $js_products = $product_order_items->map(function ($item, $key) use (&$position) {

            return [
                'name' => $item->description,
                'id' => $item->sku->product->id,
                'price' => $item->amount,
                'brand' => optional($item->sku->product)->name,
                'category' => optional($item->sku->product->categories->first())->name,
                'quantity' => $item->quantity,
            ];
        });

        \JavaScript::put([
            'order_products' => $js_products
        ]);


        return view('templates.checkout_success')->with(['order' => $order]);
    }
}
