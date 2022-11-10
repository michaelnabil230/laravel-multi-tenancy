<?php

namespace MichaelNabil230\MultiTenancy;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use MichaelNabil230\MultiTenancy\Bootstrappers\Contracts\TenancyBootstrapper;
use MichaelNabil230\MultiTenancy\Exceptions\SubscriptionNotEnable;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class MultiTenancy
{
    use ManagesModelOptions\Tenant,
        ManagesModelOptions\Domain,
        ManagesModelOptions\Owner,
        ManagesModelOptions\Plan,
        ManagesModelOptions\Feature,
        ManagesModelOptions\Section,
        ManagesModelOptions\Subscription;

    /**
     * Set this property if you want to customize the on-fail behavior.
     *
     * @var callable|null
     */
    public static $onFail = null;

    /**
     * The name of the container of the tenant.
     *
     * @var string
     */
    public static $containerKey = 'currentTenant';

    /**
     * If the subscription is enabled in the config file.
     *
     * @return bool
     */
    public static function subscriptionEnable(): bool
    {
        return config('multi-tenancy.subscription.enable', false);
    }

    /**
     * Get all the bootstrappers.
     *
     * @return TenancyBootstrapper[]
     */
    public static function getBootstrappers(): array
    {
        // If no callback for getting bootstrappers is set, we just return all of them.
        $resolve = config('multi-tenancy.bootstrappers', []);

        // Here We instantiate the bootstrappers and return them.
        return array_map('app', $resolve);
    }

    /**
     * Catch any error in fail in middlewares
     *
     * @param  \Exception  $e
     * @param  \Illuminate\Http\Request  $request
     * @return callable
     */
    public static function onFail(Exception $e, Request $request)
    {
        $onFail = static::$onFail ?? function ($e, $request) {
            throw $e;
        };

        return $onFail($e, $request);
    }

    /**
     * Add the tenant in app container.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Tenant  $tenant
     * @return \MichaelNabil230\MultiTenancy\Models\Tenant
     */
    public static function bindAsCurrentTenant(Tenant $tenant): Tenant
    {
        app()->forgetInstance(self::$containerKey);

        app()->instance(self::$containerKey, $tenant);

        return $tenant;
    }

    /**
     * Get the current tenant form app container.
     *
     * @return \MichaelNabil230\MultiTenancy\Models\Tenant|null
     */
    public static function current(): ?Tenant
    {
        if (! app()->has(self::$containerKey)) {
            return null;
        }

        return app(self::$containerKey);
    }

    /**
     * Check if has current tenant form app container.
     *
     * @return bool
     */
    public static function checkCurrent(): bool
    {
        return static::current() !== null;
    }

    /**
     * Forget the current tenant form app container.
     *
     * @return  \MichaelNabil230\MultiTenancy\Models\Tenant|null
     */
    public static function forgetCurrent(): ?Tenant
    {
        $currentTenant = static::current();

        if (is_null($currentTenant)) {
            return null;
        }

        $currentTenant->forget();

        return $currentTenant;
    }

    /**
     * All available plans.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function plans(): Collection
    {
        throw_if(self::subscriptionEnable(), new SubscriptionNotEnable());

        $sections = self::section()->with('features')->get();

        $plans = self::plan()->query()
            ->with(['features' => fn ($query) => $query->pluck('feature_id')])
            ->get();

        return collect([
            'sections' => $sections,
            'plans' => $plans,
        ]);
    }
}
