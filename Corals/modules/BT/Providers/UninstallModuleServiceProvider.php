<?php

namespace Corals\Modules\BT\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\Modules\BT\database\migrations\BTTables;
use Corals\Modules\BT\database\seeds\BTDatabaseSeeder;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [
        BTTables::class
    ];

    protected function booted()
    {
        $this->dropSchema();

        $btDatabaseSeeder = new BTDatabaseSeeder();
        
        $btDatabaseSeeder->rollback();
    }
}
