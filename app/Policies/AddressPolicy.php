<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy {
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    public function update( User $user, Address $address ) {
        return $address->user_id === $user->id;
    }

    public function destroy( User $user, Address $address ) {
        return $address->user_id === $user->id;
    }

    public function own( User $user, Address $address ) {
        return $address->id === $user->id;
    }


}
