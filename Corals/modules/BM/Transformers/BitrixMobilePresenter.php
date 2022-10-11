<?php

namespace Corals\Modules\BM\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class BitrixMobilePresenter extends FractalPresenter
{

    /**
     * @return BitrixMobileTransformer
     */
    public function getTransformer()
    {
        return new BitrixMobileTransformer();
    }
}