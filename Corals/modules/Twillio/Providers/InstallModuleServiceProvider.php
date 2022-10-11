<?php

namespace Corals\Modules\Twillio\Providers;

use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;
use Corals\Modules\Twillio\database\migrations\TwillioTables;
use Corals\Modules\Twillio\database\seeds\TwillioDatabaseSeeder;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected $module_public_path = __DIR__ . '/../public';

    protected $migrations = [
        TwillioTables::class
    ];

    protected function booted()
    {
        $this->createSchema();

        $twillioDatabaseSeeder = new TwillioDatabaseSeeder();

        $twillioDatabaseSeeder->run();
    }
}
