<?php

namespace Corals\Modules\BT\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\BT\database\migrations\BTTables;
use Corals\Modules\BT\database\seeds\BTDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $module_public_path = __DIR__ . '/../public';
    
    protected $migrations = [
        BTTables::class
    ];

    protected function booted()
    {
        $this->createSchema();

        $btDatabaseSeeder = new BTDatabaseSeeder();

        $btDatabaseSeeder->run();
    }
}
