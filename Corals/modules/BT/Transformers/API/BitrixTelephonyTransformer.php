<?php

namespace Corals\Modules\BT\Transformers\API;

use Corals\Foundation\Transformers\APIBaseTransformer;
use Corals\Modules\BT\Models\BitrixTelephony;

class BitrixTelephonyTransformer extends APIBaseTransformer
{
    /**
     * @param BitrixTelephony $bitrixtelephony
     * @return array
     * @throws \Throwable
     */
    public function transform(BitrixTelephony $bitrixtelephony)
    {
        $transformedArray = [
            'id' => $bitrixtelephony->id,
            'name' => $bitrixtelephony->name,
            'created_at' => format_date($bitrixtelephony->created_at),
            'updated_at' => format_date($bitrixtelephony->updated_at),
        ];

        return parent::transformResponse($transformedArray);
    }
}