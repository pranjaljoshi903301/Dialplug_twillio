<?php

namespace Corals\Modules\Demo\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\Demo\database\seeds\DemosDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $migrations = [];

    protected function booted()
    {
        $this->createSchema();
        $demosDatabaseSeeder = new DemosDatabaseSeeder();

        $demosDatabaseSeeder->run();
    }
}
