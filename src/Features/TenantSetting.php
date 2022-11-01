<?php

namespace MichaelNabil230\MultiTenancy\Features;

use Illuminate\Support\Facades\Event;
use MichaelNabil230\MultiTenancy\Events\RevertedToCentralContext;
use MichaelNabil230\MultiTenancy\Events\Tenancy\TenancyBootstrapped;
use MichaelNabil230\MultiTenancy\Features\Contracts\Feature;
use MichaelNabil230\Setting\Stores\DatabaseSettingStore;

class TenantSetting implements Feature
{
    public function bootstrap(): void
    {
        if (! class_exists(DatabaseSettingStore::class)) {
            return;
        }

        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            DatabaseSettingStore::$cacheKey = 'setting.cache.tenant.'.$event->tenant->id;
        });

        Event::listen(RevertedToCentralContext::class, function () {
            //
        });
    }
}
