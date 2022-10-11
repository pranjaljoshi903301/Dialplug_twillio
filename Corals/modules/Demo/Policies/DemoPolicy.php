<?php

namespace Corals\Modules\Demo\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\User\Models\User;
use Corals\Modules\Demo\Models\Demo;

class DemoPolicy extends BasePolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('Demo::demo.view')) {
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
        return $user->can('Demo::demo.create');
    }

    /**
     * @param User $user
     * @param Demo $demo
     * @return bool
     */
    public function update(User $user, Demo $demo)
    {
        if ($user->can('Demo::demo.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Demo $demo
     * @return bool
     */
    public function destroy(User $user, Demo $demo)
    {
        if ($user->can('Demo::demo.delete')) {
            return true;
        }
        return false;
    }


    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        if (isSuperUser($user)) {
            return true;
        }

        return null;
    }
}
