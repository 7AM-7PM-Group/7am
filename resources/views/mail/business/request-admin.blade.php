<x-layouts.mail.default class="mail default">
    <p>Hello Admin,</p>

    <p>A new business registration request has been submitted and requires your review.</p>

    <p><strong>Submission details:</strong></p>
    <ul>
        <li><strong>Business Name:</strong> {{ $user->Businesses->name ?? '' }}</li>
        <li><strong>Representative:</strong> {{ $user->Businesses->representative ?? '' }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Submission Date:</strong> {{ $user->Businesses->updated_at->format('F j, Y H:i') ?? '' }}</li>
    </ul>

    <p>Please review and process this request in the admin dashboard.</p>

    <p>Regards,<br>
        <strong>System Notification</strong>
    </p>
</x-layouts.mail.default>
