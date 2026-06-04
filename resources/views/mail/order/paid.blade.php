<x-layouts.mail.default>
    <p>Hi <strong>{{ $order->user->Businesses->name ?? '' }}</strong>,</p>
    <p>We have successfully received your payment. Your order is now confirmed and will be processed soon.</p>

    <p><strong>Order Details:</strong></p>
    <ul>
        <li><strong>Order Number:</strong> {{ $order->transaction_number ?? '' }}</li>
        <li><strong>Payment Date:</strong> {{ $order->payment->created_at->format('F j, Y H:i') ?? '' }}</li>
        <li><strong>Payment Method:</strong> {{ $order->payment->payment_method ?? '' }}</li>
        <li><strong>Total Paid:</strong> Rp. {{ number_format($order->payment->amount ?? 0, 2, ',', '.') }}</li>
    </ul>


    <p>You can view your order here:</p>
    <p><a href="{{ route('history') }}">View History Order</a></p>

    <p>If you need help, contact us at <a href="mailto:csb2b@sevenambakers.com">csb2b@sevenambakers.com</a>.
    </p>
    <p>Thank you for choosing 7AM Bakers Club</p>
</x-layouts.mail.default>
