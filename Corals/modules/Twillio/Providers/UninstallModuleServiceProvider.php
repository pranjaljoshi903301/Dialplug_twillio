<?php

namespace Corals\Modules\Twillio\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\Modules\Twillio\database\migrations\TwillioTables;
use Corals\Modules\Twillio\database\seeds\TwillioDatabaseSeeder;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [
        TwillioTables::class
    ];

    protected function booted()
    {
        $this->dropSchema();

        $twillioDatabaseSeeder = new TwillioDatabaseSeeder();

        $twillioDatabaseSeeder->rollback();
    }
}
