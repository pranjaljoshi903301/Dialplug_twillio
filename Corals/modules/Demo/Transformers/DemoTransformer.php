<?php

namespace Corals\Modules\Demo\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Demo\Models\Demo;

class DemoTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('demo.models.demo.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Demo $demo
     * @return array
     * @throws \Throwable
     */
    public function transform(Demo $demo)
    {
        $transformedArray = [
            'id' => $demo->id,
            'created_at' => format_date($demo->created_at),
            'updated_at' => format_date($demo->updated_at),
            'action' => $this->actions($demo, [])
        ];

        return parent::transformResponse($transformedArray);
    }
}
