<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Auth;

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
    }

    /**
     * Handle the user "deleting" event.
     *
     * @param  \App\User  $user
     *
     * @return bool
     */
    public function deleting(User $user)
    {
        $user->email = $this->deleteEmail($user->email);

        return $user->save();
    }

    /**
     * Prefix email with "del##########" (# replaced with sequence number) to
     * prevent collisions due to the database' unique constraint
     *
     * @param  string  $email
     *
     * @return string
     */
    private function deleteEmail(string $email)
    {
        $email = $this->restoreEmail($email);

        $greatestTrashed = User::onlyTrashed()
                               ->select('email')
                               ->where('email', 'LIKE', "del% {$email}")
                               ->orderByDesc('email')
                               ->first();

        if ($greatestTrashed) {
            $counter = (int) substr($greatestTrashed->email, 3, 13);
        } else {
            $counter = 0;
        }

        return sprintf('del%010d %s', ++$counter, $email);
    }

    /**
     * Remove the emails del########## prefix
     *
     * @param  string  $email
     *
     * @return string|string[]|null
     */
    private function restoreEmail(string $email)
    {
        return preg_replace('/^del\d{10} /', '', $email);
    }

    /**
     * Handle the user "restoring" event.
     *
     * @param  \App\User  $user
     *
     * @return void
     */
    public function restoring(User $user)
    {
        $user->email = $this->restoreEmail($user->email);
    }
}
