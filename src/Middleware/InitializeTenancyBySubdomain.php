<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Support\Str;
use MichaelNabil230\MultiTenancy\Exceptions\NotASubdomainException;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByDomain;

class InitializeTenancyBySubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            $this->makeSubdomain($request->getHost());
        } catch (NotASubdomainException $e) {
            MultiTenancy::onFail($e, $request);
        }

        TenantFinderByDomain::findOrFail($request);

        return $next($request);
    }

    /**
     * Check if the hostname is a subdomain or not
     *
     * @return void
     *
     * @throws \MichaelNabil230\MultiTenancy\Exceptions\NotASubdomainException
     */
    protected function makeSubdomain(string $hostname)
    {
        $parts = explode('.', $hostname);

        $isLocalhost = count($parts) === 1;
        $isIpAddress = count(array_filter($parts, 'is_numeric')) === count($parts);

        // If we're on localhost or an IP address, then we're not visiting a subdomain.
        $isACentralDomain = in_array($hostname, config('multi-tenancy.central_domains'), true);
        $notADomain = $isLocalhost || $isIpAddress;
        $thirdPartyDomain = ! Str::endsWith($hostname, config('multi-tenancy.central_domains'));

        if ($isACentralDomain || $notADomain || $thirdPartyDomain) {
            return new NotASubdomainException($hostname);
        }
    }
}
