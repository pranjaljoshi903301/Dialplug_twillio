<?php

namespace Corals\Modules\Ecommerce\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class AttributePresenter extends FractalPresenter
{

    /**
     * @return AttributeTransformer
     */
    public function getTransformer()
    {
        return new AttributeTransformer();
    }
}
