<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Traits\IsSubdomain;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyByDomainOrSubdomain
{
    use IsSubdomain;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isSubdomain($request->getHost())) {
            return app(InitializeTenancyBySubdomain::class)->handle($request, $next);
        }

        return app(InitializeTenancyByDomain::class)->handle($request, $next);
    }
}
