<?php

namespace MichaelNabil230\MultiTenancy;

use Illuminate\Support\Collection;
use MichaelNabil230\MultiTenancy\Exceptions\SubscriptionNotEnable;

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
     * If the subscription is enabled in the config file.
     *
     * @return bool
     */
    public static function subscriptionEnable(): bool
    {
        return config('multi-tenancy.subscription.enable', false);
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
