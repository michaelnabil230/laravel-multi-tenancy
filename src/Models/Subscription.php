<?php

namespace MichaelNabil230\MultiTenancy\Models;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use LogicException;
use MichaelNabil230\MultiTenancy\Events\Subscription as EventsSubscription;
use MichaelNabil230\MultiTenancy\Services\Period;
use MichaelNabil230\MultiTenancy\Traits\BelongsToPlan;
use MichaelNabil230\MultiTenancy\Traits\BelongsToTenant;

class Subscription extends Model
{
    use HasFactory, SoftDeletes, BelongsToPlan, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'created' => EventsSubscription\SubscriptionCreated::class,
        'updated' => EventsSubscription\SubscriptionUpdated::class,
    ];

    /**
     * Determine if the subscription is active, on trial, or within its grace period.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->active() || $this->onTrial() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is recurring and not on trial.
     *
     * @return bool
     */
    public function recurring(): bool
    {
        return ! $this->onTrial() && ! $this->canceled();
    }

    /**
     * Filter query by recurring.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeRecurring(Builder $query): void
    {
        $query->notOnTrial()->notCanceled();
    }

    /**
     * Determine if the subscription is no longer active.
     *
     * @return bool
     */
    public function canceled(): bool
    {
        return ! is_null($this->ends_at);
    }

    /**
     * Filter query by canceled.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeCanceled(Builder $query): void
    {
        $query->whereNotNull('ends_at');
    }

    /**
     * Filter query by not canceled.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotCanceled(Builder $query): void
    {
        $query->whereNull('ends_at');
    }

    /**
     * Determine if the subscription has ended and the grace period has expired.
     *
     * @return bool
     */
    public function ended(): bool
    {
        return $this->canceled() && ! $this->onGracePeriod();
    }

    /**
     * Filter query by ended.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeEnded(Builder $query): void
    {
        $query->canceled()->notOnGracePeriod();
    }

    /**
     * Determine if the subscription is within its trial period.
     *
     * @return bool
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Filter query by on trial.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOnTrial(Builder $query): void
    {
        $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', Carbon::now());
    }

    /**
     * Determine if the subscription's trial has expired.
     *
     * @return bool
     */
    public function hasExpiredTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Filter query by expired trial.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeExpiredTrial(Builder $query): void
    {
        $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '<', Carbon::now());
    }

    /**
     * Filter query by not on trial.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotOnTrial(Builder $query): void
    {
        $query->whereNull('trial_ends_at')->orWhere('trial_ends_at', '<=', Carbon::now());
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     *
     * @return bool
     */
    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    /**
     * Filter query by on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeOnGracePeriod(Builder $query): void
    {
        $query->whereNotNull('ends_at')->where('ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on grace period.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeNotOnGracePeriod(Builder $query): void
    {
        $query->whereNull('ends_at')->orWhere('ends_at', '<=', Carbon::now());
    }

    /**
     * Force the trial to end immediately.
     *
     * This method must be combined with swap, resume, etc.
     *
     * @return $this
     */
    public function skipTrial(): self
    {
        $this->trial_ends_at = null;

        return $this;
    }

    /**
     * Force the subscription's trial to end immediately.
     *
     * @return $this
     */
    public function endTrial(): self
    {
        if (! is_null($this->trial_ends_at)) {
            $this->fill([
                'trial_ends_at' => null,
            ])->save();
        }

        return $this;
    }

    /**
     * Extend an existing subscription's trial period.
     *
     * @param  \Carbon\CarbonInterface  $date
     * @return $this
     */
    public function extendTrial(CarbonInterface $date): self
    {
        if (! $date->isFuture()) {
            throw new InvalidArgumentException("Extending a subscription's trial requires a date in the future.");
        }

        $this->fill([
            'trial_ends_at' => $date->getTimestamp(),
        ])->save();

        return $this;
    }

    /**
     * Cancel the subscription at a specific moment in time.
     *
     * @param  \DateTimeInterface|int  $endsAt
     * @return $this
     */
    public function cancelAt(DateTimeInterface|int $endsAt): self
    {
        if ($endsAt instanceof DateTimeInterface) {
            $endsAt = $endsAt->getTimestamp();
        }

        $this->fill([
            'ends_at' => Carbon::createFromTimestamp($endsAt),
        ])->save();

        event(new EventsSubscription\SubscriptionCancelled($this));

        return $this;
    }

    /**
     * Cancel the subscription immediately.
     *
     * @return bool
     */
    public function cancelNow(): bool
    {
        return $this->fill([
            'ends_at' => Carbon::now(),
        ])->save();

        event(new EventsSubscription\SubscriptionCancelled($this));
    }

    /**
     * Resume the canceled subscription.
     *
     * @return $this
     *
     * @throws \LogicException
     */
    public function resume(): self
    {
        if (! $this->onGracePeriod()) {
            throw new LogicException('Unable to resume subscription that is not within grace period.');
        }

        // Finally, we will remove the ending timestamp from the user's record in the
        // local database to indicate that the subscription is active again and is
        // no longer "canceled". Then we shall save this record in the database.
        $this->fill([
            'ends_at' => null,
            'trial_ends_at' => $this->onTrial() ? $this->trial_ends_at->getTimestamp() : now(),
        ])->save();

        event(new EventsSubscription\SubscriptionResume($this));

        return $this;
    }

    /**
     * Check if subscription is active.
     *
     * @return bool
     */
    public function active(): bool
    {
        return ! $this->ended();
    }

    /**
     * Check if subscription is inactive.
     *
     * @return bool
     */
    public function inactive(): bool
    {
        return ! $this->active();
    }

    /**
     * Change subscription plan.
     *
     * @param  \MichaelNabil230\MultiTenancy\Models\Plan  $plan
     * @return $this
     */
    public function changePlan(Plan $plan): self
    {
        $data = [
            'plan_id' => $plan->getKey(),
        ];

        // If plans does not have the same billing frequency
        // (e.g., invoice_interval and invoice_period) we will update
        // the billing dates starting today, and sics we are basically creating
        // a new billing cycle, the usage data will be cleared.

        if ($this->plan->invoice_interval !== $plan->invoice_interval || $this->plan->invoice_period !== $plan->invoice_period) {
            array_merge(
                $data,
                $this->setNewPeriod($plan->invoice_interval, $plan->invoice_period)
            );
        }

        $this->fill($data)->save();

        event(new EventsSubscription\SubscriptionChangePlan($this));

        return $this;
    }

    /**
     * Renew subscription period.
     *
     * @return $this
     *
     * @throws \LogicException
     */
    public function renew(): self
    {
        if ($this->ended() && $this->canceled()) {
            throw new LogicException('Unable to renew canceled ended subscription.');
        }

        $this->fill(array_merge($this->setNewPeriod(), [
            'canceled_at' => null,
        ]))->save();

        event(new EventsSubscription\SubscriptionRenewed($this));

        return $this;
    }

    /**
     * Set new subscription period.
     *
     * @param  string|null  $invoiceInterval
     * @param  int|null  $invoicePeriod
     * @return array
     */
    protected function setNewPeriod(int|null $invoiceInterval = null, int|null $invoicePeriod = null): array
    {
        if (is_null($invoiceInterval)) {
            $invoiceInterval = $this->plan->invoice_interval;
        }

        if (is_null($invoicePeriod)) {
            $invoicePeriod = $this->plan->invoice_period;
        }

        $period = Period::make($invoiceInterval, $invoicePeriod);

        return [
            'starts_at' => $period->getStartDate(),
            'ends_at' => $period->getEndDate(),
        ];
    }
}
