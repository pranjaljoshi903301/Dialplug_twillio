<?php

namespace Corals\Modules\BM\Providers;

use Corals\Modules\BM\Models\BitrixMobile;
use Corals\Modules\BM\Models\BMUsers;
use Corals\Modules\BM\Models\BMLines;
use Corals\Modules\BM\Policies\BitrixMobilePolicy;
use Corals\Modules\BM\Policies\BMUsersPolicy;
use Corals\Modules\BM\Policies\BMLinesPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class BMAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        BitrixMobile::class => BitrixMobilePolicy::class,
        BMUsers::class => BMUsersPolicy::class,
        BMLines::class => BMLinesPolicy::class,
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