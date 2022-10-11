<?php

namespace Corals\Modules\Ecommerce\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Ecommerce\Facades\Shop;
use Corals\Modules\Ecommerce\Models\Product;
use Corals\Modules\Ecommerce\Transformers\API\ProductPresenter;
use Corals\Modules\Ecommerce\Transformers\API\SKUPresenter;
use Illuminate\Http\Request;

class ShopController extends APIBaseController
{
    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->corals_middleware_except = array_merge($this->corals_middleware_except,
            ['productsList', 'singleProduct']);
        parent::__construct();
    }

    /**
     * @param $permission
     */
    private function canAccess($permission)
    {
        if (!user()->hasPermissionTo($permission)) {
            abort(403, 'Forbidden!!');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsList(Request $request)
    {
        try {
            $products = Shop::getProducts($request);

            return (new ProductPresenter())->present($products);
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function singleProduct(Request $request, Product $product)
    {
        try {
            $product->setPresenter(new ProductPresenter());

            $skus = (new SKUPresenter())->present($product->activeSKU)['data'];

            $singleProductResponse = [
                'product' => $product->presenter(),
                'skus' => $skus
            ];

            return apiResponse($singleProductResponse);
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function settings(Request $request)
    {
        try {
            $this->canAccess('Ecommerce::settings.access');

            $settings = config('ecommerce.settings');

            $settingsList = [];

            foreach ($settings as $setting_key => $setting_items) {
                foreach ($setting_items as $key => $setting) {
                    $setting_concat = 'ecommerce_' . strtolower($setting_key) . '_' . $key;

                    $settingsList[$setting_key][$key] = \Settings::get($setting_concat);
                }
            }
            return apiResponse($settingsList);
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }
}
