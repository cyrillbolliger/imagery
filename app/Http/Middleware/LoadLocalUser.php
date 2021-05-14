<?php

namespace App\Http\Middleware;

use App\Exceptions\UserFederationException;
use App\Exceptions\UserSubjectMissmatchException;
use App\Services\UserFederationService;
use Closure;
use Illuminate\Auth\AuthenticationException;

class LoadLocalUser
{
    /**
     * @var UserFederationService
     */
    protected $federationService;

    /**
     * @param UserFederationService $federationService
     */
    public function __construct(UserFederationService $federationService)
    {
        $this->federationService = $federationService;
    }

    /**
     * Convert any sso authenticated user to its local user object (identified
     * by email). Redirect to the sso user registration if no local user object
     * exists.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param UserFederationService $federationService
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->federationService->loadLocalUser();
        } catch (UserFederationException $e) {
            return redirect()->route('register-sso-user');
        } catch (AuthenticationException $e) {
            return redirect()->route( 'login' );
        } catch (UserSubjectMissmatchException $e) {
            \Log::info($e->getMessage());
            return redirect()->route( 'user-account-error' );
        }

        return $next($request);
    }
}
