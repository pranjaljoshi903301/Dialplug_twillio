<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Category;
use Corals\User\Models\User;

class CategoryPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.ecommerce';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('Ecommerce::category.view');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Ecommerce::category.create');
    }

    /**
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function update(User $user, Category $category)
    {
        return $user->can('Ecommerce::category.update');
    }

    /**
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function destroy(User $user, Category $category)
    {
        return $user->can('Ecommerce::category.delete');
    }

}
