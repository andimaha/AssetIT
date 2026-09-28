<?php

namespace App\Policies;

use App\Models\ItRequest;
use App\Models\User;

class ItRequestPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ItRequest $request): bool
    {
        if ($user->hasRole('staff_it')) {
            return true;
        }

        if ($request->UserPemohonID === $user->id) {
            return true;
        }

        return $request->relatedUsers()
            ->where('users.id', $user->id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return !empty($user->NIK);
    }

    public function update(User $user, ItRequest $request): bool
    {
        return $user->hasRole('staff_it');
    }

    public function delete(User $user, ItRequest $request): bool
    {
        return $user->hasRole('staff_it');
    }
}
