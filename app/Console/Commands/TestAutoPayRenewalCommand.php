<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DietSubscription;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\NutritionLead;

class TestAutoPayRenewalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:test-autopay {subscription_id? : Cashfree Subscription ID (e.g. sub_usr_48_plus_1795249101)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate the next month AutoPay recurring charge for a Cashfree subscription';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $subId = $this->argument('subscription_id');

        if (! $subId) {
            $latest = DietSubscription::whereNotNull('razorpay_order_id')
                ->where('status', 'active')
                ->latest()
                ->first();

            $subId = $latest?->razorpay_order_id;
        }

        if (! $subId) {
            $this->error('No active subscription found. Please provide a subscription ID.');
            return 1;
        }

        $this->info("--------------------------------------------------");
        $this->info(" Testing AutoPay Renewal for: {$subId}");
        $this->info("--------------------------------------------------");

        $dietSub = DietSubscription::where('razorpay_order_id', $subId)->first();
        $apiSub = Subscription::where('cashfree_subscription_id', $subId)->first();

        if (! $dietSub && ! $apiSub) {
            $this->error("Subscription '{$subId}' not found in database.");
            return 1;
        }

        $childName = $dietSub?->child?->name ?? 'Child #' . ($dietSub?->child_id ?? 'N/A');
        $planName = $dietSub?->subscriptionPlan?->name ?? 'Plus';
        $oldExpiry = $dietSub?->expires_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s');

        $this->line("<comment>Child Name:</comment> {$childName}");
        $this->line("<comment>Plan Tier:</comment> {$planName}");
        $this->line("<comment>Current Expiry Date:</comment> {$oldExpiry}");
        $this->newLine();

        $this->info("⏳ Simulating AutoPay recurring charge from bank...");

        // 1. Calculate new expiry (+30 days)
        $currentEnd = $dietSub?->expires_at && $dietSub->expires_at->isFuture()
            ? $dietSub->expires_at
            : now();
        $newExpiry = $currentEnd->copy()->addDays(30);

        // 2. Extend DietSubscription
        if ($dietSub) {
            $dietSub->update([
                'payment_status' => 'paid',
                'status'         => 'active',
                'expires_at'     => $newExpiry,
            ]);
        }

        // 3. Extend API Subscription table
        if ($apiSub) {
            $apiSub->update([
                'status'               => 'ACTIVE',
                'current_period_start' => now(),
                'current_period_end'   => $newExpiry,
            ]);
        }

        // 4. Record the Payment transaction
        $payment = Payment::create([
            'subscription_id'  => $apiSub?->id,
            'order_id'         => null,
            'provider'         => 'cashfree',
            'transaction_type' => 'capture',
            'payment_type'     => 'CHARGE',
            'status'           => 'SUCCESS',
            'currency'         => 'INR',
            'amount'           => $dietSub?->amount_paid ?? 999.00,
            'gateway_order_id' => $subId,
            'invoice_number'   => 'INV-AUTOPAY-' . strtoupper(uniqid()),
            'paid_at'          => now(),
            'notes'            => 'Automated test recurring charge for AutoPay verification',
        ]);

        // 5. Create or Reset Diet Lead for Nutritionist in Admin Panel
        $lead = NutritionLead::firstOrNew(['diet_subscription_id' => $dietSub?->id]);
        $lead->user_id = $dietSub?->user_id;
        $lead->child_id = $dietSub?->child_id;
        $lead->status = 'new';
        $lead->completed_at = null;
        $lead->created_at = now();
        $lead->updated_at = now();
        $lead->save();

        $this->info("✅ [1/3] AutoPay Payment Recorded: ₹" . number_format($payment->amount, 2) . " (Status: SUCCESS)");
        $this->info("✅ [2/3] Subscription Extended: New Expiry is {$newExpiry->format('Y-m-d H:i:s')}");
        $this->info("✅ [3/3] Month 2 Diet Lead Ready: Lead #{$lead->id} (Status: {$lead->status})");
        $this->newLine();

        $this->alert("SUCCESS: AutoPay renewal simulated successfully!");
        $this->line("👉 Check your Admin Panel now: <info>/admin/nutrition/leads</info>");
        $this->line("   You will see Lead #{$lead->id} waiting for nutritionist review and approval!");

        return 0;
    }
}
