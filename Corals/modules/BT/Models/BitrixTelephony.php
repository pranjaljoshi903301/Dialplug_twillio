<?php

namespace Corals\Modules\BT\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class BitrixTelephony extends BaseModel
{
    use PresentableTrait, LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */

    public $table = "bt_config";
    public $config = 'bt.models.bitrixtelephony';

    protected static $logAttributes = [];

    protected $guarded = ['id'];
}
