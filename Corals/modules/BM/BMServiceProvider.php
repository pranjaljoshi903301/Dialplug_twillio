<?php

namespace Corals\Modules\BM;

use Corals\Modules\BM\Facades\BM;
use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\BM\Providers\BMAuthServiceProvider;
use Corals\Modules\BM\Providers\BMObserverServiceProvider;
use Corals\Modules\BM\Providers\BMRouteServiceProvider;

use Corals\Settings\Facades\Settings;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class BMServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'BM');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'BM');

        // Load migrations
        // $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->registerCustomFieldsModels();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/bm.php', 'bm');

        $this->app->register(BMRouteServiceProvider::class);
        $this->app->register(BMAuthServiceProvider::class);
        $this->app->register(BMObserverServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('BM', BM::class);
        });
    }

    protected function registerCustomFieldsModels()
    {
        Settings::addCustomFieldModel(BitrixMobile::class);
    }
}
