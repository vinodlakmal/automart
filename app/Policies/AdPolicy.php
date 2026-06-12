<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;

class AdPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Ad $ad): bool
    {
        if ($ad->status === 'active') {
            return true;
        }

        return $user !== null && $user->id === $ad->user_id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Ad $ad): bool
    {
        return $user->id === $ad->user_id;
    }

    public function delete(User $user, Ad $ad): bool
    {
        return $user->id === $ad->user_id;
    }
}
