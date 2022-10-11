<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Ecommerce\DataTables\BrandsDataTable;
use Corals\Modules\Ecommerce\Http\Requests\BrandRequest;
use Corals\Foundation\Http\Requests\BulkRequest;
use Corals\Modules\Ecommerce\Models\Brand;
use Corals\Modules\Ecommerce\Services\BrandService;

class BrandsController extends BaseController
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;

        $this->resource_url = config('ecommerce.models.brand.resource_url');
        $this->title = 'Ecommerce::module.brand.title';
        $this->title_singular = 'Ecommerce::module.brand.title_singular';

        parent::__construct();
    }

    /**
     * @param BrandRequest $request
     * @param BrandsDataTable $dataTable
     * @return mixed
     */
    public function index(BrandRequest $request, BrandsDataTable $dataTable)
    {
        return $dataTable->render('Ecommerce::brands.index');
    }

    /**
     * @param BrandRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(BrandRequest $request)
    {
        $brand = new Brand();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Ecommerce::brands.create_edit')->with(compact('brand'));
    }

    /**
     * @param BrandRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(BrandRequest $request)
    {
        try {

            $this->brandService->store($request, Brand::class);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Brand::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BrandRequest $request
     * @param Brand $brand
     * @return Brand
     */
    public function show(BrandRequest $request, Brand $brand)
    {
        return $brand;
    }

    /**
     * @param BrandRequest $request
     * @param Brand $brand
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(BrandRequest $request, Brand $brand)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $brand->name])]);

        return view('Ecommerce::brands.create_edit')->with(compact('brand'));
    }

    /**
     * @param BrandRequest $request
     * @param Brand $brand
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        try {
            $this->brandService->update($request, $brand);

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Brand::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param BulkRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAction(BulkRequest $request)
    {
        try {
            $action = $request->input('action');
            $selection = json_decode($request->input('selection'), true);
            switch ($action) {
                case 'delete':
                    foreach ($selection as $selection_id) {
                        $brand = Brand::findByHash($selection_id);
                        $brand_request = new BrandRequest;
                        $brand_request->setMethod('DELETE');
                        $this->destroy($brand_request, $brand);
                    }
                    $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
                    break;
                case 'active' :
                    foreach ($selection as $selection_id) {
                        $brand = Brand::findByHash($selection_id);
                        if (user()->can('Ecommerce::product.update')) {
                            $brand->update([
                                'status' => 'active'
                            ]);
                            $brand->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;

                case 'inActive' :
                    foreach ($selection as $selection_id) {
                        $brand = Brand::findByHash($selection_id);
                        if (user()->can('Ecommerce::product.update')) {
                            $brand->update([
                                'status' => 'inactive'
                            ]);
                            $brand->save();
                            $message = ['level' => 'success', 'message' => trans('Ecommerce::attributes.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Ecommerce::attributes.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
            }
        } catch (\Exception $exception) {
            log_exception($exception, Brand::class, 'bulkAction');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }
        return response()->json($message);
    }

    /**
     * @param BrandRequest $request
     * @param Brand $brand
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BrandRequest $request, Brand $brand)
    {
        try {
            $this->brandService->destroy($request, $brand);

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Brand::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }
}
