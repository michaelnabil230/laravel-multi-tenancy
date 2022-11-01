<?php

namespace MichaelNabil230\MultiTenancy\Bootstrappers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use MichaelNabil230\MultiTenancy\Bootstrappers\Contracts\TenancyBootstrapper;
use MichaelNabil230\MultiTenancy\Models\Tenant;

class FilesystemTenancyBootstrapper implements TenancyBootstrapper
{
    public array $originalPaths = [];

    public function __construct(
        protected Application $app,
    ) {
        $this->originalPaths = [
            'disks' => [],
            'storage' => $this->app->storagePath(),
            'asset_url' => config('app.asset_url'),
        ];

        $this->app['url']->macro('setAssetRoot', function ($assetRoot) {
            return $this;
        });
    }

    public function bootstrap(Tenant $tenant)
    {
        $suffix = config('multi-tenancy.filesystem.suffix_base').$tenant->getKey();

        // storage_path()
        if (config('multi-tenancy.filesystem.suffix_storage_path') ?? true) {
            $this->app->useStoragePath($this->originalPaths['storage']."/{$suffix}");
        }

        // asset()
        if (config('multi-tenancy.filesystem.asset_helper_tenancy') ?? true) {
            if ($this->originalPaths['asset_url']) {
                config()->set('app.asset_url', $this->originalPaths['asset_url']."/$suffix");
                $this->app['url']->setAssetRoot(config('app.asset_url'));
            } else {
                // $this->app['url']->setAssetRoot($this->app['url']->route('multi-tenancy.asset', ['path' => '']));
            }
        }

        // Storage facade
        Storage::forgetDisk(config('multi-tenancy.filesystem.disks', []));

        foreach (config('multi-tenancy.filesystem.disks', []) as $disk) {
            $originalRoot = config("filesystems.disks.{$disk}.root");
            $this->originalPaths['disks'][$disk] = $originalRoot;

            $finalPrefix = str_replace(
                ['%storage_path%', '%tenant%'],
                [storage_path(), $tenant->getKey()],
                config("multi-tenancy.filesystem.root_override.{$disk}", ''),
            );

            if (! $finalPrefix) {
                $finalPrefix = $originalRoot
                    ? rtrim($originalRoot, '/').'/'.$suffix
                    : $suffix;
            }

            config()->set("filesystems.disks.{$disk}.root", $finalPrefix);
        }
    }

    public function revert()
    {
        // storage_path()
        $this->app->useStoragePath($this->originalPaths['storage']);

        // asset()
        config()->set('app.asset_url', $this->originalPaths['asset_url']);
        $this->app['url']->setAssetRoot(config('app.asset_url'));

        // Storage facade
        Storage::forgetDisk(config('multi-tenancy.filesystem.disks', []));
        foreach (config('multi-tenancy.filesystem.disks', []) as $disk) {
            config()->set("filesystems.disks.{$disk}.root", $this->originalPaths['disks'][$disk]);
        }
    }
}
