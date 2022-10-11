<?php

namespace Corals\Modules\Ecommerce\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class ProductPresenter extends FractalPresenter
{

    /**
     * @return ProductTransformer
     */
    public function getTransformer()
    {
        return new ProductTransformer();
    }
}
