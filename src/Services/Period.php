<?php

namespace MichaelNabil230\MultiTenancy\Services;

use Illuminate\Support\Carbon;
use MichaelNabil230\MultiTenancy\Enums\PeriodicityType;

class Period
{
    /**
     * Starting date of the period.
     *
     * @var string
     */
    protected $start;

    /**
     * Ending date of the period.
     *
     * @var string
     */
    protected $end;

    /**
     * Interval.
     *
     * @var \MichaelNabil230\MultiTenancy\Enums\PeriodicityType
     */
    protected $interval;

    /**
     * Interval count.
     *
     * @var int
     */
    protected $period = 1;

    /**
     * Create a new Period instance.
     *
     * @return void
     */
    public function __construct(PeriodicityType $interval = PeriodicityType::month, int $period = 1, Carbon|string $start = '')
    {
        $this->interval = $interval;
        $this->period = $period;

        if (empty($start)) {
            $this->start = Carbon::now();
        } elseif (! $start instanceof Carbon) {
            $this->start = new Carbon($start);
        } else {
            $this->start = $start;
        }

        $method = 'add'.ucfirst($this->interval->name).'s';
        $this->end = $this->start->{$method}($this->period);
    }

    /**
     * Create a new Period instance.
     */
    public static function make(PeriodicityType $interval = PeriodicityType::month, int $period = 1, Carbon|string $start = ''): self
    {
        return new self($interval, $period, $start);
    }

    /**
     * Get start date.
     */
    public function getStartDate(): string
    {
        return $this->start;
    }

    /**
     * Get end date.
     */
    public function getEndDate(): string
    {
        return $this->end;
    }

    /**
     * Get period interval.
     */
    public function getInterval(): string
    {
        return $this->interval->name;
    }

    /**
     * Get period interval count.
     */
    public function getIntervalCount(): int
    {
        return $this->period;
    }
}
