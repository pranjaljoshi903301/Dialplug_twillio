<?php

namespace Corals\Modules\Ecommerce\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Shipping extends BaseModel
{
    use PresentableTrait, LogsActivity;

    protected $table = 'ecommerce_shippings';

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'ecommerce.models.shipping';

    protected static $logAttributes = ['priority', 'shipping_method', 'rate', 'country',
        'description', 'name', 'min_order_total', 'exclusive'];

    protected $guarded = ['id'];
}
