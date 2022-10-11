<?php

namespace Corals\Modules\Ecommerce\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class BrandPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return BrandTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new BrandTransformer($extras);
    }
}
