<?php

namespace Corals\Modules\Twillio\Providers;

use Corals\Modules\Twillio\Models\Bar;
use Corals\Modules\Twillio\Observers\BarObserver;
use Illuminate\Support\ServiceProvider;

class TwillioObserverServiceProvider extends ServiceProvider
{
    /**
     * Register Observers
     */
    public function boot()
    {

        Bar::observe(BarObserver::class);
    }
}
