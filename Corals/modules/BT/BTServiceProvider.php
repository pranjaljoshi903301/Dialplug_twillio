<?php

namespace Corals\Modules\BT;

use Corals\Modules\BT\Facades\BT;
use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Models\BTUsers;
use Corals\Modules\BT\Providers\BTAuthServiceProvider;
use Corals\Modules\BT\Providers\BTObserverServiceProvider;
use Corals\Modules\BT\Providers\BTRouteServiceProvider;

use Corals\Settings\Facades\Settings;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class BTServiceProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'BT');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'BT');

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
        $this->mergeConfigFrom(__DIR__ . '/config/bt.php', 'bt');

        $this->app->register(BTRouteServiceProvider::class);
        $this->app->register(BTAuthServiceProvider::class);
        $this->app->register(BTObserverServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('BT', BT::class);
        });
    }

    protected function registerCustomFieldsModels()
    {
        Settings::addCustomFieldModel(BitrixTelephony::class);
        Settings::addCustomFieldModel(BTUsers::class);
    }
}
