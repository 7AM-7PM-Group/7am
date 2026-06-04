<x-layouts.mail.default class="mail default">
    <p>Hello {{ $user->name }},</p>

    <p>Congratulations! Your business registration request has been <strong>approved</strong>.</p>

    <p><strong>Business details:</strong></p>
    <ul>
        <li><strong>Business Name:</strong> {{ $user->Businesses->name ?? '' }}</li>
    </ul>

    <p>You now have access to your business account, B2B ordering, special business pricing, and business profile
        management.</p>

    <p>Please log in to your account to start placing orders.</p>

    <p>Warm regards,<br>
        <strong>7AM Bakers Team</strong>
    </p>
</x-layouts.mail.default>
