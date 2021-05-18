# About the Single-Sign-On Implementation
* Based on OIDC with the authorization code flow. All OIDC stuff is handled by 
[vizir/laravel-keycloak-web-guard](https://github.com/Vizir/laravel-keycloak-web-guard).
* SSO is only used for authentication, any further steps are based on a local
user table.

## How it works
1. The login button redirects the user agent to the SSO system (keycloak).
1. After authenticating the user, the SSO system redirects the user back to the
app, calling `/callback?code=thesecretcode`. 
1. The `ChooseGuard` middleware makes the request use the `web-sso` guard, which
uses the [vizir/laravel-keycloak-web-guard](https://github.com/Vizir/laravel-keycloak-web-guard).
1. The [vizir/laravel-keycloak-web-guard](https://github.com/Vizir/laravel-keycloak-web-guard) 
exchanges the authorization code for an ID token and generates a `KeycloakUser`
object from the ID token.
1. The `LoadLocalUser` middleware tries to find the corresponding user in 
the user table of Laravel's local database (by the OIDC `sub` field, or by email 
address as fallback). If a local user is found, the local user is loaded. Else,
the user agent is redirected to the registration route
(see [Registration](#registration)).
1. The `BlockNonEnabledUsers` checks, if the user was already approved 
(enabled). If so, he has successfully authenticated. Else he is redirected to
the pending approval route (see [Approval](#approval)).

### Registration
Registration may be done ahead of time by an admin or on the fly while signing
in. If a user is registered ahead of time, he doesn't have to wait for approval.

#### Ahead of time
An admin creates a local user account. When the user now connects, the 
`LoadLocalUser` middleware will find it's account and he/she can log in 
(provided, the local account was created with the same email address as the user
uses for keycloak). No further steps are needed.

#### On the fly
1. After beeing redirected to the registration route (see 
[How it works](#how-it-works)), a new local user is created, based on the data
from the OIDC token.
1. The system's admin (see the environment variable `APP_ADMIN_EMAIL`) is 
notified by email about the registration and asked to enable the newly created
user.
1. The user is redirected to the pending approval route.

### Approval
As long as the user is not approved, he can't sign in. To approve a user, the
admin may click the link he receives by email and save the user. The crucial
part is a) to check the user's `Enabled` field and b) to inform the user, by 
checking the `Welcome Email` field.

The idea behind the approval field is, that anyone can register, but not 
everyone should be granted access.

#### In Detail
1. Any admin with the activation link can edit the user (if an admin opens the
   activation link, the `GrantUserAccessForActivation` middleware enters the 
   admin to the user's `activatable_by` field).
1. As soon as the user is activated by an admin, and the admin sets the 
   `managed_by` property of the user to a group the admin can manage, the user's
   `activatable_by` field is cleared, so the admin does not retain access to the 
   user for ever. The admin may however always regain access if he uses the 
   activation link (see step 1).


### `sub` vs. `email` Field
The OIDC `sub` field stands for subject and is the intended user identifier.
In our case it contains the Keycloak user ID.
- The `sub` field takes precedence over the email address.
- The identification over the email address is needed in case of
  [ahead of time](#ahead-of-time) registration.
- If the user is identified by the email address, and the `sub` field of the
  local user is empty, the `sub` field is complemented with the value from the
  OIDC token.
- If the user is identified by the email address but the `sub` fields of the
  local user table and the OIDC token do not match, the user is redirected to
  the `user-account-error` page. Details are logged.

  This case occures if the originally registered user is deleted in Keycloak but
  not in the imagery and later a new account with the same email address is
  created in Keycloak.

## Quirks
We've made the following price-value tradeoffs, that must be known.

### Change of email address
A change of the email address in keycloak does not lead to a change of the email
address in the imagery. A superadmin must change the email address in the
imagery to be in accordance with keycloak. Regular admins and users can't change
any emailadresses, because they can't change it in keycloak either.

### Disable account
If the login in keycloak is disabled, the account in the imagery is not 
automatically disabled as well. You have to do it in both systems.

### Delete account
User accounts in the imagery are only soft deleted (there are so many 
references to the user all over the place (images, groups, other users etc.)).
If a deleted user tries to sign in, he will get a message telling him, that
there is a problem with his user account and that he should contact the 
imagery's admin.
