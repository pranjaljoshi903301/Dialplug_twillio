<?php

namespace Corals\Modules\Directory\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class ClaimPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return ClaimTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new ClaimTransformer($extras);
    }
}
