<?php

namespace MichaelNabil230\MultiTenancy\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use MichaelNabil230\MultiTenancy\Exceptions\SubscriptionNotEnable;
use MichaelNabil230\MultiTenancy\Models\Plan;
use MichaelNabil230\MultiTenancy\Models\Subscription;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use MichaelNabil230\MultiTenancy\SubscriptionBuilder;

trait HasSubscriptions
{
    /**
     * Get all of the subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this
            ->hasMany(MultiTenancy::subscriptionModel(), $this->getForeignKey())
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the last of the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastSubscription(): HasOne
    {
        return $this
            ->hasOne(MultiTenancy::subscriptionModel(), $this->getForeignKey())
            ->latestOfMany();
    }

    /**
     * Get a subscription by plan id.
     *
     * @param  int  $planId
     * @return \MichaelNabil230\MultiTenancy\Models\Subscription|null
     */
    public function subscription(int $planId): ?Subscription
    {
        return $this->subscriptions()->where('plan_id', $planId)->first();
    }

    /**
     * Create subscription to a new plan.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Plan  $plan
     * @param  \Illuminate\Support\Carbon|null  $startDate
     * @return \MichaelNabil230\MultiTenancy\SubscriptionBuilder
     */
    public function createSubscription(Plan $plan, Carbon $startDate = null): SubscriptionBuilder
    {
        throw_if(! MultiTenancy::subscriptionEnable(), new SubscriptionNotEnable());

        return SubscriptionBuilder::make($this, $plan, $startDate);
    }

    /**
     * Determine if the model has a given subscription.
     *
     * @param  int  $planId
     * @return bool
     */
    public function subscribed(int $planId): bool
    {
        $subscription = $this->subscription($planId);

        if (! $subscription || ! $subscription->valid()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the model is on trial.
     *
     * @param  int  $planId
     * @return bool
     */
    public function onTrial(int $planId): bool
    {
        if ($this->onGenericTrial()) {
            return true;
        }

        $subscription = $this->subscription($planId);

        if (! $subscription || ! $subscription->onTrial()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the model's trial has ended.
     *
     * @param  int  $planId
     * @return bool
     */
    public function hasExpiredTrial(int $planId): bool
    {
        if ($this->hasExpiredGenericTrial()) {
            return true;
        }

        $subscription = $this->subscription($planId);

        if (! $subscription || ! $subscription->hasExpiredTrial()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the model is on a "generic" trial at the model level.
     *
     * @return bool
     */
    public function onGenericTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Determine if the model's "generic" trial at the model level has expired.
     *
     * @return bool
     */
    public function hasExpiredGenericTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }
}
