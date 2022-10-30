<?php

namespace MichaelNabil230\MultiTenancy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use MichaelNabil230\MultiTenancy\Models\Plan;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\Services\Period;

class SubscriptionBuilder
{
    /**
     * The owner being subscribed to.
     *
     * @var \MichaelNabil230\MultiTenancy\Models\Tenant
     */
    protected $tenant;

    /**
     * The name of the plan being subscribed to.
     *
     * @var \MichaelNabil230\MultiTenancy\Models\Plan
     */
    protected $plan;

    /**
     * The date and time the start will be start.
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $startDate;

    /**
     * The date and time the trial will expire.
     *
     * @var \MichaelNabil230\MultiTenancy\Services\Period
     */
    protected $trialEndsAt;

    /**
     * Indicates that the trial should end immediately.
     *
     * @var bool
     */
    protected $skipTrial = false;

    /**
     * Indicates that the forever time.
     *
     * @var bool
     */
    protected $forever = false;

    /**
     * Create a new subscription builder instance.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Tenant  $tenant
     * @param  \MichaelNabil230\MultiTenancy\Models\Plan  $plan
     * @param  \Illuminate\Support\Carbon|null  $startDate
     * @return void
     */
    public function __construct(Tenant $tenant, Plan $plan, Carbon $startDate = null)
    {
        $this->tenant = $tenant;
        $this->plan = $plan;
        $this->startDate = $startDate ?? now();

        $this->trialEndsAt();
    }

    /**
     * Create a new subscription instance.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Tenant  $tenant
     * @param  \MichaelNabil230\MultiTenancy\Models\Plan  $plan
     * @param  \Illuminate\Support\Carbon|null  $startDate
     * @return self
     */
    public static function make(Tenant $tenant, Plan $plan, Carbon $startDate = null): self
    {
        return new self($tenant, $plan, $startDate);
    }

    /**
     * Specify the number of days of the trial.
     *
     * @return $this
     */
    public function trialEndsAt(): self
    {
        $this->trialEndsAt = Period::make(
            $this->plan->trial_interval,
            $this->plan->trial_period,
            $this->startDate,
        );

        return $this;
    }

    /**
     * Force the trial to end immediately.
     *
     * @return $this
     */
    public function skipTrial(): self
    {
        $this->skipTrial = true;

        return $this;
    }

    /**
     * Forever time in the live :).
     *
     * @return $this
     */
    public function forever(): self
    {
        $this->forever = true;

        return $this;
    }

    /**
     * Create subscription to a new plan.
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes = []): Model
    {
        return $this->tenant
            ->subscriptions()
            ->create(array_replace($this->buildPayload(), $attributes));
    }

    /**
     * Build the payload for subscription creation.
     *
     * @return array
     */
    protected function buildPayload(): array
    {
        [$startsAt, $endsAt] = $this->getStartAndEndDates();

        return array_filter([
            'plan_id' => $this->plan->getKey(),
            'trial_ends_at' => ! $this->skipTrial ? $this->trialEndsAt->getEndDate() : null,
            'starts_at' => ! $this->forever ? $startsAt : null,
            'ends_at' => ! $this->forever ? $endsAt : null,
        ]);
    }

    /**
     * Get dates of the start and end of the subscription.
     *
     * @return array
     */
    public function getStartAndEndDates(): array
    {
        $period = Period::make(
            $this->plan->invoice_interval,
            $this->plan->invoice_period,
            $this->trialEndsAt->getEndDate(),
        );

        return [$period->getStartDate(), $period->getEndDate()];
    }
}
