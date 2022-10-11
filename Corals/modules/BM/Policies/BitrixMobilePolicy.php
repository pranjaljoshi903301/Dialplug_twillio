<?php

namespace Corals\Modules\BM\Policies;

use Corals\User\Models\User;
use Corals\Modules\BM\Models\BitrixMobile;

class BitrixMobilePolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('BM::bitrixmobile.view')) {
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
        return $user->can('BM::bitrixmobile.create');
    }

    /**
     * @param User $user
     * @param BitrixMobile $bitrixmobile
     * @return bool
     */
    public function update(User $user, BitrixMobile $bitrixmobile)
    {
        // if ($user->can('BM::bitrixmobile.update')) {
        //     return true;
        // }
        // return false;
        return true;
    }

    /**
     * @param User $user
     * @param BitrixMobile $bitrixmobile
     * @return bool
     */
    public function destroy(User $user, BitrixMobile $bitrixmobile)
    {
        // if ($user->can('BM::bitrixmobile.delete')) {
        //     return true;
        // }
        // return false;
        return true;
    }


    /**
     * @param $user
     * @param $ability
     * @return bool
     */
    public function before($user, $ability)
    {
        // if (isSuperUser($user)) {
        //     return true;
        // }

        // return null;
        return true;
    }
}
