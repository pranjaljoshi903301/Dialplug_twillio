<?php

namespace Corals\Modules\Exotel\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class UsageDetail extends BaseModel
{
    use PresentableTrait, LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */

    public $table = 'Dialplug_Misc.audit_bitrix_exotel_plugin';

    // protected static $logAttributes = [];

    // protected $casts = [
    //     'properties' => 'json',
    // ];

    // protected $guarded = ['id'];
}
