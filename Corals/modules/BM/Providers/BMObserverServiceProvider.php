<?php

namespace Corals\Modules\BM\Providers;

use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\BM\Observers\BitrixMobileObserver;
use Illuminate\Support\ServiceProvider;

class BMObserverServiceProvider extends ServiceProvider
{
    /**
     * Register Observers
     */
    public function boot()
    {

        BitrixMobile::observe(BitrixMobileObserver::class);
    }
}
