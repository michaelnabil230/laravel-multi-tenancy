<?php

namespace MichaelNabil230\MultiTenancy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MichaelNabil230\MultiTenancy\Models\Plan;
use MichaelNabil230\MultiTenancy\Models\Subscription;
use MichaelNabil230\MultiTenancy\MultiTenancy;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'create:tenant', description: 'Create a new tenant')]
class CreateTenant extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'create:tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = Str::camel($this->argument('name'));

        $this->components->warn("Creating tenant $name ...");

        if (MultiTenancy::subscriptionEnable() && MultiTenancy::plan()::count() === 0) {
            $this->components->error('No Plans in the database.');

            return Command::FAILURE;
        }

        [$premiumDomain, $dashboardDomain, $apiDomain] = $this->getDomains($name);

        if ($this->ifDomainIsExists($premiumDomain, $dashboardDomain, $apiDomain)) {
            $this->components->error("Domain $premiumDomain already exists");

            return Command::FAILURE;
        }

        $email = $password = Str::random(8).'-admin'.'@admin.com';

        $owner = MultiTenancy::owner()::create([
            'name' => "Owner tenant $name",
            'email' => $email,
            'password' => $password,
        ]);

        /** @var \MichaelNabil230\MultiTenancy\Models\Tenant $tenant */
        $tenant = MultiTenancy::tenant()::create([
            'name' => $name,
            'owner_id' => $owner->getKey(),
        ]);

        $subscription = null;
        $plan = null;

        if (MultiTenancy::subscriptionEnable()) {
            $plan = $this->getPlanSelected();
            $subscription = $tenant->createSubscription($plan)->create();
        }

        $domains = $tenant->domains()->createMany([
            ['domain' => $premiumDomain, 'is_premium' => true, 'is_verified' => true],
            ['domain' => $apiDomain],
            ['domain' => $dashboardDomain],
        ]);

        $this->displayData($email, $password, $domains, $plan, $subscription);

        $this->newLine();

        $this->components->info("Created tenant $name successfully.");

        return Command::SUCCESS;
    }

    /**
     * Check if the domain exists before be to create.
     */
    public function ifDomainIsExists(string $premiumDomain, string $dashboardDomain, string $apiDomain): bool
    {
        $isExists = MultiTenancy::domain()::query()
            ->orWhere('domain', $premiumDomain)
            ->orWhere('domain', $dashboardDomain)
            ->orWhere('domain', $apiDomain)
            ->exists();

        return $isExists;
    }

    /**
     * The get plan is selected by the user.
     */
    public function getPlanSelected(): ?Plan
    {
        $slug = $this->components->choice(
            'Choose a plan from the list.',
            MultiTenancy::plan()::get()
                ->mapWithKeys(fn (Plan $plan) => [$plan->getKey() => $plan->slug])
                ->toArray(),
        );

        return MultiTenancy::plan()::whereTranslation('slug', $slug)->first();
    }

    /**
     * Display the data as json or in cli.
     */
    public function displayData(
        string $email,
        string $password,
        Collection $domains,
        ?Plan $plan,
        ?Subscription $subscription,
    ): void {
        $this->option('json')
            ? $this->asJson($email, $password, $domains, $plan, $subscription)
            : $this->forCli($email, $password, $domains, $plan, $subscription);
    }

    /**
     * Convert the given data to JSON.
     */
    protected function asJson(
        string $email,
        string $password,
        Collection $domains,
        ?Plan $plan,
        ?Subscription $subscription,
    ): void {
        $this->output->writeln(collect([
            'email' => $email,
            'password' => $password,
            'domains' => $domains->except(['tenant_id', 'updated_at', 'created_at']),
            'plan' => $plan->only(['slug', 'name', 'description', 'price']),
            'subscription' => $subscription->only(['trial_ends_at', 'starts_at', 'ends_at', 'canceled_at']),
        ])->toJson());
    }

    /**
     * Convert the given data to regular CLI output.
     */
    protected function forCli(
        string $email,
        string $password,
        Collection $domains,
        ?Plan $plan,
        ?Subscription $subscription,
    ): void {
        $this->components->twoColumnDetail('<fg=green;options=bold>Owner credentials</>');
        $this->components->twoColumnDetail('Email', $email);
        $this->components->twoColumnDetail('Password', $password);

        $this->newLine();

        $this->components->twoColumnDetail('<fg=green;options=bold>Domains</>');
        foreach ($domains as $domain) {
            $isVerified = $domain->is_verified ? 'Verified' : 'UnVerified';
            $isPremium = $domain->is_premium ? 'Premium' : 'Not premium';

            $this->components->twoColumnDetail(
                sprintf('%s - <fg=gray>%s</>', $domain->id, $domain->domain),
                sprintf('%s / <fg=gray>%s</>', $isVerified, $isPremium),
            );
        }

        $this->newLine();

        if (! is_null($plan)) {
            $this->components->twoColumnDetail('<fg=green;options=bold>Plan</>');
            $this->components->twoColumnDetail('Slug', $plan->slug);
            $this->components->twoColumnDetail('Name', $plan->name);
            $this->components->twoColumnDetail('Description', $plan->description);
            $this->components->twoColumnDetail('Price', $plan->price);
            $this->newLine();
        }

        if (! is_null($subscription)) {
            $this->components->twoColumnDetail('<fg=green;options=bold>Subscription</>');
            $this->components->twoColumnDetail('Trial ends at', $subscription->trial_ends_at);
            $this->components->twoColumnDetail('Starts at', $subscription->starts_at);
            $this->components->twoColumnDetail('Ends at', $subscription->ends_at);
            $this->components->twoColumnDetail('Canceled at', $subscription->canceled_at ?? 'null');
            $this->newLine();
        }
    }

    /**
     * Get all domains by name.
     *
     * @return array<int, string>
     */
    protected function getDomains(string $name): array
    {
        $appUrl = Str::replaceLast('/', '', config('app.url'));
        $premiumDomain = Str::replace(['https://', 'http://'], $name.'.', $appUrl);
        $dashboardDomain = 'dashboard-'.$premiumDomain;
        $apiDomain = 'api-'.$premiumDomain;

        return [$premiumDomain, $dashboardDomain, $apiDomain];
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the tenant.', null],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['json', null, InputOption::VALUE_NONE, 'Output the route list as JSON.'],
        ];
    }
}
