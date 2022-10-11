<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Foundation\Http\Controllers\PublicBaseController;
use Corals\Modules\CMS\Traits\SEOTools;
use Corals\Modules\Ecommerce\Http\Requests\AddToCartRequest;
use Corals\Modules\Ecommerce\Models\Product;
use Corals\Modules\Ecommerce\Models\SKU;
use Illuminate\Http\Request;

class CartPublicController extends PublicBaseController
{
    use SEOTools;

    /**
     * CartController constructor.
     */

    public function __construct()
    {
        $this->title = 'Ecommerce::module.cart.title';
        $this->title_singular = 'Ecommerce::module.cart.title';

        parent::__construct();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        \ShoppingCart::setInstance('default');
        \ShoppingCart::removeCoupons();
        \ShoppingCart::removeFees('Shipping');

        $item = [
            'title' => 'Cart',
            'meta_description' => 'Shopping Cart',
            'url' => url('cart'),
            'type' => 'cart'
        ];


        $this->setSEO((object)$item);

        return view('templates.cart');
    }

    /**
     * @param Request $request
     * @param $itemhash
     * @return \Illuminate\Http\JsonResponse
     */
    public function setQuantity(Request $request, $itemhash)
    {

        \ShoppingCart::setInstance('default');


        $data = [];

        try {
            $data = $request->json()->all();
            $action = isset($data['action']) ? $data['action'] : '';
            $cartItem = \ShoppingCart::find(['itemHash' => $itemhash]);

            $sku = $cartItem->id;

            if ($action == "increaseQuantity" && $itemhash) {
                $quantity = $cartItem->qty + 1;

                if (($quantity > $sku->allowed_quantity) && ($sku->allowed_quantity > 0)) {
                    \ShoppingCart::updateItem($itemhash, 'qty', $sku->allowed_quantity);
                    $message = ['level' => 'warning', 'message' => trans('Ecommerce::exception.cart.item_limited_per_order', ['quantity' => $sku->allowed_quantity])];
                    $data['quantity'] = $sku->allowed_quantity;
                    $data['item_total'] = \Currency::format($sku->allowed_quantity * $cartItem->price);
                } else {
                    $sku->checkInventory($quantity, true);
                    $item = \ShoppingCart::increment($itemhash);
                    $message = ['level' => 'success', 'message' => trans(trans('Ecommerce::labels.cart.item_has_been_update'))];
                    $data['quantity'] = $cartItem->qty;
                    $data['item_total'] = \Currency::format($cartItem->qty * $cartItem->price);
                }

            } else if ($action == "decreaseQuantity" && $itemhash) {
                $cartItem = \ShoppingCart::decrement($itemhash);

                if (!$cartItem) {
                    $action = "removeItem";
                    $message = ['level' => 'success', 'message' => trans(trans('Ecommerce::labels.cart.item_has_been_delete'))];

                } else {
                    $message = ['level' => 'success', 'message' => trans(trans('Ecommerce::labels.cart.item_has_been_update'))];
                    $data['quantity'] = $cartItem->qty;
                    $data['item_total'] = \Currency::format($cartItem->qty * $cartItem->price);

                }

            } else if ($action == "removeItem" && $itemhash) {
                \ShoppingCart::removeItem($itemhash);
                $message = ['level' => 'success', 'message' => trans(trans('Ecommerce::labels.cart.item_has_been_delete'))];

            } else if (isset($request->quantity)) {
                $quantity = $request->quantity;

                if (($quantity > $sku->allowed_quantity) && ($sku->allowed_quantity > 0)) {
                    $quantity = $sku->allowed_quantity;
                    $message = ['level' => 'warning', 'message' => trans('Ecommerce::exception.cart.item_limited_per_order', ['quantity' => $sku->allowed_quantity])];
                    $data['item_total'] = \Currency::format($sku->allowed_quantity * $cartItem->price);
                } else {
                    $sku->checkInventory($request->quantity, true);
                    $message = ['level' => 'success', 'message' => trans(trans('Ecommerce::labels.cart.item_has_been_update'))];
                }

                $message['quantity'] = $request->quantity;
                $data['quantity'] = $request->quantity;
                \ShoppingCart::updateItem($itemhash, 'qty', $quantity);
            }

            $message['itemhash'] = $itemhash;
            $message['product_name'] = $sku->product->name;
            $message['product_id'] = $sku->product->id;

            $data['action'] = $action;
            $data['sub_total'] = \ShoppingCart::subTotal();
            $data['tax_total'] = \ShoppingCart::taxTotal();
            $data['total_discount'] = \ShoppingCart::totalDiscount();
            $data['total'] = \ShoppingCart::total();

            if (count(\ShoppingCart::getItems()) > 0) {
                $data['empty'] = false;
            } else {
                $data['empty'] = true;
                \ShoppingCart::destroyCart();
            }
        } catch (\Exception $exception) {
            log_exception($exception, \ShoppingCart::class, 'setQuantity');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }
        $message = array_merge($message, $data);
        return response()->json($message);
    }

    public function getCartItemsSummary()
    {
        $rendered_cart_summary = view('partials.cart_summary')->render();
        return response()->json(['cart_summary' => $rendered_cart_summary]);
    }

    public function emptyCart()
    {
        \ShoppingCart::setInstance('default');

        \ShoppingCart::destroyCart();
        $message = ['level' => 'success', 'message' => trans(trans('Ecommerce::labels.cart.cart_empty'))];
        return response()->json($message);
    }

    /**
     * @param AddToCartRequest $request
     * @param Product $product
     * @param SKU $sku
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(AddToCartRequest $request, Product $product, SKU $sku)
    {
        \ShoppingCart::setInstance('default');


        $data = [];
        if (!$sku->exists) {
            $sku_hash = $request->get('sku_hash');
            $sku = SKU::findByHash($sku_hash);
        }

        $quantity = $request->get('quantity', 1);

        $cart_quantity = 0;
        try {
            foreach (\ShoppingCart::getItems() as $item) {
                if ($item->id->id == $sku->id) {
                    $cart_quantity += $item->qty;

                }
            }
            $sku->checkInventory(($cart_quantity + $quantity), true);

            \ShoppingCart::add(
                $sku,
                $name = null,
                $qty = $quantity,
                $price = $sku->price,
                ['product_options' => $request->get('options', []), 'properties' => ['sku_id' => $sku->id]]

            );

            $message = ['level' => 'success', 'message' => trans(trans('Ecommerce::labels.cart.product_has_been_add')),
                'action_buttons' => [trans('Ecommerce::labels.checkout.cart_detail') => url('cart'), trans('Ecommerce::labels.cart.proceed_checkout') => url('checkout')]
            ];
            $data['total'] = \ShoppingCart::total();
            $data['cart_count'] = count(\ShoppingCart::getItems());

            $message['product_name'] = $sku->product->name;
            $message['product_id'] = $sku->product->id;
            $message['quantity'] = $quantity;


        } catch (\Exception $exception) {
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        $message = array_merge($message, $data);
        return response()->json($message);
    }
}
