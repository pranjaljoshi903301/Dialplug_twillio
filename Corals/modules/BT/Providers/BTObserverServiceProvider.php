<?php

namespace Corals\Modules\BT\Providers;

use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Observers\BitrixTelephonyObserver;
use Illuminate\Support\ServiceProvider;

class BTObserverServiceProvider extends ServiceProvider
{
    /**
     * Register Observers
     */
    public function boot()
    {

        BitrixTelephony::observe(BitrixTelephonyObserver::class);
    }
}
