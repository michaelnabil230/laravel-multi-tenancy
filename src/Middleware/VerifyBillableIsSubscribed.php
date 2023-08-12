<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class VerifyBillableIsSubscribed
{
    /**
     * Verify the incoming request's tenant has a subscription.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $planId): Response
    {
        if ($this->subscribed(tenant(), $planId)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response('Subscription Required.', 402);
        }

        $route = config('multi-tenancy.subscription.route');

        throw_if(is_null($route), new \Exception('Please insert your route for the subscription in config `multi-tenancy.php`'));

        return to_route($route);
    }

    /**
     * Determine if the given tenant is subscribed to the given plan.
     */
    protected function subscribed(Tenant $tenant, int $planId): bool
    {
        if (! $tenant) {
            return false;
        }

        return $tenant->onGenericTrial() || $tenant->subscribed($planId);
    }
}
