<?php

namespace Corals\Modules\Ecommerce\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class CategoryPresenter extends FractalPresenter
{

    /**
     * @return CategoryTransformer
     */
    public function getTransformer()
    {
        return new CategoryTransformer();
    }
}
