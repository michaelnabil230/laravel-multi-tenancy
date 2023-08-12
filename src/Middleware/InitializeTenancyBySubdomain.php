<?php

namespace MichaelNabil230\MultiTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MichaelNabil230\MultiTenancy\Exceptions\NotASubdomainException;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\TenantFinder\TenantFinderByDomain;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyBySubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->makeSubdomain($request->getHost());
        } catch (NotASubdomainException $exception) {
            MultiTenancy::onFail($exception, $request);
        }

        TenantFinderByDomain::findOrFail($request);

        return $next($request);
    }

    /**
     * Check if the hostname is a subdomain or not
     *
     * @throws \MichaelNabil230\MultiTenancy\Exceptions\NotASubdomainException
     */
    protected function makeSubdomain(string $hostname): void
    {
        $parts = explode('.', $hostname);

        $isLocalhost = count($parts) === 1;
        $isIpAddress = count(array_filter($parts, 'is_numeric')) === count($parts);

        // If we're on localhost or an IP address, then we're not visiting a subdomain.
        $isACentralDomain = in_array($hostname, config('multi-tenancy.central_domains'), true);
        $notADomain = $isLocalhost || $isIpAddress;
        $thirdPartyDomain = ! Str::endsWith($hostname, config('multi-tenancy.central_domains'));

        if ($isACentralDomain || $notADomain || $thirdPartyDomain) {
            throw new NotASubdomainException($hostname);
        }
    }
}
