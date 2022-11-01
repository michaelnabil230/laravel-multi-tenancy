<?php

namespace MichaelNabil230\MultiTenancy\Features;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use MichaelNabil230\MultiTenancy\Features\Contracts\Feature;
use MichaelNabil230\MultiTenancy\MultiTenancy;

class TelescopeTags implements Feature
{
    public function bootstrap(): void
    {
        if (! class_exists(Telescope::class)) {
            return;
        }

        Telescope::tag(function (IncomingEntry $entry) {
            if (! request()->route() || ! MultiTenancy::checkCurrent()) {
                return [];
            }

            return [
                'tenant:'.MultiTenancy::current()?->getKey(),
            ];
        });
    }
}
