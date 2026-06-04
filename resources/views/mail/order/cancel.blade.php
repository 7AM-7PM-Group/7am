<x-layouts.mail.default>
    <p>Hi <strong>{{ $order->user->Businesses->name ?? ($order->user->name ?? '') }}</strong>,</p>

    <p>Your order has been <strong>cancelled by
            our admin team</strong>.</p>

    @if (!empty($order->cancellation_reason))
        <p><strong>Cancellation reason:</strong> {{ $order->cancellation_reason }}</p>
    @endif

    <p><strong>Order Details:</strong></p>
    <ul>
        <li><strong>Order Number:</strong> {{ $order->transaction_number ?? '' }}</li>
        <li><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y H:i') ?? '' }}</li>
        <li><strong>Shipping Date:</strong> {{ $order->shipping_date->format('F j, Y') ?? '' }}</li>
        <li><strong>Total Amount:</strong> Rp. {{ number_format($order->total ?? 0, 2, ',', '.') }}</li>
    </ul>

    <p>If you need help, contact us at <a href="mailto:csb2b@sevenambakers.com">csb2b@sevenambakers.com</a>.
    </p>
    <p>Thank you for choosing 7AM Bakers Club</p>
</x-layouts.mail.default>
