<?php

namespace Corals\Modules\BT\Providers;

use Corals\Modules\BT\Models\BitrixTelephony;
use Corals\Modules\BT\Models\BTUsers;
use Corals\Modules\BT\Policies\BitrixTelephonyPolicy;
use Corals\Modules\BT\Policies\BTUsersPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class BTAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        BitrixTelephony::class => BitrixTelephonyPolicy::class,
        BTUsers::class => BTUsersPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}