<?php

namespace Corals\Modules\Payment\AuthorizeNet\Providers;

use Corals\Foundation\Providers\BaseUpdateModuleServiceProvider;

class UpdateModuleServiceProvider extends BaseUpdateModuleServiceProvider
{
    protected $module_code = 'corals-payment-authorizenet';
    protected $batches_path = __DIR__ . '/../update-batches/*.php';
}
