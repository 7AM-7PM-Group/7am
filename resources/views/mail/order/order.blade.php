<x-layouts.mail.default>
    <p>Hi <strong>{{ $order->user->Businesses->name ?? '' }}</strong>,</p>
    <p>Thank you for your order. Your order has been successfully created and is currently <strong>awaiting
            payment</strong>.</p>


    <p><strong>Order Details:</strong></p>
    <ul>
        <li><strong>Order Number:</strong> {{ $order->transaction_number ?? '' }}</li>
        <li><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y H:i') ?? '' }}</li>
        <li><strong>Shipping Date:</strong> {{ $order->shipping_date->format('F j, Y') ?? '' }}</li>
        <li><strong>Total Amount:</strong> Rp. {{ number_format($order->total ?? 0, 2, ',', '.') }}</li>
    </ul>

    <p>Please confirm again your order to our admin by click button below</p>
    <p><a style="padding: 8px 4px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;"
            href="https://wa.me/{{ Setting::where('key', 'b2b_whatsapp_number')->value('value') }}">Confirm Order by
            Whatsapp</a>
    </p>

    <p>Please complete your payment using the following link:</p>
    <p><a href=" {{ route('checkout', $order->slug ?? '') }}">Complete Payment</a></p>

    <p>If you need help, contact us at <a href="mailto:csb2b@sevenambakers.com">csb2b@sevenambakers.com</a>.
    </p>
    <p>Thank you for choosing 7AM Bakers Club</p>
</x-layouts.mail.default>
