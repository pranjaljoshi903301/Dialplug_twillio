<?php

namespace Corals\Modules\Ecommerce\Traits;

use Corals\Modules\Ecommerce\Classes\Coupons\Advanced;
use Corals\Modules\Ecommerce\Classes\Coupons\Fixed;
use Corals\Modules\Ecommerce\Classes\Coupons\Percentage;
use Corals\Modules\Ecommerce\Classes\Ecommerce;
use Corals\Modules\Ecommerce\Facades\Ecommerce as EcommerceFacade;
use Corals\Modules\Ecommerce\Http\Requests\CheckoutRequest;
use Corals\Modules\Ecommerce\Models\Coupon;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Models\SKU;
use Corals\Modules\Payment\Common\Models\Tax;
use Corals\Modules\Payment\Payment;
use Corals\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

trait CheckoutControllerCommonFunctions
{
    /**
     * CartController constructor.
     */
    protected $shipping;

    /**
     * @param $step
     * @param Request $request
     * @return bool|string
     * @throws \Throwable
     */
    public function checkoutStep($step, Request $request)
    {

        if (request()->has('order')) {
            if ($order = Order::findByHash(request()->input('order'))) {
                \ShoppingCart::setInstance($order->hashed_id);
                $this->setViewSharedData(['order' => $order]);
            } else {
                abort(404);
            }
        }

        try {
            switch ($step) {
                case 'checkout-method':
                    return view('Ecommerce::checkout.partials.checkout_method')->render();
                case 'cart-details':
                    return view('Ecommerce::checkout.partials.cart_items')->render();
                case 'billing-shipping-address':
                    $enable_shipping = \ShoppingCart::getAttribute('enable_shipping');

                    $billing_address = \ShoppingCart::getAttribute('billing_address') ?? [];
                    if (!$billing_address) {
                        if (user() && user()->address('billing')) {
                            $billing_address = user()->address('billing');
                        }
                    }

                    $shipping_address = \ShoppingCart::getAttribute('shipping_address') ?? [];

                    if (!$shipping_address) {
                        if (user() && user()->address('shipping')) {
                            $shipping_address = user()->address('shipping');

                        }
                    }
                    if (user()) {
                        $shipping_address['first_name'] = user()->name;
                        $shipping_address['last_name'] = user()->last_name;
                        $shipping_address['email'] = user()->email;

                        $billing_address['first_name'] = user()->name;
                        $billing_address['last_name'] = user()->last_name;
                        $billing_address['email'] = user()->email;
                    }


                    return view('Ecommerce::checkout.partials.address')->with(compact('shipping_address', 'enable_shipping', 'billing_address'))->render();
                case 'select-payment':
                    $gateway = null;
                    $gateway_name = $request->get('gateway_name');
                    $billing = [];
                    $order_has_shipping_item = \ShoppingCart::getAttribute('order_has_shipping_item');


                    $enable_shipping = \ShoppingCart::getAttribute('enable_shipping');
                    $billing_address = \ShoppingCart::getAttribute('billing_address');
                    $shipping_address = \ShoppingCart::getAttribute('shipping_address');

                    $cart_items = \ShoppingCart::getItems();
                    $cart_fees = \ShoppingCart::getFees();

                    $user = user();

                    $billing['billing_address'] = $billing_address;
                    $shipping['shipping_address'] = $shipping_address;

                    if (\ShoppingCart::getAttribute('order_id')) {
                        $order = Order::find(\ShoppingCart::getAttribute('order_id'));

                        if ($order) {
                            $order->items()->delete();

                            $order->update([
                                'amount' => \ShoppingCart::total(false),
                                'billing' => $billing,
                                'shipping' => $shipping,
                                'currency' => \Payments::session_currency(),
                                'status' => 'pending',
                            ]);
                        }

                    } else {
                        $order = Order::create([
                            'amount' => \ShoppingCart::total(false),
                            'currency' => \Payments::session_currency(),
                            'order_number' => \Ecommerce::createOrderNumber(),
                            'billing' => $billing,
                            'shipping' => $shipping,
                            'status' => 'pending',
                            'user_id' => $user ? $user->id : null,
                        ]);

                        \ShoppingCart::setAttribute('order_id', $order->id);
                    }

                    $items = [];

                    foreach ($cart_items as $item) {

                        $items[] = [
                            'amount' => $item->id->price,
                            'quantity' => $item->qty,
                            'description' => $item->id->product->name . ' - ' . $item->id->code,
                            'sku_code' => $item->id->code,
                            'type' => 'Product',
                            'tax_ids' => $item->tax_ids,
                            'properties' => $item->properties,
                            'item_options' => ['product_options' => $item->product_options]
                        ];
                    }
                    foreach ($cart_fees as $fee_name => $fee) {
                        if (($fee->type != 'Shipping') || ($order_has_shipping_item && $fee->type == 'Shipping')) {
                            $items[] = [
                                'amount' => $fee->amount,
                                'quantity' => $fee->qty ?? 1,
                                'description' => $fee_name,
                                'sku_code' => '',
                                'tax_ids' => $fee->options['tax_ids'] ?? [],
                                'properties' => $fee->options['properties'] ?? [],
                                'type' => $fee->type ?? 'Fee',

                            ];
                        }
                    }


                    if ($enable_shipping && !$order_has_shipping_item) {

                        $shipping_rates = \ShoppingCart::getAttribute('shipping_rates');
                        $selected_shipping_method = \ShoppingCart::getAttribute('selected_shipping_method');
                        $selected_shipping = $shipping_rates[$selected_shipping_method];
                        $shipping_description = $selected_shipping['service'] ? $selected_shipping['provider'] . ' - ' . $selected_shipping['service'] : $selected_shipping['provider'];
                        $shipping_properties = ['shipping_rule_id' => $selected_shipping['shipping_rule_id'], 'shipping_method' => $selected_shipping_method];
                        $items[] = [
                            'amount' => $selected_shipping['amount'],
                            'tax_ids' => $selected_shipping->options['tax_ids'] ?? [],
                            'quantity' => 1,
                            'description' => $shipping_description,
                            'sku_code' => '',
                            'type' => 'Shipping',
                            'properties' => $shipping_properties,
                        ];

                        \ShoppingCart::removeFees('Shipping');
                        \ShoppingCart::addFee($shipping_description, $selected_shipping['amount'], 'Shipping', false, $shipping_properties);

                        $order->amount = \ShoppingCart::total(false);
                        $order->save();
                    }

                    $order_tax = \ShoppingCart::taxTotal(false);

                    if ($order_tax) {
                        $items[] = [
                            'amount' => $order_tax,
                            'quantity' => 1,
                            'description' => 'Sales Tax',
                            'sku_code' => 'tax',
                            'type' => 'Tax',
                        ];
                    }


                    foreach (\ShoppingCart::getCoupons() as $coupon_code => $coupon) {
                        $items[] = [
                            'amount' => -1 * $coupon->discount(),
                            'quantity' => 1,
                            'description' => $coupon_code . ' ( ' . $coupon->displayValue() . ' )',
                            'sku_code' => $coupon_code,
                            'type' => 'Discount',
                            'properties' => ['code' => $coupon_code, 'type' => $coupon->getName(), 'amount' => $coupon->value]
                        ];
                    }

                    $order->items()->createMany($items);

                    if (!$gateway_name) {
                        $available_gateways = \Payments::getAvailableGateways();
                        foreach ($available_gateways as $gateway_key => $available_gateway) {
                            $ecommerce = new Ecommerce($gateway_key);
                            if (!$ecommerce->gateway->getConfig('support_ecommerce')) {
                                unset($available_gateways[$gateway_key]);
                            }
                        }
                        if (count($available_gateways) == 1) {
                            $gateway_name = key($available_gateways);
                        }
                    }

                    //save amount again after addons calculations
                    $order->amount = \ShoppingCart::total(false);
                    $order->save();


                    if (\ShoppingCart::getInstanceName() != $order->hashed_id) {

                        \ShoppingCart::cloneTo($order->hashed_id);

                    }

                    if ($gateway_name) {
                        $ecommerce = new Ecommerce($gateway_name);
                        $gateway = $ecommerce->gateway;
                    }

                    return view('Ecommerce::checkout.partials.payment')->with(compact('gateway', 'available_gateways', 'order'))->render();
                    break;
                case 'shipping-method':
                    $shipping_address = \ShoppingCart::getAttribute('shipping_address');
                    $cart = \ShoppingCart::getItems();
                    $order_total = \ShoppingCart::total(false);

                    $shipping_rates = \Shipping::getAvailableShippingMethods($shipping_address, $cart, $order_total);

                    \ShoppingCart::setAttribute('shipping_rates', $shipping_rates);

                    $shipping_methods = [];

                    if (is_array($shipping_rates)) {
                        foreach ($shipping_rates as $key => $rate) {
                            $label = $rate['provider'];
                            if ($rate['service']) {
                                $label .= " " . $rate['service'];
                            }
                            if ($rate['amount']) {
                                $label .= ' : <span class="text-info">' . \Currency::format($rate['amount'], $rate['currency']) . '</span>';
                            }
                            if ($rate['estimated_days']) {
                                $label .= ', Estimated Delivery : <span class="text-info">' . $rate['estimated_days'] . ' Day(s) </span>';
                            }
                            if ($rate['description']) {
                                $label .= '<br><small>' . $rate['description'] . '</small>';
                            }
                            $shipping_methods[$key] = $label;
                        }
                    }

                    return view('Ecommerce::checkout.partials.shipping_methods')->with(['shipping_methods' => $shipping_methods])->render();
                    break;
                case 'order-review':
                    $order = Order::find(\ShoppingCart::getAttribute('order_id'));

                    return view('Ecommerce::checkout.partials.order_review')->with(['order' => $order])->render();
                    break;
                default:
                    return false;
            }
        } catch (\Exception $exception) {
            log_exception($exception, 'CheckOutController', 'checkoutStep', null, true);
        }
    }

    public function saveCheckoutStep($step, CheckoutRequest $request)
    {

        if (request()->has('order')) {
            if ($order = Order::findByHash(request()->input('order'))) {
                \ShoppingCart::setInstance($order->hashed_id);
                $this->setViewSharedData(['order' => $order]);
            } else {
                abort(404);
            }
        }

        $cart = \ShoppingCart::getItems();
        try {
            switch ($step) {
                case 'cart-details':
                    if ($request->input('coupon_code')) {
                        $coupon_code = $request->input('coupon_code');
                        $coupon = Coupon::where('code', $coupon_code)->first();
                        if (!$coupon) {
                            throw new \Exception(trans('Ecommerce::exception.checkout.invalid_coupon',
                                ['code' => $coupon_code]));
                        }
                        $coupon_class = new Advanced($coupon_code, $coupon, []);
                        $coupon_class->validate(true);

                        \ShoppingCart::addCoupon($coupon_class);
                    }
                    break;
                case 'billing-shipping-address':
                    $shipping_address = $request->input('shipping_address');
                    $billing_address = $request->input('billing_address');
                    if (\Settings::get('ecommerce_tax_calculate_tax', true)) {
                        if ($shipping_address) {
                            $this->calculateCartTax($shipping_address);
                        } else if ($billing_address) {
                            $this->calculateCartTax($billing_address);
                        }
                    }

                    if (user() && $request->input('save_billing')) {
                        user()->saveAddress($billing_address, 'billing');
                    }
                    if (user() && $request->input('save_shipping')) {
                        user()->saveAddress($shipping_address, 'shipping');
                    }

                    \ShoppingCart::setAttribute('billing_address', $billing_address);
                    \ShoppingCart::setAttribute('shipping_address', $shipping_address);
                    break;
                case 'select-payment':
                    $checkoutToken = $request->input('checkoutToken');
                    $gateway_key = $request->input('gateway');
                    $payment_details = $request->input('payment_details');

                    $gateway = Payment::create($gateway_key);

                    if (method_exists($gateway, 'validateRequest')) {
                        $gateway->validateRequest($request);
                    }

                    \ShoppingCart::setAttribute('checkoutToken', $checkoutToken);
                    \ShoppingCart::setAttribute('gateway', $gateway_key);
                    \ShoppingCart::setAttribute('payment_details', $payment_details);
                    break;
                case 'shipping-method':
                    $shipping_method = $request->input('selected_shipping_method');
                    \ShoppingCart::setAttribute('selected_shipping_method', $shipping_method);
                    break;
            }

            echo json_encode(['action' => 'nextCheckoutStep', 'lastSuccessStep' => '#' . $step]);
        } catch (\Exception $exception) {
            if ($exception instanceof ValidationException) {
                throw $exception;
            }
            log_exception($exception, 'CheckOutController', 'saveCheckoutStep', null, true);
        }
    }

    /**
     * @param $gateway
     * @param Order $order
     * @return $this
     */
    public function gatewayPayment($gateway_name, Order $order)
    {

        if (request()->has('order')) {
            if ($order = Order::findByHash(request()->input('order'))) {
                \ShoppingCart::setInstance($order->hashed_id);
                $this->setViewSharedData(['order' => $order]);
            } else {
                abort(404);
            }
        }

        try {
            $ecommerce = new Ecommerce($gateway_name);
            $gateway = $ecommerce->gateway;

            //Add Additional Charges of exists from settings

            $additional_charge_amount = \Settings::get('ecommerce_additonalcharge_additonal_charge_amount', 0);
            $additional_charge_type = \Settings::get('ecommerce_additonalcharge_additonal_charge_type', '');
            $order->items()->where('sku_code', 'ADD_CHARGE')->delete();
            if ($additional_charge_amount && $additional_charge_type) {
                $additional_charge_title = \Settings::get('ecommerce_additonalcharge_additonal_charge_title', '');
                \ShoppingCart::removeFee($additional_charge_title);

                $apply_additional_charge = true;
                $additional_charge_gateways = \Settings::get('ecommerce_additonalcharge_additonal_charge_gateways', '');
                if ($additional_charge_gateways) {
                    $apply_charge_gateways = explode(',', $additional_charge_gateways);
                    if (!in_array($gateway_name, $apply_charge_gateways)) {
                        $apply_additional_charge = false;
                    }
                }
                if ($apply_additional_charge) {
                    if ($additional_charge_type == "fixed") {
                        $charge_amount = $additional_charge_amount;
                    } elseif ($additional_charge_type == "percentage") {
                        $charge_amount = ($additional_charge_amount / 100) * \ShoppingCart::subTotal(false);

                    }
                    if ($charge_amount) {
                        \ShoppingCart::addFee($additional_charge_title, $charge_amount, 'Charge');
                        $order->items()->create([
                            'amount' => $charge_amount,
                            'quantity' => 1,
                            'description' => $additional_charge_title,
                            'sku_code' => '',
                            'type' => 'Charge',
                        ]);
                    }
                }


            }
            //save amount again after additinal charge calculations
            $order->amount = \ShoppingCart::total(false);
            $order->save();

            $view = $gateway->getPaymentViewName('ecommerce');
            $action = 'checkout/step/select-payment';
            return view($view)->with(compact('gateway', 'action', 'order'));
        } catch (\Exception $exception) {
            log_exception($exception, 'CartController', 'card', null, true);
        }
    }

    /**
     * @param $gateway
     * @param Order $order
     * @param User $user
     * @return mixed
     */
    public function gatewayPaymentToken($gateway, Order $order, User $user, Request $request)
    {
        if (is_null($user)) {
            $user = user();
        }

        $params = $request->all();

        try {
            $ecommerce = new Ecommerce($gateway);
            $token = $ecommerce->createPaymentToken($order, $params);
            return $token;
        } catch (\Exception $exception) {
            log_exception($exception, 'CartController', 'generatePaymentToken');
        }
    }

    /**
     * @param $gateway
     * @param Order $order
     * @param User $user
     * @return mixed
     */
    public function gatewayCheckPaymentToken($gateway, Request $request)
    {


        $params = $request->all();

        try {
            $ecommerce = new Ecommerce($gateway);
            $token = $ecommerce->checkPaymentToken($params);
            return $token;
        } catch (\Exception $exception) {
            log_exception($exception, 'CartController', 'checkPaymentToken');
            return json_encode(['status' => 'error', 'error' => $exception->getMessage()]);
        }
    }


    public function doCheckout(Request $request)
    {
        $order_id = $request->get('order_id');
        $order = Order::find($order_id);

        \ShoppingCart::setInstance($order->hashed_id);

        $user = user() ?? new User();
        $cartItems = \ShoppingCart::getItems();
        $cartFees = \ShoppingCart::getFees();


        $checkoutToken = \ShoppingCart::getAttribute('checkoutToken');
        $payment_details = \ShoppingCart::getAttribute('payment_details');

        $gateway = \ShoppingCart::getAttribute('gateway');

        if (((count($cartItems) > 0) || (count($cartFees) > 0)) && ($checkoutToken || $payment_details)) {
            try {
                $payment_gateway = Payment::create($gateway);
                $payment_name = $payment_gateway->getName();

                if ($payment_gateway->requireRedirect()) {
                    $billing = $order->billing;
                    $billing['gateway'] = $payment_name;

                    $order->update([
                        'billing' => $billing,
                    ]);

                    $paymentRedirectURL = URL::temporarySignedRoute('ecommerce.public.checkout.redirect',
                        now()->addMinutes(5), [
                            'gateway' => $gateway,
                            'order' => $order->hashed_id,
                        ]);

                    return redirectTo($paymentRedirectURL);
                } elseif ($payment_gateway->getConfig('offline_management')) {
                    $payment_status = "pending";
                    $order_status = "submitted";
                    $payment_reference = $checkoutToken;
                } else {
                    $payment_reference = $this->payGatewayOrder($order, $user, [
                        'token' => $checkoutToken,
                        'gateway' => $gateway,
                        'payment_details' => $payment_details
                    ]);

                    $payment_status = "paid";
                    $order_status = "processing";
                }

                EcommerceFacade::checkoutOrder($order, $payment_name, $payment_reference,
                    $payment_status, $order_status, $user);

                flash(trans('Ecommerce::messages.order.order_placed'))->success();

                return redirectTo($this->urlPrefix . 'checkout/order-success/' . $order->hashed_id);
            } catch (\Exception $exception) {
                log_exception($exception, 'CheckOutController', 'doCheckout');
            }
        }

        return redirectTo($this->urlPrefix . 'checkout');
    }

    /**
     * @param $order
     * @param User $user
     * @param $checkoutDetails
     * @return bool
     * @throws \Exception
     */
    protected function payGatewayOrder($order, User $user, $checkoutDetails)
    {

        return $this->payGatewayOrderSend($order, $user, $checkoutDetails);
    }

    /**
     * @param $order
     * @param User $user
     * @param $checkoutDetails
     * @return bool
     * @throws \Exception
     */
    protected function payGatewayOrderSend($order, User $user, $checkoutDetails)
    {
        $Ecommerce = new Ecommerce($checkoutDetails['gateway']);

        return $Ecommerce->payOrder($order, $user, $checkoutDetails);
    }

    public function calculateCartTax($address)
    {
        $cart_items = \ShoppingCart::getItems();


        foreach ($cart_items as $cart_item) {
            $itemHash = $cart_item->getHash();

            $tax_rate = 0;

            if (is_array($cart_item->tax_ids)) {

                $tax_rate = Tax::query()->whereIn('id', $cart_item->tax_ids)->sum('rate');
                $tax_rate = $tax_rate / 100;

            } else {
                $taxes = \Payments::calculateTax($cart_item->id->product, $address);
                $tax_ids = [];
                foreach ($taxes as $tax_id => $tax) {
                    $tax_rate += $tax['rate'];
                    $tax_ids[] = $tax_id;
                }
                \ShoppingCart::updateItem($itemHash, 'tax_ids', $tax_ids);

            }

            \ShoppingCart::updateItem($itemHash, 'tax', $tax_rate);
        }

        $cart_fees = \ShoppingCart::getFees();
        foreach ($cart_fees as $fee_name => $fee) {
            $fee_options = $fee->options ?? [];
            if (is_array($fee_options['tax_ids'])) {
                $tax_rate = Tax::query()->whereIn('id', $fee_options['tax_ids'])->sum('rate');
                if ($tax_rate) {
                    $fee_amount = $fee->amount;
                    $fee_type = $fee->type;
                    $fee_options['tax'] = ($tax_rate / 100);
                    \ShoppingCart::removeFee($fee_name);
                    \ShoppingCart::addFee($fee_name, $fee_amount, $fee_type, true, $fee_options);
                }
            }
        }
    }

    public function loadOrderToCart($order)
    {

        \ShoppingCart::destroyCart();
        \ShoppingCart::setAttribute('order_id', $order->id);
        $result = ['has_shipping_item' => false];
        foreach ($order->items as $order_item) {
            switch ($order_item->type) {

                case 'Product':
                    $sku = SKU::where('code', $order_item->sku_code)->first();

                    \ShoppingCart::add(
                        $sku,
                        $name = $order_item->description,
                        $qty = $order_item->quantity,
                        $price = $order_item->amount,
                        ['tax_ids' => $order_item->tax_ids, 'properties' => $order_item->getProperties() ?? []]
                    );
                    break;
                case 'Fee':

                    \ShoppingCart::addFee($order_item->description, $order_item->amount, 'Fee', false, ['qty' => $order_item->quantity, 'tax_ids' => $order_item->tax_ids, 'type' => $order_item->type, 'properties' => $order_item->getProperties()]);
                    break;
                case 'Shipping':
                    $result['has_shipping_item'] = true;
                    \ShoppingCart::addFee($order_item->description, $order_item->amount, 'Shipping', false, ['qty' => $order_item->quantity, 'tax_ids' => $order_item->tax_ids, 'type' => $order_item->type, 'properties' => $order_item->getProperties()]);

                    break;
                case 'Discount':

                    $coupon_type = $order_item->getProperty('type');
                    if ($coupon_type == "Fixed") {
                        \ShoppingCart::addCoupon(new Fixed($order_item->getProperty('code'), $order_item->getProperty('amount')));

                    } else if ($coupon_type == "Percentage") {
                        \ShoppingCart::addCoupon(new Percentage($order_item->getProperty('code'), $order_item->getProperty('amount') / 100));
                    }

                    break;
                default:
                    break;
            }
        }


        return $result;
    }

    /**
     * @param Request $request
     * @param $gateway
     * @param Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectPage(Request $request, $gateway, Order $order)
    {
        if (!in_array($order->status, ['pending'])) {
            abort(404);
        }

        $payment_gateway = Payment::create($gateway);

        $paymentRedirectContent = $payment_gateway->getPaymentRedirectContent([
            'redirectHandler' => '\Corals\Modules\Ecommerce\Facades\Ecommerce::redirectHandler',
            'paymentPurpose' => 'Payment for order number: ' . $order->order_number,
            'transactionId' => $order->order_number,
            "amount" => $order->amount,
            "currency" => $order->currency,
            'object_details' => [
                'object_class' => Order::class,
                'object_id' => $order->id,
            ]
        ]);

        $gatewayName = $payment_gateway->getName();

        return view('views.redirect_page')->with(compact('paymentRedirectContent','gatewayName'));
    }
}
