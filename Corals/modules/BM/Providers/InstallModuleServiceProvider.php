<?php

namespace Corals\Modules\BM\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\BM\database\migrations\BMTables;
use Corals\Modules\BM\database\seeds\BMDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $module_public_path = __DIR__ . '/../public';
    
    protected $migrations = [
        BMTables::class
    ];

    protected function booted()
    {
        
        // Change For Checking Subscription
        $this->createSchema();

        $bmDatabaseSeeder = new BMDatabaseSeeder();

        $bmDatabaseSeeder->run();
    }
}
