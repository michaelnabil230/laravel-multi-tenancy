<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class InitializeTenancyByTenantUser extends IdentificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guards = $this->guards();

        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $tenant = Auth::guard($guard)->user()->tenant;

                $this->initializeTenancy($request, $next, $tenant);
            }
        }

        return $next($request);
    }

    private function guards(): array
    {
        $userModel = MultiTenancy::ownerModel();

        $provider = collect(config('auth.providers'))
            ->filter(fn ($provider) => $provider['model'] == $userModel)
            ->keys()
            ->first();

        return collect(config('auth.guards'))
            ->filter(fn ($guard) => $guard['provider'] == $provider)
            ->keys()
            ->toArray();
    }
}
