<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Exceptions\TenancyNotInitializedException;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use Symfony\Component\HttpFoundation\Response;

class ScopeSessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        throw_unless(
            MultiTenancy::checkCurrent(),
            new TenancyNotInitializedException('Tenancy needs to be initialized before the session scoping middleware is executed'),
        );

        $sessionKey = config('multi-tenancy.session_key', 'ensure_valid_tenant_session_tenant_id');

        if (! $request->session()->has($sessionKey)) {
            $request->session()->put($sessionKey, tenant()->getKey());
        } else {
            abort_if($request->session()->get($sessionKey) !== tenant()->getKey(), Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
