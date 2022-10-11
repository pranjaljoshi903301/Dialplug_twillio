<?php

namespace Corals\Modules\Directory\Policies;

use Corals\Foundation\Policies\BasePolicy;
use Corals\Modules\Directory\Models\Listing;
use Corals\User\Models\User;

class ListingPolicy extends BasePolicy
{
    protected $administrationPermission = 'Administrations::admin.directory';

    /**
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        if (request()->is('directory/user*') && $user->can('Directory::listing.view')) {
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
        return request()->is('directory/user*') && $user->can('Directory::listing.create');

    }

    /**
     * @param User $user
     * @param Listing $listing
     * @return bool
     */
    public function update(User $user, Listing $listing)
    {
        if (request()->is('directory/user*')
            && $listing->user_id == user()->id
            && $user->can('Directory::listing.update')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Listing $listing
     * @return bool
     */
    public function destroy(User $user, Listing $listing)
    {
        if (request()->is('directory/user*')
            && $listing->user_id == user()->id
            && $user->can('Directory::listing.delete')) {
            return true;
        }

        return false;
    }

}
