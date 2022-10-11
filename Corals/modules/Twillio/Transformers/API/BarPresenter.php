<?php

namespace Corals\Modules\Twillio\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class BarPresenter extends FractalPresenter
{

    /**
     * @return BarTransformer
     */
    public function getTransformer()
    {
        return new BarTransformer();
    }
}
