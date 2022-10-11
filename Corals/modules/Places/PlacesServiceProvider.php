<?php

namespace Corals\Modules\Places;

use Corals\Modules\Places\Console\Commands\RunImports;
use Corals\Modules\Places\Facades\Places;
use Corals\Modules\Places\Models\Import as Import;
use Corals\Modules\Places\Providers\PlacesAuthServiceProvider;
use Corals\Modules\Places\Providers\PlacesObserverServiceProvider;
use Corals\Modules\Places\Providers\PlacesRouteServiceProvider;

use Corals\Settings\Facades\Settings;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class PlacesServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Places');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Places');

        // Load migrations
//        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->registerCustomFieldsModels();
        $this->registerCommand();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/places.php', 'places');

        $this->app->register(PlacesRouteServiceProvider::class);
        $this->app->register(PlacesAuthServiceProvider::class);
        $this->app->register(PlacesObserverServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Places', Places::class);
        });
    }

    protected function registerCustomFieldsModels()
    {
        Settings::addCustomFieldModel(Import::class);
    }

    protected function registerCommand()
    {
        $this->commands(RunImports::class);
    }

}
