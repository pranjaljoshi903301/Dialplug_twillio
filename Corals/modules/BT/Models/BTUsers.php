<?php

namespace Corals\Modules\BT\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class BTUsers extends BaseModel
{
    use PresentableTrait, LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */

    public $table = "bt_users";
    public $config = 'bt.models.user';

    protected static $logAttributes = [];

    protected $guarded = ['id'];
}
