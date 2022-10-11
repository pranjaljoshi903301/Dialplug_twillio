<?php

namespace Corals\Modules\Exotel\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\Exotel\database\migrations\ExotelTables;
use Corals\Modules\Exotel\database\seeds\ExotelDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $module_public_path = __DIR__ . '/../public';

    protected $migrations = [
        ExotelTables::class
    ];

    protected function booted()
    {
        $this->createSchema();

        $exotelDatabaseSeeder = new ExotelDatabaseSeeder();

        $exotelDatabaseSeeder->run();
    }
}
