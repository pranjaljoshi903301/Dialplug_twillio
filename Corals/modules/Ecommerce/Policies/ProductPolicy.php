<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Product;
use Corals\User\Models\User;

class ProductPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.ecommerce';

    protected $skippedAbilities = [
        'update', 'destroy', 'variations'
    ];

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Ecommerce::product.view')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Ecommerce::product.create');
    }

    /**
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function update(User $user, Product $product)
    {
        if ($product->status == 'deleted') {
            return false;
        }

        if ($user->can('Ecommerce::product.update') || isSuperUser()) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function destroy(User $user, Product $product)
    {
        if ($product->status == 'deleted' || $product->sku()->count() > 1) {
            return false;
        }


        if ($user->can('Ecommerce::product.delete') || isSuperUser()) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Product $product
     * @return bool
     */
    public function variations(User $user, Product $product)
    {
        return $product->type == "variable";
    }
}
