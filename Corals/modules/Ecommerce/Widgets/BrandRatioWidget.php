<?php

namespace Corals\Modules\Ecommerce\Widgets;

use Corals\Modules\Ecommerce\Charts\BrandRatio;
use Corals\Modules\Ecommerce\Models\Brand;


class BrandRatioWidget
{

    function __construct()
    {
    }

    function run($args)
    {


        $data = Brand::withCount('products')
            ->get()->pluck('products_count', 'name')->toArray();



        $chart = new BrandRatio();
        $chart->labels(array_keys($data));
        $chart->dataset(trans('Ecommerce::labels.widget.products_by_brand'), 'pie', array_values($data));

        $chart->options([
            'plugins' => '{
                    colorschemes: {
                        scheme: \'brewer.Paired12\'
                    }
                }'
        ]);


        return view('Corals::chart')->with(['chart' => $chart])->render();
    }

}
