<?php

namespace Corals\Modules\Amazon\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class ImportPresenter extends FractalPresenter
{

    /**
     * @param array $extras
     * @return ImportTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer($extras = [])
    {
        return new ImportTransformer($extras);
    }
}
