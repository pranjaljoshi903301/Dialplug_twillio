<?php

namespace Corals\Modules\Exotel\Providers;

use Corals\Modules\Exotel\Models\Bar;
use Corals\Modules\Exotel\Observers\BarObserver;
use Illuminate\Support\ServiceProvider;

class ExotelObserverServiceProvider extends ServiceProvider
{
    /**
     * Register Observers
     */
    public function boot()
    {

        Bar::observe(BarObserver::class);
    }
}
