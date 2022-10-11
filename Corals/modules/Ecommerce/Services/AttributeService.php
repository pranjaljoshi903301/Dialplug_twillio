<?php

namespace Corals\Modules\Ecommerce\Services;

use Corals\Foundation\Services\BaseServiceClass;
use Corals\Modules\Ecommerce\Models\AttributeOption;

class AttributeService extends BaseServiceClass
{
    protected $excludedRequestParams = ['options'];

    public function postStoreUpdate($request, $additionalData = [])
    {
        $attribute = $this->model;

        $options = $request->get('options', []);

        $attribute_options = array_flip($attribute->options()->pluck('id')->toArray());

        foreach ($options as $option) {
            $option_id = $option['id'] ?? null;
            unset($attribute_options[$option_id]);
            AttributeOption::query()->updateOrCreate(['id' => $option_id, 'attribute_id' => $attribute->id], $option);
        }

        if (!empty($attribute_options)) {
            $attribute->options()->whereIn('id', array_keys($attribute_options))->forceDelete();
        }
    }
}
