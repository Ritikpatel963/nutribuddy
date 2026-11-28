<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('plan_id')
                ->constrained('product_subscription_plans')
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade');
            $table->foreignId('address_id')
                ->nullable()
                ->constrained('customer_addresses')
                ->nullOnDelete();
            $table->json('shipping_address')->nullable();
            $table->string('cashfree_subscription_id')->nullable()->index();
            $table->string('cf_sub_reference')->nullable()->index();
            $table->string('status')->default('PENDING')->index();
            $table->unsignedSmallInteger('cycles_completed')->default(0);
            $table->unsignedSmallInteger('max_cycles')->default(1);
            $table->decimal('month_1_price', 10, 2);
            $table->decimal('recurring_price', 10, 2);
            $table->dateTime('next_billing_at')->nullable();
            $table->dateTime('last_billed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_subscriptions');
    }
};
