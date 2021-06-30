<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the user "creating" event.
     *
     * @param  \App\User  $user
     *
     * @return void
     */
    public function creating(User $user)
    {
        // only fire for authenticated local users
        // so we don't kill the seeder (authenticated)
        // and the OIDC registration process
        $manager = Auth::user();

        if ($manager instanceof \App\User) {
            $user->addedBy()->associate($manager);
        }

        $this->setActivationToken($user);
    }

    /**
     * Handle the user "updating" event.
     *
     * @param  \App\User  $user
     *
     * @return void
     */
    public function updating(User $user)
    {
        $this->setActivationToken($user);
        $this->handleActivation($user);
    }

    /**
     * Handle the user "deleting" event.
     *
     * @param  \App\User  $user
     *
     * @return bool
     */
    public function deleting(User $user): bool
    {
        $user->email = $this->deleteUniqueField($user->email, 'email');
        $user->sub = $this->deleteUniqueField($user->sub, 'sub');

        return $user->save();
    }

    /**
     * Prefix the field with "del##########" (# replaced with sequence number)
     * to prevent collisions due to the database' unique constraint
     *
     * @param  string|null  $value
     * @param  string  $field
     *
     * @return string|null;
     */
    private function deleteUniqueField(?string $value, string $field): ?string
    {
        if (! $value) {
            return null;
        }

        $value = $this->restoreUniqueField($value);

        $greatestTrashed = User::onlyTrashed()
                               ->select($field)
                               ->where($field, 'LIKE', "del% {$value}")
                               ->orderByDesc($field)
                               ->first();

        if ($greatestTrashed) {
            $counter = (int) substr($greatestTrashed->$field, 3, 13);
        } else {
            $counter = 0;
        }

        return sprintf('del%010d %s', ++$counter, $value);
    }

    /**
     * Remove the fields del########## prefix
     *
     * @param  string|null  $value
     *
     * @return string|null
     */
    private function restoreUniqueField(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return preg_replace('/^del\d{10} /', '', $value);
    }

    /**
     * Handle the user "restoring" event.
     *
     * @param  \App\User  $user
     *
     * @return void
     */
    public function restoring(User $user): void
    {
        $user->email = $this->restoreUniqueField($user->email);
        $user->sub = $this->restoreUniqueField($user->sub);
    }

    /**
     * Add a random activation token to the user if needed
     *
     * @param  User  $user
     */
    private function setActivationToken(User $user)
    {
        if (!$user->enabled && !$user->activation_token) {
            $user->activation_token = Str::random(64);
        }
    }

    /**
     * Remove the activatable_by and set the added_by user association if the
     * user was just activated.
     *
     * @param  User  $user
     */
    private function handleActivation(User $user)
    {
        if ($this->userWasJustActivated($user)) {
            $user->activatable_by = null;
            $user->added_by = Auth::user()->id;
        }
    }

    /**
     * Determine if the given user is about to be activated by checking if the
     * user was activatable by the currently logged in user, if the user is
     * enabled and if the currently logged in user will also have future
     * permission to manage the user.
     *
     * @param  User  $user
     * @return bool
     */
    private function userWasJustActivated(User $user)
    {
        $manager = Auth::user();

        if (!$manager instanceof \App\User) {
            // tests and OIDC registration
            return false;
        }

        return $user->enabled
               && $user->activatable_by === $manager->id
               && $manager->canManageGroup($user->managed_by);
    }
}
