<?php

namespace App\Console\Commands;

use App\Models\DietSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireDietSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diet:expire-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire diet service subscriptions that have passed their expiration date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCount = DietSubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            Log::info("Expired {$expiredCount} diet subscriptions.");
            $this->info("Expired {$expiredCount} diet subscriptions.");
        } else {
            $this->info("No diet subscriptions to expire.");
        }
    }
}
