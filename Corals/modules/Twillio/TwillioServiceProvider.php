<?php

namespace Corals\Modules\Twillio;

use Corals\Modules\Twillio\Facades\Twillio;
use Corals\Modules\Twillio\Models\Bar;
use Corals\Modules\Twillio\Providers\TwillioAuthServiceProvider;
use Corals\Modules\Twillio\Providers\TwillioObserverServiceProvider;
use Corals\Modules\Twillio\Providers\TwillioRouteServiceProvider;

use Corals\Settings\Facades\Settings;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class TwillioServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Twillio');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Twillio');

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
        $this->mergeConfigFrom(__DIR__ . '/config/twillio.php', 'twillio');

        $this->app->register(TwillioRouteServiceProvider::class);
        $this->app->register(TwillioAuthServiceProvider::class);
        $this->app->register(TwillioObserverServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Twillio', Twillio::class);
        });
    }

    protected function registerCustomFieldsModels()
    {
        Settings::addCustomFieldModel(Bar::class);
    }
}
