<?php

namespace Corals\Modules\Ecommerce\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class OrderPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return OrderTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new OrderTransformer($extras);
    }
}
