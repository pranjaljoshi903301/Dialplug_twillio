<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Brand;
use Corals\User\Models\User;

class BrandPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.ecommerce';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('Ecommerce::brand.view');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Ecommerce::brand.create');
    }

    /**
     * @param User $user
     * @param Brand $brand
     * @return bool
     */
    public function update(User $user, Brand $brand)
    {
        return $user->can('Ecommerce::brand.update');
    }

    /**
     * @param User $user
     * @param Brand $brand
     * @return bool
     */
    public function destroy(User $user, Brand $brand)
    {
        return $user->can('Ecommerce::brand.delete');
    }

}
