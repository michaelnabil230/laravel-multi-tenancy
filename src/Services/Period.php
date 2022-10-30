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
     * @param  \MichaelNabil230\MultiTenancy\Enums\PeriodicityType  $interval
     * @param  int  $period
     * @param  \Illuminate\Support\Carbon|string  $start
     * @return void
     */
    public function __construct(PeriodicityType $interval = PeriodicityType::month, $period = 1, $start = '')
    {
        $this->interval = $interval;

        if (empty($start)) {
            $this->start = Carbon::now();
        } elseif (! $start instanceof Carbon) {
            $this->start = new Carbon($start);
        } else {
            $this->start = $start;
        }

        $this->period = $period;
        $start = clone $this->start;
        $method = 'add'.ucfirst($this->interval->name).'s';
        $this->end = $start->{$method}($this->period);
    }

    /**
     * Create a new Period instance.
     *
     * @param  \MichaelNabil230\MultiTenancy\Enums\PeriodicityType  $interval
     * @param  int  $period
     * @param  \Illuminate\Support\Carbon|string  $start
     * @return self
     */
    public static function make(PeriodicityType $interval = PeriodicityType::month, $period = 1, $start = ''): self
    {
        return new self($interval, $period, $start);
    }

    /**
     * Get start date.
     *
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->start;
    }

    /**
     * Get end date.
     *
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->end;
    }

    /**
     * Get period interval.
     *
     * @return string
     */
    public function getInterval(): string
    {
        return $this->interval->name;
    }

    /**
     * Get period interval count.
     *
     * @return int
     */
    public function getIntervalCount(): int
    {
        return $this->period;
    }
}
