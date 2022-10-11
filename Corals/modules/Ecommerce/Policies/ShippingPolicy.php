<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Shipping;
use Corals\User\Models\User;

class ShippingPolicy extends BasePolicy
{

    protected $administrationPermission = 'Administrations::admin.ecommerce';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * @param User $user
     * @param Shipping $shipping
     * @return bool
     */
    public function update(User $user, Shipping $shipping)
    {
        return false;
    }

    /**
     * @param User $user
     * @param Shipping $shipping
     * @return bool
     */
    public function destroy(User $user, Shipping $shipping)
    {
        return false;
    }
}
