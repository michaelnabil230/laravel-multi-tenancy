<?php

namespace MichaelNabil230\MultiTenancy\Features;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use MichaelNabil230\MultiTenancy\Features\Contracts\Feature;
use MichaelNabil230\MultiTenancy\Tenancy;

class TelescopeTags implements Feature
{
    public function bootstrap(Tenancy $tenancy): void
    {
        if (! class_exists(Telescope::class)) {
            return;
        }

        Telescope::tag(function (IncomingEntry $entry) use ($tenancy) {
            $tags = [];

            if (! request()->route()) {
                return $tags;
            }

            if (tenancy()->initialized) {
                $tags = [
                    'tenant:'.$tenancy->tenant->id,
                ];
            }

            return $tags;
        });
    }
}
