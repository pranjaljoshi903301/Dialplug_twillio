<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Coupon;
use Corals\User\Models\User;

class CouponPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.ecommerce';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('Ecommerce::coupon.view');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Ecommerce::coupon.create');
    }

    /**
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function update(User $user, Coupon $coupon)
    {
        return $user->can('Ecommerce::coupon.update');
    }

    /**
     * @param User $user
     * @param Coupon $coupon
     * @return bool
     */
    public function destroy(User $user, Coupon $coupon)
    {
        return $user->can('Ecommerce::coupon.delete');
    }

}
