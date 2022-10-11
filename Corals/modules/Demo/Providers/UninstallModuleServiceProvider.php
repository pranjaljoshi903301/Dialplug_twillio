<?php

namespace Corals\Modules\Demo\Providers;

use Corals\Foundation\Providers\BaseUninstallModuleServiceProvider;
use Corals\User\Models\User;

class UninstallModuleServiceProvider extends BaseUninstallModuleServiceProvider
{
    protected $migrations = [];

    protected function booted()
    {
        User::where('email', 'like', '%@example.%')->delete();
        User::where('email', 'like', 'superuser@laraship.com')->delete();
    }
}
