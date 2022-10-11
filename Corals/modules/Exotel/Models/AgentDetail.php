<?php

namespace Corals\Modules\Exotel\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class AgentDetail extends BaseModel
{
    use PresentableTrait, LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */

    public $table = 'exotel_db.exotel_agent_details';

    // protected static $logAttributes = [];

    // protected $casts = [
    //     'properties' => 'json',
    // ];

    // protected $guarded = ['id'];
}
