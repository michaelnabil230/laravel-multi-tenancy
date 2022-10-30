<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventAccessFromCentralDomains
{
    /**
     * Set this property if you want to customize the on-fail behavior.
     *
     * @var callable|null
     */
    public static $onFail = null;

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->getHost(), config('multi-tenancy.central_domains'))) {
            $onFail = static::$onFail ?? function ($request, $next) {
                abort(404);
            };

            return $onFail($request, $next);
        }

        return $next($request);
    }
}
