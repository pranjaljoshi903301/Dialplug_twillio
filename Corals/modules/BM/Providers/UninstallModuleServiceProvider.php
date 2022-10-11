<?php

namespace Corals\Modules\BM\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\Modules\BM\database\migrations\BMTables;
use Corals\Modules\BM\database\seeds\BMDatabaseSeeder;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [
        BMTables::class
    ];

    protected function booted()
    {
        $this->dropSchema();

        $bmDatabaseSeeder = new BMDatabaseSeeder();
        
        $bmDatabaseSeeder->rollback();
    }
}
