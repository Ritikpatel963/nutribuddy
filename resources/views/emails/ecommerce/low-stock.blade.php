@extends('emails.layout')

@section('title', 'Admin Alert: Low Stock Warning for ' . $product->name)
@section('preheader', 'Low inventory warning: ' . $product->name . ' is at ' . $currentStock . ' units remaining.')

@section('hero')
    <p style="margin: 0 0 10px; color: #dc2626; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        INVENTORY ALERT ⚠️
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        Low Stock Warning
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Inventory is running low and needs restocking.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px;">
        Hello Admin,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.7;">
        The stock level for the following product has fallen to or below the configured low-stock threshold.
    </p>

    <!-- STOCK INFO CARD -->
    <div style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="font-size: 14px;">
            <tr>
                <td style="padding: 6px 0; color: #6b6b80;" width="40%">Product Name:</td>
                <td style="padding: 6px 0; color: #0d0020; font-weight: 700; font-size: 15px;">{{ $product->name }}</td>
            </tr>
            @if($variantName)
                <tr>
                    <td style="padding: 6px 0; color: #6b6b80;">Variant:</td>
                    <td style="padding: 6px 0; color: #7c3aed; font-weight: 600;">{{ $variantName }}</td>
                </tr>
            @endif
            @if($sku)
                <tr>
                    <td style="padding: 6px 0; color: #6b6b80;">SKU:</td>
                    <td style="padding: 6px 0; color: #0d0020; font-family: monospace;">{{ $sku }}</td>
                </tr>
            @endif
            <tr>
                <td style="padding: 8px 0 6px; color: #6b6b80; border-top: 1px dashed #fecdd3;">Remaining Stock:</td>
                <td style="padding: 8px 0 6px; color: #dc2626; font-weight: 900; font-size: 18px; border-top: 1px dashed #fecdd3;">
                    {{ $currentStock }} units
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #6b6b80;">Threshold Level:</td>
                <td style="padding: 6px 0; color: #555166; font-weight: 600;">{{ $threshold }} units</td>
            </tr>
        </table>
    </div>

    <div style="text-align: center; margin: 28px 0 16px;">
        <a href="{{ route('admin.ecommerce.products.edit', $product) }}" class="cta-btn" style="background-color: #dc2626;" target="_blank">
            Restock Product in Admin &rarr;
        </a>
    </div>
@endsection
