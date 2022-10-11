<?php

namespace Corals\Modules\Ecommerce\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class CouponPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return CouponTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new CouponTransformer($extras);
    }
}
