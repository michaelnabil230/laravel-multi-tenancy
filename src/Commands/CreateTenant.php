<?php

namespace MichaelNabil230\MultiTenancy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use MichaelNabil230\MultiTenancy\Models\Domain;
use MichaelNabil230\MultiTenancy\Models\Tenant;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'create:tenant')]
class CreateTenant extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'create:tenant {name} {plan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $plan = $this->argument('plan');

        $this->comment("Creating tenant $name ...");

        $appUrl = Str::replaceLast('/', '', config('app.url'));
        $premiumDomain = Str::replace(['https://', 'http://'], $name.'.', $appUrl);
        $dashboardDomain = 'dashboard-'.$premiumDomain;
        $apiDomain = 'api-'.$premiumDomain;

        $isExists = Domain::query()
            ->orWhere('domain', $premiumDomain)
            ->orWhere('domain', $dashboardDomain)
            ->orWhere('domain', $apiDomain)
            ->exists();

        if ($isExists) {
            $this->error("Domain $premiumDomain already exists");

            return;
        }

        $tenant = Tenant::create([
            'name' => $name,
            'plan' => $plan,
        ]);

        $email = $password = $tenant->id.'-admin'.'@admin.com';

        $tenant->owner()->create([
            'name' => "Owner tenant $name",
            'email' => $email,
            'password' => $password,
        ]);

        $tenant->domains()->createMany([
            ['domain' => $premiumDomain, 'is_store' => true, 'is_verified' => true],
            ['domain' => $apiDomain],
            ['domain' => $dashboardDomain],
        ]);

        $this->table(['Email', 'Password', 'Premium domain', 'Dashboard domain', 'Api domain'], [
            [
                $email,
                $password,
                $premiumDomain,
                $dashboardDomain,
                $apiDomain,
            ],
        ]);

        $this->newLine();

        $this->info("Created tenant $name successfully.");
    }
}
