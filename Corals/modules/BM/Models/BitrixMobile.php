<?php

namespace Corals\Modules\BM\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class BitrixMobile extends BaseModel
{
    use PresentableTrait, LogsActivity;

    /**
     *  Model configuration.
     * @var string
     */

    public $table = "bm_config";
    public $config = 'bm.models.bitrixmobile';

    protected static $logAttributes = [];

    protected $guarded = ['id'];
}
