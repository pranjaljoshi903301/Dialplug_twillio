<?php

namespace Corals\Modules\Demo\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class DemoPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return DemoTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new DemoTransformer($extras);
    }
}
