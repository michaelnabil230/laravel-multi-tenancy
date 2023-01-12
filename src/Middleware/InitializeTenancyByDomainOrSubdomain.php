<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Support\Str;

class InitializeTenancyByDomainOrSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if ($this->isSubdomain($request->getHost())) {
            return app(InitializeTenancyBySubdomain::class)->handle($request, $next);
        } else {
            return app(InitializeTenancyByDomain::class)->handle($request, $next);
        }
    }

    protected function isSubdomain(string $hostname): bool
    {
        return Str::endsWith($hostname, config('multi-tenancy.central_domains'));
    }
}
