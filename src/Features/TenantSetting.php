<?php

namespace MichaelNabil230\MultiTenancy\Features;

use Illuminate\Support\Facades\Event;
use MichaelNabil230\MultiTenancy\Events\TenancyBootstrapped;
use MichaelNabil230\MultiTenancy\Features\Contracts\Feature;
use MichaelNabil230\MultiTenancy\Tenancy;
use MichaelNabil230\Setting\Stores\DatabaseSettingStore;

class TenantSetting implements Feature
{
    public function bootstrap(Tenancy $tenancy): void
    {
        if (! class_exists(DatabaseSettingStore::class)) {
            return;
        }

        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            DatabaseSettingStore::$cacheKey = 'setting.cache.tenant.'.$event->tenancy->tenant->id;
        });
    }
}
