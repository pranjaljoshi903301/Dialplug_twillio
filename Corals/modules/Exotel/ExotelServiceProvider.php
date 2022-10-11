<?php

namespace Corals\Modules\Exotel;

use Corals\Modules\Exotel\Facades\Exotel;
use Corals\Modules\Exotel\Models\Bar;
use Corals\Modules\Exotel\Providers\ExotelAuthServiceProvider;
use Corals\Modules\Exotel\Providers\ExotelObserverServiceProvider;
use Corals\Modules\Exotel\Providers\ExotelRouteServiceProvider;

use Corals\Settings\Facades\Settings;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ExotelServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */

    public function boot()
    {
        // Load view
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Exotel');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Exotel');

        // Load migrations
//        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->registerCustomFieldsModels();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/exotel.php', 'exotel');

        $this->app->register(ExotelRouteServiceProvider::class);
        $this->app->register(ExotelAuthServiceProvider::class);
        $this->app->register(ExotelObserverServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Exotel', Exotel::class);
        });
    }

    protected function registerCustomFieldsModels()
    {
        Settings::addCustomFieldModel(Bar::class);
    }
}
