<?php

namespace MichaelNabil230\MultiTenancy\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MichaelNabil230\MultiTenancy\Models\Domain;
use Spatie\Dns\Dns;

class CheckDomainVerification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Domain $domain)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nameServer = config('multi-tenancy.name_server');

        if (empty($nameServer)) {
            return;
        }

        $isVerified = Dns::query()
            ->useNameserver($nameServer)
            ->getRecords($this->domain->domain);

        $this->domain->update(['is_verified' => ! empty($isVerified)]);
    }
}
