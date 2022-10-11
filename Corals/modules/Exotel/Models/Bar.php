<?php

namespace Corals\Modules\Exotel\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Bar extends BaseModel
{
    use PresentableTrait, LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */
    // public $config = 'exotel.models.bar';

    public $table = 'exotel_db.exotel_details';    
    // protected static $logAttributes = [];

    // protected $casts = [
    //     'properties' => 'json',
    // ];

    // protected $guarded = ['id'];
}
