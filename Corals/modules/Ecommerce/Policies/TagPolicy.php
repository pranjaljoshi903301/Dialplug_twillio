<?php

namespace Corals\Modules\Ecommerce\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Ecommerce\Models\Tag;
use Corals\User\Models\User;

class TagPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.ecommerce';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('Ecommerce::tag.view');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('Ecommerce::tag.create');
    }

    /**
     * @param User $user
     * @param Tag $tag
     * @return bool
     */
    public function update(User $user, Tag $tag)
    {
        return $user->can('Ecommerce::tag.update');
    }

    /**
     * @param User $user
     * @param Tag $tag
     * @return bool
     */
    public function destroy(User $user, Tag $tag)
    {
        return $user->can('Ecommerce::tag.delete');
    }

}
