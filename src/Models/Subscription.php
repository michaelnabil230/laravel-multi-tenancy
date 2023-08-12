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
use MichaelNabil230\MultiTenancy\Enums\PeriodicityType;
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
     */
    public function valid(): bool
    {
        return $this->active() || $this->onTrial() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is recurring and not on trial.
     */
    public function recurring(): bool
    {
        return ! $this->onTrial() && ! $this->canceled();
    }

    /**
     * Filter query by recurring.
     */
    public function scopeRecurring(Builder $query): void
    {
        $query->notOnTrial()->notCanceled();
    }

    /**
     * Determine if the subscription is no longer active.
     */
    public function canceled(): bool
    {
        return ! is_null($this->ends_at);
    }

    /**
     * Filter query by canceled.
     */
    public function scopeCanceled(Builder $query): void
    {
        $query->whereNotNull('ends_at');
    }

    /**
     * Filter query by not canceled.
     */
    public function scopeNotCanceled(Builder $query): void
    {
        $query->whereNull('ends_at');
    }

    /**
     * Determine if the subscription has ended and the grace period has expired.
     */
    public function ended(): bool
    {
        return $this->canceled() && ! $this->onGracePeriod();
    }

    /**
     * Filter query by ended.
     */
    public function scopeEnded(Builder $query): void
    {
        $query->canceled()->notOnGracePeriod();
    }

    /**
     * Determine if the subscription is within its trial period.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Filter query by on trial.
     */
    public function scopeOnTrial(Builder $query): void
    {
        $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', Carbon::now());
    }

    /**
     * Determine if the subscription's trial has expired.
     */
    public function hasExpiredTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Filter query by expired trial.
     */
    public function scopeExpiredTrial(Builder $query): void
    {
        $query->whereNotNull('trial_ends_at')->where('trial_ends_at', '<', Carbon::now());
    }

    /**
     * Filter query by not on trial.
     */
    public function scopeNotOnTrial(Builder $query): void
    {
        $query->whereNull('trial_ends_at')->orWhere('trial_ends_at', '<=', Carbon::now());
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     */
    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    /**
     * Filter query by on grace period.
     */
    public function scopeOnGracePeriod(Builder $query): void
    {
        $query->whereNotNull('ends_at')->where('ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on grace period.
     */
    public function scopeNotOnGracePeriod(Builder $query): void
    {
        $query->whereNull('ends_at')->orWhere('ends_at', '<=', Carbon::now());
    }

    /**
     * Force the trial to end immediately.
     *
     * This method must be combined with swap, resume, etc.
     */
    public function skipTrial(): self
    {
        $this->trial_ends_at = null;

        return $this;
    }

    /**
     * Force the subscription's trial to end immediately.
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
     */
    public function extendTrial(CarbonInterface $date): self
    {
        throw_unless($date->isFuture(), new InvalidArgumentException("Extending a subscription's trial requires a date in the future."));

        $this->fill([
            'trial_ends_at' => $date->getTimestamp(),
        ])->save();

        return $this;
    }

    /**
     * Cancel the subscription at a specific moment in time.
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
     * @throws \LogicException
     */
    public function resume(): self
    {
        throw_unless($this->onGracePeriod(), new LogicException('Unable to resume subscription that is not within grace period.'));

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
     */
    public function active(): bool
    {
        return ! $this->ended();
    }

    /**
     * Check if subscription is inactive.
     */
    public function inactive(): bool
    {
        return ! $this->active();
    }

    /**
     * Change subscription plan.
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
     * @throws \LogicException
     */
    public function renew(): self
    {
        throw_unless($this->onGracePeriod(), new LogicException('Unable to renew canceled ended subscription.'));

        $this->fill(array_merge($this->setNewPeriod(), [
            'canceled_at' => null,
        ]))->save();

        event(new EventsSubscription\SubscriptionRenewed($this));

        return $this;
    }

    /**
     * Set new subscription period.
     */
    protected function setNewPeriod(PeriodicityType $invoiceInterval = null, int $invoicePeriod = null): array
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
