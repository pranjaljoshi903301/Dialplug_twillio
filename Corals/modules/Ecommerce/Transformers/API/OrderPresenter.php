<?php

namespace Corals\Modules\Ecommerce\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class OrderPresenter extends FractalPresenter
{

    /**
     * @return OrderTransformer
     */
    public function getTransformer()
    {
        return new OrderTransformer();
    }
}
