<?php


namespace App\Services;


use App\Exceptions\UserFederationException;
use App\Exceptions\UserSubjectMissmatchException;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Vizir\KeycloakWebGuard\Facades\KeycloakWeb;

class UserFederationService
{
    /**
     * @throws UserSubjectMissmatchException
     * @throws AuthenticationException
     * @throws UserFederationException
     */
    public function loadLocalUser(): void
    {
        if ($this->isLocalUserLoaded()) {
            return;
        }

        $authenticated = Auth::user();

        if (!$authenticated) {
            throw new AuthenticationException();
        }

        $user = $this->retrieveLocalUser($authenticated);

        if (!$user) {
            throw new UserFederationException('Local user not found.');
        }

        $user->complementSub($authenticated->sub);

        $this->assertSub($user, $authenticated->sub);

        Auth::shouldUse('web-local');
        Auth::setUser($user);
        Auth::login($user, true);
        KeycloakWeb::forgetToken();
    }

    public function isLocalUserLoaded(): bool
    {
        return Auth::user() instanceof User;
    }

    private function retrieveLocalUser($authenticated): ?User
    {
        $user = User::whereSub($authenticated->sub)->first();

        if (!$user) {
            $user = User::whereEmail($authenticated->id)->first();
        }

        return $user;
    }

    /**
     * Asserts that the jwt tokens sub field match the local sub field.
     *
     * @param  User  $user
     * @param  string  $sub
     *
     * @throws UserSubjectMissmatchException
     */
    private function assertSub(User $user, string $sub): void
    {
        if ($user->sub !== $sub) {
            $message = <<<EOL
User identified by email $user->email but the local subject doesn't match the
    jwt token's sub.
    - Local sub: $user->sub
    - OIDC sub: $sub
    This may happen if a user's SSO account was deleted and recreated with the
    same email address, or if the user changed his email address (in the SSO)
    and tries to take over an existing local account. This may be legit, but
    can also be hostile.
EOL;
            throw new UserSubjectMissmatchException($message);
        }
    }
}
