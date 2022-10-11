<?php

namespace Corals\Modules\Twillio\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class BarPresenter extends FractalPresenter
{

    /**
     * @return BarTransformer
     */
    public function getTransformer($extras = [])
    {
        return new BarTransformer($extras);
    }
}
