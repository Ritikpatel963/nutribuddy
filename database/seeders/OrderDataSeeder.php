<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure we have products
        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please run ProductDataSeeder first.');
            return;
        }

        // 2. Create some sample customers
        $customers = User::factory()->count(10)->create(['role' => 'customer']);

        // 3. Generate orders for these customers
        foreach ($customers as $customer) {
            // Each customer gets 1-3 orders
            $orderCount = rand(1, 3);
            
            Order::factory()
                ->count($orderCount)
                ->create([
                    'user_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->phone,
                ])
                ->each(function ($order) use ($products) {
                    // Add 1-4 random products to each order
                    $itemsCount = rand(1, 4);
                    $selectedProducts = $products->random($itemsCount);
                    
                    $subtotal = 0;
                    $taxTotal = 0;

                    foreach ($selectedProducts as $product) {
                        $qty = rand(1, 3);
                        $unitPrice = $product->base_price;
                        $lineTotal = $unitPrice * $qty;
                        
                        // Calculate tax (18% as defined in our TaxRate seeder logic)
                        $taxPercent = 18.00;
                        $taxAmount = ($lineTotal * $taxPercent) / 100;
                        
                        OrderItem::factory()->create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'sku' => $product->sku,
                            'quantity' => $qty,
                            'unit_price' => $unitPrice,
                            'tax_percent' => $taxPercent,
                            'tax_amount' => $taxAmount,
                            'line_total' => $lineTotal + $taxAmount,
                        ]);

                        $subtotal += $lineTotal;
                        $taxTotal += $taxAmount;
                    }

                    // Update order totals
                    $order->update([
                        'subtotal' => $subtotal,
                        'tax_total' => $taxTotal,
                        'grand_total' => $subtotal + $taxTotal + $order->shipping_total - $order->discount_total,
                    ]);
                });
        }
    }
}
