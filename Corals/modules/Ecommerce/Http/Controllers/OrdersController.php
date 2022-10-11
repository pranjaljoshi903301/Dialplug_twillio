<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Carbon\Carbon;
use Corals\Foundation\DataTables\CoralsBuilder;
use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Ecommerce\Classes\Ecommerce;
use Corals\Modules\Ecommerce\DataTables\MyOrdersDataTable;
use Corals\Modules\Ecommerce\DataTables\MyPrivatePagesDataTable;
use Corals\Modules\Ecommerce\DataTables\OrdersDataTable;
use Corals\Modules\Ecommerce\Http\Requests\OrderPartialUpdateRequest;
use Corals\Modules\Ecommerce\Http\Requests\OrderRequest;
use Corals\Foundation\Http\Requests\BulkRequest;
use Corals\Modules\Ecommerce\Http\Requests\RefundOrderRequest;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Models\Shipping;
use Corals\Modules\Ecommerce\Models\SKU;
use Corals\Modules\Payment\Common\Models\Invoice;
use Corals\Modules\Payment\Common\Models\Tax;
use Corals\Modules\Payment\Common\Models\Transaction;
use Illuminate\Http\Request;
use \Spatie\MediaLibrary\Models\Media;

class OrdersController extends BaseController
{

    protected $shipping;

    public function __construct()
    {
        $this->resource_url = config('ecommerce.models.order.resource_url');
        $this->title = 'Ecommerce::module.order.title';
        $this->title_singular = 'Ecommerce::module.order.title_singular';
        parent::__construct();
    }

    protected function canAccess($order)
    {
        $canAccess = false;

        if (user()->hasPermissionTo('Ecommerce::my_orders.access') && $order->user->id == user()->id) {
            $canAccess = true;
        } elseif (user()->hasPermissionTo('Ecommerce::orders.access')) {
            $canAccess = true;
        }

        if (!$canAccess) {
            abort(403);
        }
    }

    /**
     * @param Request $request
     * @param OrdersDataTable $dataTable
     * @return mixed
     */
    public function index(OrderRequest $request, OrdersDataTable $dataTable)
    {
        return $dataTable->render('Ecommerce::orders.index');
    }

    public function create(OrderRequest $request)
    {
        $order = new Order();

        $order->order_number = \Ecommerce::createOrderNumber();
        $order->status = 'pending';
        $order->currency = \Payments::session_currency();

        $order->invoice = [
            'code' => Invoice::getCode('INV'),
            'invoice_date' => now()->toDateString(),
            'status' => 'pending',
        ];

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        $discountItem = [
            'code' => null,
            'amount' => null,
            'type' => null,
        ];

        $taxesList = \Payments::getTaxesList();

        return view('Ecommerce::orders.create_edit')->with(compact('order', 'discountItem', 'taxesList'));
    }

    public function store(OrderRequest $request)
    {
        try {
            $data = $this->getOrderDataFromRequest($request);

            $order = Order::create($data['orderData']);

            $order->items()->createMany($data['items']);

            $invoice = new Invoice($this->getInvoiceDataFromRequest($request, $data['orderItemsCalculations']));

            $invoice = $order->invoice()->save($invoice);

            $this->createInvoiceItemsFromOrder($order, $invoice);

            if ($request->has('send_invoice')) {
                event('notifications.invoice.send_invoice', ['invoice' => $invoice]);
            }
            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Order::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    protected function getOrderDataFromRequest($request)
    {
        $orderData = $request->only(['order_number', 'status', 'currency', 'properties']);

        $orderData['user_id'] = $request->input('invoice.user_id');
        $orderData['billing']['payment_status'] = $request->input('invoice.status');
        $orderItemsCalculations = $this->calculateOrder($request, false);

        $items = $this->getOrderItems($request);

        if (!empty($orderItemsCalculations['tax_total'])) {
            $items[] = [
                'amount' => $orderItemsCalculations['tax_total'],
                'quantity' => 1,
                'description' => 'Sales Tax',
                'sku_code' => null,
                'type' => 'Tax',
                'properties' => [],
            ];
        }

        if (!empty($orderItemsCalculations['discount'])) {
            $items[] = [
                'amount' => -1 * $orderItemsCalculations['discount'],
                'quantity' => 1,
                'description' => $request->input('discount_properties.code', 'Discount'),
                'sku_code' => null,
                'type' => 'Discount',
                'properties' => [
                    'type' => $request->input('discount_properties.type'),
                    'code' => $request->input('discount_properties.code'),
                    'amount' => $request->input('discount_properties.amount')
                ],
            ];
        }

        $orderData['amount'] = $orderItemsCalculations['total'] ?? 0;

        return compact('orderData', 'orderItemsCalculations', 'items');
    }

    protected function getInvoiceDataFromRequest($request, $orderItemsCalculations)
    {
        $invoiceData = $request->get('invoice');
        $invoiceData['currency'] = $request->get('currency');
        $invoiceData['sub_total'] = $orderItemsCalculations['subtotal'];
        $invoiceData['total'] = $orderItemsCalculations['total'];

        return $invoiceData;
    }

    protected function createInvoiceItemsFromOrder($order, $invoice)
    {
        $invoice_items = [];

        foreach ($order->items as $order_item) {
            $invoice_items[] = [
                'code' => \Str::random(6),
                'description' => $order_item->description,
                'amount' => $order_item->amount,
                'quantity' => $order_item->quantity,
                'itemable_id' => $order_item->id,
                'itemable_type' => get_class($order_item),
            ];
        }

        $invoice->items()->forceDelete();

        $invoice->items()->createMany($invoice_items);
    }

    protected function getOrderItems($request)
    {
        $items = $request->get('items');

        $orderItems = [];

        foreach ($items as $type => $typeItems) {
            switch ($type) {
                case 'Product':
                    foreach ($typeItems as $id => $item) {
                        $sku = SKU::find($id);
                        if (!$sku) {
                            continue;
                        }

                        $orderItems[] = [
                            'amount' => $item['unit_price'],
                            'tax_ids' => $item['taxes'] ?? [],
                            'quantity' => $item['quantity'],
                            'description' => $item['description'] ?? ($sku->product->name . ' - ' . $sku->code),
                            'sku_code' => $sku->code,
                            'type' => $type,
                            'properties' => ['sku_id' => $id]
                        ];
                    }
                    break;
                case'Shipping':
                    foreach ($typeItems as $id => $item) {
                        $shipping = Shipping::find($id);

                        if (!$shipping) {
                            continue;
                        }

                        $orderItems[] = [
                            'amount' => $item['unit_price'],
                            'quantity' => $item['quantity'],
                            'tax_ids' => $item['taxes'] ?? [],
                            'description' => $item['description'] ?? $shipping->name . ' - ' . $type,
                            'sku_code' => null,
                            'type' => $type,
                            'properties' => ['shipping_rule_id' => $id]
                        ];
                    }
                    break;
                case 'Fee':
                    foreach ($typeItems as $id => $item) {
                        $orderItems[] = [
                            'amount' => $item['unit_price'],
                            'quantity' => $item['quantity'],
                            'tax_ids' => $item['taxes'] ?? [],
                            'description' => $item['description'] ?? ($item['name'] . ' - ' . $type),
                            'sku_code' => null,
                            'type' => $type,
                            'properties' => ['fee_name' => $item['name']]
                        ];
                    }
                    break;
            }
        }

        return $orderItems;
    }

    /**
     * @param OrderPartialUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPaymentDetails(OrderPartialUpdateRequest $request, Order $order)
    {
        $order_statuses = trans(config('ecommerce.models.order.statuses'));

        $payment_statuses = trans(config('ecommerce.models.order.payment_statuses'));

        $this->setViewSharedData(['title_singular' => trans('Ecommerce::module.order.update')]);

        return view('Ecommerce::orders.payment_details')->with(compact('order', 'order_statuses', 'payment_statuses'));
    }

    /**
     * @param OrderPartialUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editShippingDetails(OrderPartialUpdateRequest $request, Order $order)
    {
        $order_statuses = trans(config('ecommerce.models.order.statuses'));

        $shippment_statuses = trans(config('ecommerce.models.order.shippment_statuses'));

        $this->setViewSharedData(['title_singular' => trans('Ecommerce::module.order.update')]);

        return view('Ecommerce::orders.shipping_details')->with(compact('order', 'order_statuses', 'shippment_statuses'));
    }

    /**
     * @param OrderPartialUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editOrderStatus(OrderPartialUpdateRequest $request, Order $order)
    {
        $order_statuses = trans(config('ecommerce.models.order.statuses'));

        $this->setViewSharedData(['title_singular' => trans('Ecommerce::module.order.update')]);

        return view('Ecommerce::orders.order_status')->with(compact('order', 'order_statuses'));
    }


    /**
     * @param OrderRequest $request
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(OrderRequest $request, Order $order)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $this->title_singular])]);

        if ($order->invoice) {
            $order->invoice->due_date = Carbon::parse($order->invoice->due_date)->toDateString();
            $order->invoice->invoice_date = Carbon::parse($order->invoice->invoice_date)->toDateString();
        }

        $items = $this->formatOrderItemsForEdit($order);

        $discountItem = $order->items()->where('type', 'Discount')->first();

        if ($discountItem) {
            $discountItem = [
                'code' => $discountItem->getProperty('code'),
                'amount' => $discountItem->getProperty('amount'),
                'type' => $discountItem->getProperty('type'),
            ];
        } else {
            $discountItem = [
                'code' => null,
                'amount' => null,
                'type' => null,
            ];
        }
        $taxesList = \Payments::getTaxesList();

        return view('Ecommerce::orders.create_edit')->with(compact('order', 'discountItem', 'items', 'taxesList'));
    }

    /**
     * @param $order
     * @return array
     */
    public function formatOrderItemsForEdit($order)
    {
        $items = [];

        foreach ($order->items as $item) {
            if (in_array($item->type, ['Tax', 'Discount'])) {
                continue;
            }

            switch ($item->type) {
                case 'Product':
                    $id = $item->getProperty('sku_id');

                    $sku = SKU::find($id);

                    if (!$sku) {
                        break;
                    }

                    $items['Product'][] = [
                        'id' => $id,
                        'name' => "{$sku->product->name} - ($sku->code)",
                        'unit_price' => $item->amount,
                        'quantity' => $item->quantity,
                        'description' => $item->description,
                        'type' => $item->type,
                        'tax_ids' => $item->tax_ids,
                    ];
                    break;
                case 'Shipping':
                    $id = $item->getProperty('shipping_rule_id');

                    $shipping = Shipping::find($id);

                    if (!$shipping) {
                        break;
                    }

                    $items['Shipping'][] = [
                        'id' => $id,
                        'name' => $shipping->name,
                        'unit_price' => $item->amount,
                        'quantity' => $item->quantity,
                        'description' => $item->description,
                        'type' => $item->type,
                        'tax_ids' => $item->tax_ids,
                    ];
                    break;
                case 'Fee':
                    $items['Fee'][] = [
                        'id' => count($items['Fee'] ?? []),
                        'name' => $item->getProperty('fee_name'),
                        'unit_price' => $item->amount,
                        'quantity' => $item->quantity,
                        'description' => $item->description,
                        'type' => $item->type,
                        'tax_ids' => $item->tax_ids,
                    ];
                    break;
            }
        }

        return $items;
    }

    /**
     * @param OrderPartialUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     */
    public function updatePaymentDetails(OrderPartialUpdateRequest $request, Order $order)
    {
        try {
            $data = $request->all();

            $billing = $order->billing ?? [];

            if ($request->has('billing')) {
                $billing = array_replace_recursive($billing, $data['billing']);
                $order->update([
                    'billing' => $billing,
                ]);
            }


            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.created', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Order::class, 'update');
        }

        return response()->json($message);
    }

    /**
     * @param OrderPartialUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     */
    public function updateShippingDetails(OrderPartialUpdateRequest $request, Order $order)
    {
        try {
            $data = $request->all();

            $shipping = $order->shipping ?? [];

            if ($request->has('shipping')) {
                $shipping = array_replace_recursive($shipping, $data['shipping']);
                $order->update([
                    'shipping' => $shipping,
                ]);
                $message = ['level' => 'success', 'message' => trans('Corals::messages.success.created', ['item' => $this->title_singular])];

            }

        } catch (\Exception $exception) {
            log_exception($exception, Order::class, 'update');
        }

        return response()->json($message);
    }


    /**
     * @param OrderPartialUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     */
    public function updateOrderStatus(OrderPartialUpdateRequest $request, Order $order)
    {
        try {
            $data = $request->all();
            $order->update([
                'status' => $data['status'],
            ]);
            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.created', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Order::class, 'update');
        }

        return response()->json($message);
    }

    /**
     * @param OrderPartialUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function notifyBuyer(OrderPartialUpdateRequest $request, Order $order)
    {
        try {
            event('notifications.e_commerce.order.updated', ['order' => $order]);

            $message = ['level' => 'success', 'message' => trans('Ecommerce::messages.order.buyer_notified_successfully')];
        } catch (\Exception $exception) {
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
            log_exception($exception, Order::class, 'update');
        }

        return response()->json($message);
    }

    /**
     * @param OrderRequest $request
     * @param Order $order
     * @return \Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|mixed
     */
    public function update(OrderRequest $request, Order $order)
    {
        try {
            $data = $this->getOrderDataFromRequest($request);

            $order->update($data['orderData']);

            $order->items()->forceDelete();

            $order->items()->createMany($data['items']);

            $invoiceData = $this->getInvoiceDataFromRequest($request, $data['orderItemsCalculations']);

            $invoice = $order->invoice;

            if ($invoice) {
                $invoice->update($invoiceData);
            } else {
                $invoice = $order->invoice()->create($invoiceData);
            }

            $this->createInvoiceItemsFromOrder($order, $invoice);

            if ($request->has('send_invoice')) {
                event('notifications.invoice.send_invoice', ['invoice' => $invoice]);
            }

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Order::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param Request $request
     * @param MyOrdersDataTable $dataTable
     * @return mixed
     */
    public function myOrders(Request $request, MyOrdersDataTable $dataTable)
    {
        if (!user()->hasPermissionTo('Ecommerce::my_orders.access')) {
            abort(403);
        }

        $this->setViewSharedData(['hideCreate' => true]);

        return $dataTable->render('Ecommerce::orders.index');
    }

    /**
     * @param Request $request
     * @param MyOrdersDataTable $dataTable
     * @return mixed
     */
    public function myPrivatePages(Request $request, MyPrivatePagesDataTable $dataTable)
    {
        if (!user()->hasPermissionTo('Ecommerce::my_orders.access')) {
            abort(403);
        }

        $this->setViewSharedData(['hideCreate' => true]);

        return $dataTable->render('Ecommerce::orders.private_pages');
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function myDownloads(Request $request)
    {
        CoralsBuilder::DataTableScripts();

        if (!user()->hasPermissionTo('Ecommerce::my_orders.access')) {
            abort(403);
        }

        $orders = Order::myOrders()->get();

        return view('Ecommerce::orders.downloads')->with(compact('orders'));
    }


    public function bulkAction(BulkRequest $request)
    {
        try {
            $action = $request->input('action');
            $selection = json_decode($request->input('selection'), true);
            switch ($action) {
                case 'pending' :
                    foreach ($selection as $selection_id) {
                        $order = Order::findByHash($selection_id);
                        if (user()->can('Ecommerce::order.update')) {
                            $order->update([
                                'status' => 'pending'
                            ]);
                            $order->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'processing' :
                    foreach ($selection as $selection_id) {
                        $order = Order::findByHash($selection_id);
                        if (user()->can('Ecommerce::order.update')) {
                            $order->update([
                                'status' => 'processing'
                            ]);
                            $order->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'submitted' :
                    foreach ($selection as $selection_id) {
                        $order = Order::findByHash($selection_id);
                        if (user()->can('Ecommerce::order.update')) {
                            $order->update([
                                'status' => 'submitted'
                            ]);
                            $order->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'canceled' :
                    foreach ($selection as $selection_id) {
                        $order = Order::findByHash($selection_id);
                        if (user()->can('Ecommerce::order.update')) {
                            $order->update([
                                'status' => 'canceled'
                            ]);
                            $order->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'completed' :
                    foreach ($selection as $selection_id) {
                        $order = Order::findByHash($selection_id);
                        if (user()->can('Ecommerce::order.update')) {
                            $order->update([
                                'status' => 'completed'
                            ]);
                            $order->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
            }
        } catch (\Exception $exception) {
            log_exception($exception, Order::class, 'bulkAction');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }
        return response()->json($message);
    }


    /**
     * @param OrderRequest $request
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(OrderRequest $request, Order $order)
    {
        return view('Ecommerce::orders.show')->with(compact('order'));
    }


    public function downloadFile(Request $request, Order $order, $hashed_id)
    {
        $this->canAccess($order);

        $id = hashids_decode($hashed_id);

        $media = Media::findOrfail($id);

        return response()->download(storage_path($media->getUrl()));
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return $this
     */
    public function track(Request $request, Order $order)
    {
        if (user()->hasPermissionTo('Ecommerce::orders.access') || user()->hasPermissionTo('Ecommerce::my_orders.access')) {
            try {
                $tracking = \Shipping::track($order);
                return view('Ecommerce::orders.track')->with(compact('order', 'tracking'));
            } catch
            (\Exception $exception) {
                log_exception($exception, 'OrderController', 'Track');
            }
        }

        abort(403);
    }

    public function calculateOrder(Request $request, $formatCalculationsWithCurrency = true)
    {
        $items = $request->get('items', []);
        $discountType = $request->input('discount_properties.type');
        $discountAmount = $request->input('discount_properties.amount');
        $currency = $request->get('items_currency', \Payments::session_currency());

        $subtotal = $taxTotal = $total = 0;

        foreach ($items as $type => $typeItems) {
            foreach ($typeItems as $item) {
                $itemTotal = $item['unit_price'] * $item['quantity'];

                $subtotal += $itemTotal;

                $taxes = $item['taxes'] ?? [];

                $itemTaxRateTotal = Tax::query()->whereIn('id', $taxes)->sum('rate');

                $taxTotal += ($itemTaxRateTotal / 100) * $itemTotal;
            }
        }

        $subtotalWithTax = $subtotal + $taxTotal;

        $discountAmount = floatval($discountAmount);

        if ($discountType == 'Percentage') {
            $discountAmount = ($discountAmount / 100) * $subtotalWithTax;
        }

        $total = $subtotalWithTax - $discountAmount;

        $calculations = [
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'discount' => $discountAmount,
            'total' => $total,
        ];

        if ($formatCalculationsWithCurrency) {
            $calculations = array_map(function ($total) use ($currency) {
                return \Payments::currency_convert($total, null, $currency, true);
            }, $calculations);
        }

        return $calculations;
    }

    public function getRefundView(RefundOrderRequest $request, Order $order)
    {
        return view('Ecommerce::orders.refund-order', compact('order'))->render();
    }

    public function doRefund(RefundOrderRequest $request, Order $order)
    {


        $data = $request->all();
        $amount = $data['amount'];
        $type = $data['type'];
        $cancel = $data['cancel'] ?? false;

        try {

            if (isset($order->billing['payment_status']) && ($order->billing['payment_status'] == 'refunded')) {
                throw new \Exception(trans('Ecommerce::exception.misc.already_refunded'));
            }

            if (isset($order->billing['payment_status']) && ($order->billing['payment_status'] != 'paid' && $order->billing['payment_status'] != 'partial_refund')) {
                throw new \Exception(trans('Ecommerce::exception.misc.order_not_paid'));
            }


            \Actions::do_action('pre_ecommerce_refund_order', $order, $amount);


            if ($type == "online") {

                $ecommerce = new Ecommerce($order->billing['gateway']);

                if (($type == "online") && !$ecommerce->gateway->getConfig('support_online_refund')) {
                    throw new \Exception(trans('Ecommerce::exception.misc.online_refund_not_supported', ['gateway' => $ecommerce->gateway->getName()]));
                }

                $refund_reference = $ecommerce->refundOrder($order, $amount, $type, $cancel);
            } else {

                $refund_reference = 'offline_' . \Str::random(6);
            }


            $user = user();

            Transaction::create([
                'code' => Transaction::getCode('TR'),
                'owner_type' => get_class($user),
                'owner_id' => $user->id ?? '',
                'invoice_id' => $order->invoice->id,
                'paid_currency' => $order->currency,
                'paid_amount' => $amount,
                'reference' => $refund_reference,
                'amount' => ($amount * -1),
                'sourcable_id' => $order->id,
                'sourcable_type' => get_class($order),
                'transaction_date' => Carbon::now(),
                'type' => 'order_refund',
                'notes' => 'Refund for order# ' . $order->id,
            ]);

            if ($order->amount > $order->getPaymentRefundedAmount()) {
                $payment_status = 'partial_refund';

            } else {
                $payment_status = 'refunded';

            }

            $order->update([
                'billing->payment_status' => $payment_status
            ]);


            if ($cancel) {
                $order->update([
                    'status' => 'canceled'
                ]);
            }

            $message = ['level' => 'success', 'message' => trans('Ecommerce::messages.refund.do_refund_order', ['item' => $order])];

        } catch (\Exception $exception) {
            log_exception($exception, 'OrderController', 'RefundOrder');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];

        }
        return response()->json($message);

    }
}


