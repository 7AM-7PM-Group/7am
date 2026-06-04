<x-layouts.mail.default>
    <!-- Life is available only in the present moment. - Thich Nhat Hanh -->
    <p>Hello {{ $user->name }},</p>

    <p>Thank you for submitting your business registration request.</p>

    <p>After review, we regret to inform you that your request has been <strong>declined</strong>.</p>

    <p><strong>Business details:</strong></p>
    <ul>
        <li><strong>Business Name:</strong> {{ $user->Businesses->name ?? '' }}</li>
    </ul>

    <p>You may submit a new request after addressing the issue mentioned above.</p>

    <p>If you need help, contact us at <a href="mailto:csb2b@sevenambakers.com">csb2b@sevenambakers.com</a>.

    <p>Warm regards,<br>
        <strong>7AM Bakers Team</strong>
    </p>
</x-layouts.mail.default>
