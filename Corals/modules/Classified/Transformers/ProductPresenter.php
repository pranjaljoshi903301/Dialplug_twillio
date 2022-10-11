<?php

namespace Corals\Modules\Classified\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class ProductPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return ProductTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new ProductTransformer($extras);
    }
}
