<?php

namespace MichaelNabil230\MultiTenancy\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class SubscriptionNotEnable extends Exception implements ProvidesSolution
{
    public function __construct()
    {
        parent::__construct('Subscription is not enabled');
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Subscription is not enabled in the config file')
            ->setSolutionDescription('Are you sure the subscription is enabled in the file config?');
    }
}
