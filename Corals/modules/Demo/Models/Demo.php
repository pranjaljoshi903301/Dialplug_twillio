<?php

namespace Corals\Modules\Demo\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Demo extends BaseModel
{
    use PresentableTrait, LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'demo.models.demo';

//    protected static $logAttributes = [];

    protected $guarded = ['id'];
}
