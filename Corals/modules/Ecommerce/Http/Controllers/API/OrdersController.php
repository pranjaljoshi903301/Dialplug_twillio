<?php

namespace Corals\Modules\Ecommerce\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Ecommerce\DataTables\MyOrdersDataTable;
use Corals\Modules\Ecommerce\Models\Order;
use Corals\Modules\Ecommerce\Services\OrderService;
use Corals\Modules\Ecommerce\Transformers\API\OrderItemPresenter;
use Corals\Modules\Ecommerce\Transformers\API\OrderPresenter;
use Illuminate\Http\Request;

class OrdersController extends APIBaseController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        $this->orderService->setPresenter(new OrderPresenter());

        parent::__construct();
    }

    /**
     * @param Request $request
     * @param MyOrdersDataTable $dataTable
     * @return mixed
     */
    public function myOrders(Request $request, MyOrdersDataTable $dataTable)
    {
        if (!user()->hasPermissionTo('Ecommerce::my_orders.access')) {
            abort(403, 'Forbidden!!');
        }

        $orders = $dataTable->query(new Order());

        return $this->orderService->index($orders, $dataTable);
    }

    public function show(Request $request, Order $order)
    {
        if (user()->cannot('view', $order)) {
            abort(403, 'Forbidden!!');
        }

        try {
            $response = [
                'order' => $this->orderService->getModelDetails($order),
                'items' => (new OrderItemPresenter())->present($order->items)['data'],
            ];

            return apiResponse($response);
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }
}
