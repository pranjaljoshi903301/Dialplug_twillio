<?php

namespace Corals\Modules\Demo\Providers;

use Corals\Modules\Demo\Models\Demo;
use Corals\Modules\Demo\Observers\DemoObserver;
use Illuminate\Support\ServiceProvider;

class DemoObserverServiceProvider extends ServiceProvider
{
    /**
     * Register Observers
     */
    public function boot()
    {
        Demo::observe(DemoObserver::class);
    }
}
