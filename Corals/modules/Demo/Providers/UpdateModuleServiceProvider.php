<?php

namespace Corals\Modules\Demo\Providers;

use Corals\Foundation\Providers\BaseUpdateModuleServiceProvider;

class UpdateModuleServiceProvider extends BaseUpdateModuleServiceProvider
{
    protected $module_code = 'corals-demo';
    protected $batches_path = __DIR__ . '/../update-batches/*.php';
}
