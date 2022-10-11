<?php

namespace Corals\Modules\Ecommerce\Http\Controllers;

use Corals\Modules\CMS\Traits\SEOTools;
use Corals\Foundation\Http\Controllers\PublicBaseController;
use Corals\Modules\Ecommerce\Facades\Shop;
use Corals\Modules\Ecommerce\Models\Product;
use Illuminate\Http\Request;

class ShopPublicController extends PublicBaseController
{
    use SEOTools;

    /**
     * @param Request $request
     * @return $this
     */
    public function index(Request $request)
    {

        $this->setViewSharedData(['page_type' => 'product_archive']);


        $layout = $request->get('layout', 'grid');

        $products = Shop::getProducts($request);

        $item = [
            'title' => 'Shop',
            'meta_description' => 'E-commerce Shop',
            'url' => url('shop'),
            'type' => 'shop',
            'image' => \Settings::get('site_logo'),
            'meta_keywords' => 'shop,e-commerce,products'
        ];


        $this->setSEO((object)$item);

        $shopText = null;

        if ($request->has('search')) {
            $shopText = trans('Ecommerce::labels.shop.search_results_for', ['search' => strip_tags($request->get('search'))]);
        }

        $sortOptions = trans(config('ecommerce.models.shop.sort_options'));


        if (\Settings::get('ecommerce_rating_enable', true)) {
            $sortOptions['average_rating'] = trans('Ecommerce::attributes.product.average_rating');
        }

        return view('templates.shop')->with(compact('layout', 'products', 'shopText', 'sortOptions'));
    }

    public function show(Request $request, $slug)
    {

        $this->setViewSharedData(['page_type' => 'product_single']);

        $product = Product::active()->where('slug', $slug)->first();

        if (!$product) {
            abort(404);

        }

        $js_product = [
            [
                'name' => $product->name,
                'id' => $product->id,
                'price' => $product->price,
                'brand' => optional($product->brand)->name,
                'category' => optional($product->categories->first())->name,
                'list' => 'Product View Page',
            ]
        ];

        \JavaScript::put([
            'product' => $js_product
        ]);

        $categories = join(',', $product->activeCategories->pluck('name')->toArray());
        $tags = join(',', $product->activeTags->pluck('name')->toArray());

        $item = [
            'title' => $product->name,
            'meta_description' => \Str::limit(strip_tags($product->description), 500),
            'url' => url('shop/' . $product->slug),
            'type' => 'product',
            'image' => $product->image,
            'meta_keywords' => $categories . ',' . $tags
        ];

        $this->setSEO((object)$item);

        view()->share('product', $product);

        return view('templates/product_single')->with(compact('product'));
    }

}
