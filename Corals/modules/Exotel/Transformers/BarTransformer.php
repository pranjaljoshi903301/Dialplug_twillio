<?php

namespace Corals\Modules\Exotel\Transformers;

use Corals\Foundation\Transformers\BaseTransformer;
use Corals\Modules\Exotel\Models\Bar;

class BarTransformer extends BaseTransformer
{
    public function __construct($extras = [])
    {
        $this->resource_url = config('exotel.models.bar.resource_url');

        parent::__construct($extras);
    }

    /**
     * @param Bar $bar
     * @return array
     * @throws \Throwable
     */
    public function transform(Bar $bar)
    {
        $show_url = $bar->getShowURL();

        $transformedArray = [
            'id' => $bar->id,
            'name' => HtmlElement('a', ['href' => $bar->getShowURL()], $bar->name),
            'subscriber_email' => $bar->subscriber_email,
            'bitrix_domain' => $bar->bitrix_domain,
            'created_at' => format_date($bar->created_at),
            'updated_at' => format_date($bar->updated_at),
            'action' => $this->actions($bar)
        ];

        return parent::transformResponse($transformedArray);
    }
}
