<?php

namespace Corals\Modules\Ecommerce\Transformers\API;

use Corals\Foundation\Transformers\APIBaseTransformer;
use Corals\Modules\Ecommerce\Models\Product;

class ProductTransformer extends APIBaseTransformer
{
    /**
     * @param Product $product
     * @return array
     * @throws \Throwable
     */
    public function transform(Product $product)
    {
        $sku = null;

        if ($product->type == "simple") {
            $sku = (new SKUPresenter())->present($product->activeSKU(true))['data'];
        }

        $transformedArray = [
            'id' => $product->id,
            'image' => $product->image,
            'name' => $product->name,
            'slug' => $product->slug,
            'is_featured' => $product->is_featured,
            'external_url' => $product->external_url,
            'price' => strip_tags($product->price),
            'regular_price' => $product->regular_price,
            'discount' => $product->discount,
            'is_simple' => $product->type == "simple",
            'brand' => $product->brand ? $product->brand->name : null,
            'caption' => $product->caption,
            'shippable' => boolval($product->shipping['enabled']),
            'status' => $product->status,
            'categories' => $product->activeCategories->pluck('name')->toArray(),
            'tags' => $product->activeTags->pluck('name')->toArray(),
            'description' => $product->description,
            'global_attributes' => apiPluck($product->globalOptions->pluck('label', 'id'), 'id', 'label'),
            'variation_attributes' => apiPluck($product->variationOptions->pluck('label', 'id'), 'id', 'label'),
            'tax_classes' => apiPluck($product->tax_classes()->pluck('tax_classes.name', 'tax_classes.id'), 'id', 'name'),
            'sku' => $sku,
            'created_at' => format_date($product->created_at),
            'updated_at' => format_date($product->updated_at),
        ];

        return parent::transformResponse($transformedArray);
    }
}
