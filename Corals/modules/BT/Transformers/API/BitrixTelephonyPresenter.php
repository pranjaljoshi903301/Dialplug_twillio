<?php

namespace Corals\Modules\BT\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class BitrixTelephonyPresenter extends FractalPresenter
{

    /**
     * @return BitrixTelephonyTransformer
     */
    public function getTransformer()
    {
        return new BitrixTelephonyTransformer();
    }
}