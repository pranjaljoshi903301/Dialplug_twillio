<?php

namespace Corals\Modules\BM\Transformers\API;

use Corals\Foundation\Transformers\APIBaseTransformer;
use Corals\Modules\BM\Models\BitrixMobile;

class BitrixMobileTransformer extends APIBaseTransformer
{
    /**
     * @param BitrixMobile $bitrixmobile
     * @return array
     * @throws \Throwable
     */
    public function transform(BitrixMobile $bitrixmobile)
    {
        $transformedArray = [
            'id' => $bitrixmobile->id,
            'mobile_number' => $bitrixmobile->mobile_number,
            'created_at' => format_date($bitrixmobile->created_at),
            'updated_at' => format_date($bitrixmobile->updated_at),
        ];

        return parent::transformResponse($transformedArray);
    }
}