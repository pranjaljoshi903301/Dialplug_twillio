<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Ecommerce\DataTables\ShippingsDataTable;
use Corals\Modules\Ecommerce\Http\Requests\ShippingRequest;
use Corals\Modules\Ecommerce\Models\Shipping;
use Corals\Modules\Ecommerce\Services\ShippingService;

class ShippingsController extends BaseController
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;

        $this->resource_url = config('ecommerce.models.shipping.resource_url');
        $this->title = 'Ecommerce::module.shipping.title';
        $this->title_singular = 'Ecommerce::module.shipping.title_singular';
        parent::__construct();
    }

    /**
     * @param ShippingRequest $request
     * @param ShippingsDataTable $dataTable
     * @return mixed
     */
    public function index(ShippingRequest $request, ShippingsDataTable $dataTable)
    {
        return $dataTable->render('Ecommerce::shippings.index');
    }

    /**
     * @param ShippingRequest $request
     * @return $this
     */
    public function create(ShippingRequest $request)
    {
        $shipping = new Shipping();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Ecommerce::shippings.create_edit')->with(compact('shipping'));
    }

    /**
     * @param ShippingRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ShippingRequest $request)
    {
        try {
            $this->shippingService->store($request, Shipping::class);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Shipping::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return Shipping
     */
    public function show(ShippingRequest $request, Shipping $shipping)
    {
        if ($request->wantsJson()) {
            return [
                'id' => $shipping->id,
                'name' => $shipping->name,
                'description' => $shipping->description,
                'price' => $shipping->rate,
            ];
        }

        return $shipping;
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return $this
     */
    public function edit(ShippingRequest $request, Shipping $shipping)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $this->title_singular])]);

        return view('Ecommerce::shippings.create_edit')->with(compact('shipping'));
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ShippingRequest $request, Shipping $shipping)
    {
        try {
            $this->shippingService->update($request, $shipping);

            flash(trans('Corals::messages.success.updated', ['item' => trans('Ecommerce::module.shipping.index_title')]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Shipping::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ShippingRequest $request, Shipping $shipping)
    {
        try {
            $this->shippingService->destroy($request, $shipping);

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => trans('Ecommerce::module.shipping.index_title')])];
        } catch (\Exception $exception) {
            log_exception($exception, Shipping::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

}
