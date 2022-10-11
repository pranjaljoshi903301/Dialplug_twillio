<?php


namespace Corals\Modules\Ecommerce\Services;


use Carbon\Carbon;
use Corals\Modules\Ecommerce\Classes\Coupons\Advanced;
use Corals\Modules\Ecommerce\Classes\Ecommerce;
use Corals\Modules\Ecommerce\Facades\Shipping;
use Corals\Modules\Ecommerce\Models\Coupon;
use Corals\Modules\Ecommerce\Models\Shipping as ShippingModel;
use Corals\Modules\Ecommerce\Transformers\API\CouponPresenter;
use Corals\Modules\Ecommerce\Transformers\API\ShippingPresenter;
use Corals\Modules\Payment\Common\Models\Invoice;

class CheckoutService
{
    /**
     * @param $request
     * @param $code
     * @return mixed
     * @throws \Corals\Modules\Ecommerce\Exceptions\CouponException
     */
    public function getCouponByCode($request, $code)
    {
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            throw new \Exception(trans('Ecommerce::exception.checkout.invalid_coupon', ['code' => $code]));
        }

        $coupon_class = new Advanced($code, $coupon, []);

        $coupon_class->validate(true);

        $coupon->setPresenter(new CouponPresenter());

        return $coupon->presenter();
    }

    /**
     * @param $request
     * @param $countryCode
     * @return mixed
     * @throws \Exception
     */
    public function getAvailableShippingRoles($request, $countryCode)
    {
        $shipping_roles = ShippingModel::query()
            ->where('country', $countryCode)
            ->orWhereNull('country')
            ->orderBy('exclusive', 'DESC')
            ->orderBy('priority', 'asc')
            ->orderBy('name', 'asc')->get();


        return (new ShippingPresenter())->present($shipping_roles)['data'];
    }

    /**
     * @param $order
     * @param $paymentStatus
     * @param $user
     * @param $billingAddress
     * @return mixed
     */
    public function generateOrderInvoice($order, $paymentStatus, $user, $billingAddress)
    {
        $invoice = $order->invoice;


        if (!$invoice) {
            $invoice = Invoice::create([
                'code' => Invoice::getCode('INV'),
                'currency' => $order->currency,
                'status' => $paymentStatus,
                'invoicable_id' => $order->id,
                'invoicable_type' => get_class($order),
                'due_date' => Carbon::now(),
                'invoice_date' => now(),
                'sub_total' => $order->amount,
                'total' => $order->amount,
                'user_id' => $user->id,
                'properties' => ['billing_address' => $billingAddress]
            ]);

            $invoice_items = [];

            foreach ($order->items as $order_item) {
                $invoice_items[] = [
                    'code' => \Str::random(6),
                    'description' => $order_item->description,
                    'amount' => $order_item->amount,
                    'itemable_id' => $order_item->id,
                    'itemable_type' => get_class($order_item),
                ];
            }

            $invoice->items()->createMany($invoice_items);
        } else {
            $invoice->status = $paymentStatus;
            $invoice->save();
        }

        return $invoice;
    }

    /**
     * @param $order
     * @param $shippingAddress
     */
    public function setOrderShippingDetails($order, $shippingAddress)
    {
        $shipping_transaction = [];

        foreach ($order->items as $order_item) {
            if ($order_item->type == 'Shipping') {
                try {
                    $shipping_transaction = Shipping::createShippingTransaction($order_item);

                } catch (\Exception $exception) {
                    log_exception($exception, 'CreatShippingTransaction', 'Checkout');
                }
            }
        }

        $order_shipping = $order->shipping ?? [];

        $shipping = array_merge($order_shipping, $shipping_transaction);

        $shipping['shipping_address'] = $shippingAddress;

        $order->shipping = $shipping;

        $order->save();
    }

    /**
     * @param $order
     * @param $invoice
     * @param $user
     * @throws \Exception
     */
    public function orderFulfillment($order, $invoice, $user)
    {
        \Actions::do_action('post_order_received', $order);

        event('notifications.e_commerce.order.received', ['user' => $user, 'order' => $order]);

        $ecommerce = new Ecommerce();
        $ecommerce->deductFromInventory($order);
        $ecommerce->addContentAccess($order, $user);
        $ecommerce->setTransactions($invoice, $order);
    }
}
