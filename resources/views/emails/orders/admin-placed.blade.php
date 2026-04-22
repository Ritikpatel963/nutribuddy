<p>New order received.</p>
<p>Order number: <strong>{{ $order->order_number }}</strong></p>
<p>Customer: {{ $order->customer_name }} ({{ $order->customer_phone }})</p>
<p>Total: INR {{ number_format($order->grand_total, 2) }}</p>
