<?php

namespace Corals\Modules\BT\Policies;

use Corals\User\Models\User;
use Corals\Modules\BT\Models\BitrixTelephony;

class BitrixTelephonyPolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if ($user->can('BT::bitrixtelephony.view')) {
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
        return $user->can('BT::bitrixtelephony.create');
    }

    /**
     * @param User $user
     * @param BitrixTelephony $bitrixtelephony
     * @return bool
     */
    public function update(User $user, BitrixTelephony $bitrixtelephony)
    {
        // if ($user->can('BT::bitrixtelephony.update')) {
        //     return true;
        // }
        // return false;
        return true;
    }

    /**
     * @param User $user
     * @param BitrixTelephony $bitrixtelephony
     * @return bool
     */
    public function destroy(User $user, BitrixTelephony $bitrixtelephony)
    {
        // if ($user->can('BT::bitrixtelephony.delete')) {
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
