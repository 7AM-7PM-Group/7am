<?php

namespace App\Jobs;

use App\Models\CustomerPricelist;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\PricelistRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncCustomerPricelistsJob implements ShouldQueue
{
    use Queueable;



    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        CustomerPricelist::sync();
    }
}
