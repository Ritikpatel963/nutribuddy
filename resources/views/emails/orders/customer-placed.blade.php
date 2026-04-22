<p>Hello {{ $order->customer_name }},</p>
<p>Your order <strong>{{ $order->order_number }}</strong> has been placed successfully with Cash on Delivery.</p>
<p>Order total: <strong>INR {{ number_format($order->grand_total, 2) }}</strong></p>
<p>You can track this order in your user panel.</p>
