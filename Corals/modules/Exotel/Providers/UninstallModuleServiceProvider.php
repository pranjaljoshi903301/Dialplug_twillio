<?php

namespace Corals\Modules\Exotel\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\Modules\Exotel\database\migrations\ExotelTables;
use Corals\Modules\Exotel\database\seeds\ExotelDatabaseSeeder;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [
        ExotelTables::class
    ];

    protected function booted()
    {
        $this->dropSchema();

        $exotelDatabaseSeeder = new ExotelDatabaseSeeder();

        $exotelDatabaseSeeder->rollback();
    }
}
