<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;

class InitializeTenancyByDomain extends IdentificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $this->initializeTenancy(
            $request,
            $next,
            $request->getHost()
        );
    }
}
