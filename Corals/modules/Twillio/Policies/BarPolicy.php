<?php

namespace Corals\Modules\Twillio\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\User\Models\User;
use Corals\Modules\Twillio\Models\Bar;

class BarPolicy extends BasePolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Twillio::bar.view')) {
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
        return $user->can('Twillio::bar.create');
    }

    /**
     * @param User $user
     * @param Bar $bar
     * @return bool
     */
    public function update(User $user, Bar $bar)
    {
        if ($user->can('Twillio::bar.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Bar $bar
     * @return bool
     */
    public function destroy(User $user, Bar $bar)
    {
        if ($user->can('Twillio::bar.delete')) {
            return true;
        }
        return false;
    }

}
