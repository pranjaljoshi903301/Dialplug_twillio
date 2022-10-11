<?php

namespace Corals\Modules\Ecommerce\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class SKUPresenter extends FractalPresenter
{

    /**
     * @return SKUTransformer
     */
    public function getTransformer($extras = [])
    {
        return new SKUTransformer($extras);
    }
}
