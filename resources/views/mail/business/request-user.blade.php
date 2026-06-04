<x-layouts.mail.default>
    <p>Hello {{ $user->name }},</p>

    <p>Thank you for submitting your business registration request on our platform.</p>

    <p><strong>Your submission details:</strong></p>
    <ul>
        <li><strong>Business Name:</strong> {{ $user->Businesses->name ?? '' }}</li>
        <li><strong>Representative:</strong> {{ $user->Businesses->representative ?? '' }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Submission Date:</strong> {{ $user->Businesses->updated_at->format('F j, Y H:i') ?? '' }}</li>
    </ul>

    <p>Our team is currently reviewing your business information. You will receive an update once your business is
        either approved or declined.</p>

    <p>If you need help, contact us at <a href="mailto:csb2b@sevenambakers.com">csb2b@sevenambakers.com</a>.

    <p>Warm regards,
    </p>
    <br>
    <strong>7AM Bakers Team</strong>
</x-layouts.mail.default>
