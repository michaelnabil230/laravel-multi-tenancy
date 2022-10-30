<?php

namespace MichaelNabil230\MultiTenancy\Events\Contracts;

use Illuminate\Queue\SerializesModels;
use MichaelNabil230\MultiTenancy\Models\Subscription;

abstract class SubscriptionEvent
{
    use SerializesModels;

    public function __construct(public Subscription $subscription)
    {
    }
}
