<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyBillableIsSubscribed
{
    /**
     * Verify the incoming request's tenant has a subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  int  $planId
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, int $planId)
    {
        if ($this->subscribed(tenant(), $planId)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response('Subscription Required.', 402);
        }

        $route = config('multi-tenancy.subscription.route');

        throw_if(is_null($route), new \Exception('Please insert your route in config `multi-tenancy.php`'));

        return to_route($route);
    }

    /**
     * Determine if the given tenant is subscribed to the given plan.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Tenant  $tenant
     * @param  int  $planId
     * @return bool
     */
    protected function subscribed($tenant, $planId)
    {
        if (! $tenant) {
            return false;
        }

        return $tenant->onGenericTrial() || $tenant->subscribed($planId);
    }
}
