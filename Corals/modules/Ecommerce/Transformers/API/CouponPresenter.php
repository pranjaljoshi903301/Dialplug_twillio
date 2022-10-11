<?php

namespace Corals\Modules\Ecommerce\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class CouponPresenter extends FractalPresenter
{

    /**
     * @return CouponTransformer
     */
    public function getTransformer()
    {
        return new CouponTransformer();
    }
}
