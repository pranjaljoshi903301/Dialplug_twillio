<?php

namespace Corals\Modules\Classified\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Classified\DataTables\ProductsDataTable;
use Corals\Foundation\Http\Requests\BulkRequest;
use Corals\Modules\Classified\Facades\Classified as ClassifiedFacade;
use Corals\Modules\Classified\Http\Requests\ProductRequest;
use Corals\Modules\Classified\Models\Product;
use Corals\Modules\Utility\Facades\Category\Category;

class ProductsController extends BaseController
{
    protected $excludedRequestParams = ['categories', 'options'];

    public $view_prefix = '';

    public function __construct()
    {
        $this->title = 'Classified::module.product.title';
        $this->title_singular = 'Classified::module.product.title_singular';

        $this->setVariables();

        parent::__construct();
    }

    public function setVariables()
    {
        $this->resource_url = config('classified.models.product.resource_url');
        $this->view_prefix = 'Classified::products';
    }

    /**
     * @param ProductRequest $request
     * @param ProductsDataTable $dataTable
     * @return mixed
     */
    public function index(ProductRequest $request, ProductsDataTable $dataTable)
    {
        return $dataTable->render('Classified::products.index');
    }

    /**
     * @param ProductRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function create(ProductRequest $request)
    {
        $subscriptionStatus = [];

        if (user()->cannot('Classified::product.manage')) {
            try {
                $subscriptionStatus = ClassifiedFacade::getUserSubscriptionStatus();
                if ($subscriptionStatus && $subscriptionStatus['products_count']['limit_reached']) {
                    throw new \Exception($subscriptionStatus['products_count']['message']);
                }
            } catch (\Exception $exception) {
                log_exception($exception, Product::class, 'create');
                return redirectTo($this->resource_url);
            }
        }

        $product = new Product();

        $statusOptions = get_array_key_translation(config('classified.models.product.status_options'));

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view($this->view_prefix . '.create_edit')->with(compact('product', 'statusOptions', 'subscriptionStatus'));
    }


    /**
     * @param ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            $data['price_on_call'] = \Arr::get($data, 'price_on_call', false);

            $data['user_id'] = user()->id;

            $subscriptionStatus = ClassifiedFacade::getUserSubscriptionStatus();

            if (($subscriptionStatus && $subscriptionStatus['feature_products_count']['limit_reached'])
                || (!$subscriptionStatus && user()->cannot('Classified::product.manage'))) {
                unset($data['is_featured']);
            }

            if (\Arr::has($this->excludedRequestParams, 'verified')) {
                unset($data['verified']);
            }

            $product = Product::query()->create($data);

            $categories = $request->get('categories', []);

            $product->categories()->sync($categories);

            $product->indexRecord();

            Category::setModelOptions($request, $product);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Product::class, 'store');
        }

        return redirectTo($product->getEditUrl($this->resource_url));
    }

    /**
     * @param $request
     * @param $product
     */

    /**
     * @param ProductRequest $request
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ProductRequest $request, Product $product)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $product->name])]);

        $statusOptions = get_array_key_translation(config('classified.models.product.status_options'));

        $subscriptionStatus = ClassifiedFacade::getUserSubscriptionStatus($product->user);

        return view($this->view_prefix . '.create_edit')->with(compact('product', 'statusOptions', 'subscriptionStatus'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            $data['price_on_call'] = \Arr::get($data, 'price_on_call', false);

            $subscriptionStatus = ClassifiedFacade::getUserSubscriptionStatus($product->user);

            if (!$product->is_featured) {
                if (($subscriptionStatus && $subscriptionStatus['feature_products_count']['limit_reached'])
                    || (!$subscriptionStatus && user()->cannot('Classified::product.manage'))) {
                    unset($data['is_featured']);
                }
            }

            $data['is_featured'] = \Arr::get($data, 'is_featured', false);

            if (!\Arr::has($this->excludedRequestParams, 'verified')) {
                $data['verified'] = \Arr::get($data, 'verified', false);
            }

            $product->update($data);

            $categories = $request->get('categories', []);

            $product->categories()->sync($categories);

            $product->indexRecord();

            Category::setModelOptions($request, $product);

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Product::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    public function bulkAction(BulkRequest $request)
    {
        try {

            $action = $request->input('action');
            $selection = json_decode($request->input('selection'), true);

            switch ($action) {
                case 'delete':
                    foreach ($selection as $selection_id) {
                        $product = Product::findByHash($selection_id);
                        $product_request = new ProductRequest;
                        $product_request->setMethod('DELETE');
                        $this->destroy($product_request, $product);
                    }
                    $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
                    break;

                case 'active' :
                    foreach ($selection as $selection_id) {
                        $product = Product::findByHash($selection_id);
                        if (user()->can('Classified::product.update')) {
                            $product->update([
                                'status' => 'active'
                            ]);
                            $product->save();
                            $message = ['level' => 'success', 'message' => trans('Classified::attributes.product.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Classified::attributes.product.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;

                case 'inActive' :
                    foreach ($selection as $selection_id) {
                        $product = Product::findByHash($selection_id);
                        if (user()->can('Classified::product.update')) {
                            $product->update([
                                'status' => 'inactive'
                            ]);
                            $product->save();
                            $message = ['level' => 'success', 'message' => trans('Classified::attributes.product.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Classified::attributes.product.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'sold' :
                    foreach ($selection as $selection_id) {
                        $product = Product::findByHash($selection_id);
                        if (user()->can('Classified::product.update')) {
                            $product->update([
                                'status' => 'sold'
                            ]);
                            $product->save();
                            $message = ['level' => 'success', 'message' => trans('Classified::attributes.product.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Classified::attributes.product.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
                case 'archived' :
                    foreach ($selection as $selection_id) {
                        $product = Product::findByHash($selection_id);
                        if (user()->can('Classified::product.update')) {
                            $product->update([
                                'status' => 'archived'
                            ]);
                            $product->save();
                            $message = ['level' => 'success', 'message' => trans('Classified::attributes.product.update_status', ['item' => $this->title_singular])];
                        } else {
                            $message = ['level' => 'error', 'message' => trans('Classified::attributes.product.no_permission', ['item' => $this->title_singular])];
                        }
                    }
                    break;
            }
        } catch (\Exception $exception) {
            log_exception($exception, Product::class, 'bulkAction');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }
        return response()->json($message);
    }

    public function destroy(ProductRequest $request, Product $product)
    {
        try {
            $product->clearMediaCollection($product->galleryMediaCollection);

            $product->delete();

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular]), 'class' => $product->slug];
        } catch (\Exception $exception) {
            log_exception($exception, Product::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }


}
