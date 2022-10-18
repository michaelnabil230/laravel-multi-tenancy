<?php

namespace MichaelNabil230\MultiTenancy\Features;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use MichaelNabil230\MultiTenancy\Events\TenancyBootstrapped;
use MichaelNabil230\MultiTenancy\Features\Contracts\Feature;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use MichaelNabil230\MultiTenancy\Tenancy;

class TenantConfig implements Feature
{
    public static array $storageToConfigMap = [
        // 'paypal_api_key' => 'services.paypal.api_key',
    ];

    public function __construct(
        protected Repository $config
    ) {
    }

    public function bootstrap(Tenancy $tenancy): void
    {
        Event::listen(TenancyBootstrapped::class, function (TenancyBootstrapped $event) {
            $this->setTenantConfig($event->tenancy->tenant);
        });
    }

    public function setTenantConfig(Tenant $tenant): void
    {
        foreach (static::$storageToConfigMap as $storageKey => $configKey) {
            $override = Arr::get($tenant, $storageKey);

            if (! is_null($override)) {
                if (is_array($configKey)) {
                    foreach ($configKey as $key) {
                        $this->config[$key] = $override;
                    }
                } else {
                    $this->config[$configKey] = $override;
                }
            }
        }
    }
}
