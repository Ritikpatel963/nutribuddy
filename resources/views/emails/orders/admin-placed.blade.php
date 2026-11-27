@extends('emails.layout')

@section('title', 'Admin Alert: New Order #' . $order->order_number . ' Received')
@section('preheader', 'New order received from ' . $order->customer_name . ' for ₹' . number_format($order->grand_total, 2))

@section('hero')
    <p style="margin: 0 0 10px; color: #7c3aed; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        ADMIN ORDER NOTIFICATION 📦
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        New Order Received!
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Order <strong>#{{ $order->order_number }}</strong> has been placed.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px;">
        A new customer order was created on the storefront.
    </p>

    <!-- CUSTOMER & ORDER INFO CARD -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 20px; background-color: #faf5ff; border: 1px solid #f1e8f5; border-radius: 12px; padding: 16px;">
        <tr>
            <td style="padding: 6px 12px; color: #6b6b80; font-size: 13px;" width="35%">Customer Name:</td>
            <td style="padding: 6px 12px; color: #0d0020; font-weight: 700; font-size: 14px;">{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; color: #6b6b80; font-size: 13px;">Customer Phone:</td>
            <td style="padding: 6px 12px; color: #0d0020; font-weight: 600; font-size: 14px;">{{ $order->customer_phone ?: $order->shipping_phone }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; color: #6b6b80; font-size: 13px;">Customer Email:</td>
            <td style="padding: 6px 12px; color: #0d0020; font-size: 14px;">{{ $order->customer_email }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; color: #6b6b80; font-size: 13px;">Payment Method:</td>
            <td style="padding: 6px 12px; color: #0d0020; font-weight: 700; font-size: 14px;">{{ strtoupper($order->payment_method) }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; color: #6b6b80; font-size: 13px;">Payment Status:</td>
            <td style="padding: 6px 12px; font-weight: 700; font-size: 14px; color: {{ $order->payment_status === 'paid' ? '#10b981' : '#f59e0b' }};">
                {{ strtoupper($order->payment_status) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; color: #6b6b80; font-size: 13px;">Shipping Destination:</td>
            <td style="padding: 6px 12px; color: #0d0020; font-size: 13px;">
                {{ $order->shipping_city }}, {{ $order->shipping_state }} ({{ $order->shipping_postal_code }})
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <div style="background-color: #ffffff; border: 1px solid #f1e8f5; border-radius: 12px; overflow: hidden; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #fcf9fe; border-bottom: 1px solid #f1e8f5;">
                    <th align="left" style="padding: 10px 16px; color: #6b6b80; font-size: 12px; text-transform: uppercase;">Item</th>
                    <th align="center" style="padding: 10px 12px; color: #6b6b80; font-size: 12px; text-transform: uppercase;">Qty</th>
                    <th align="right" style="padding: 10px 16px; color: #6b6b80; font-size: 12px; text-transform: uppercase;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php
                        $vName = $item->item_snapshot['variant_name'] ?? ($item->productVariant?->name ?? null);
                    @endphp
                    <tr>
                        <td style="padding: 12px 16px; border-bottom: 1px solid #f8f2fa;">
                            <strong>{{ $item->product_name }}</strong>
                            @if($vName)<div style="color: #845ec2; font-size: 12px;">{{ $vName }}</div>@endif
                        </td>
                        <td align="center" style="padding: 12px; border-bottom: 1px solid #f8f2fa; color: #555166;">
                            {{ $item->quantity }}
                        </td>
                        <td align="right" style="padding: 12px 16px; border-bottom: 1px solid #f8f2fa; font-weight: 700;">
                            ₹{{ number_format($item->line_total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #fffbf5;">
                    <td colspan="2" style="padding: 12px 16px; font-weight: 800; font-size: 15px; color: #0d0020;">Grand Total</td>
                    <td align="right" style="padding: 12px 16px; font-weight: 900; font-size: 17px; color: #ff4d8f;">₹{{ number_format($order->grand_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- CTA -->
    <div style="text-align: center; margin: 26px 0 10px;">
        <a href="{{ route('admin.ecommerce.orders.show', $order) }}" class="cta-btn" style="background-color: #7c3aed;" target="_blank">
            Open Order in Admin Panel &rarr;
        </a>
    </div>
@endsection
