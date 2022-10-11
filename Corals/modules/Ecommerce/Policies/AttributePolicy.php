<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Attribute;
use Corals\User\Models\User;

class AttributePolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.ecommerce';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('Ecommerce::attribute.view');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Ecommerce::attribute.create');
    }

    /**
     * @param User $user
     * @param Attribute $attribute
     * @return bool
     */
    public function update(User $user, Attribute $attribute)
    {
        return $user->can('Ecommerce::attribute.update');
    }

    /**
     * @param User $user
     * @param Attribute $attribute
     * @return bool
     */
    public function destroy(User $user, Attribute $attribute)
    {
        return $user->can('Ecommerce::attribute.delete');
    }

}
