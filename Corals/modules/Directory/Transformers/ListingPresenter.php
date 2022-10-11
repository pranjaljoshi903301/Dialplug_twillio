<?php

namespace Corals\Modules\Directory\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class ListingPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return ListingTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new ListingTransformer($extras);
    }
}
