<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Confirmation</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <div style="max-width:560px;margin:0 auto;padding:32px 16px;">
        <div style="background:#ffffff;border-radius:12px;padding:32px;">
            <h1 style="font-size:20px;margin:0 0 16px;">Welcome aboard, {{ $enrollment->user->name }}! 🎉</h1>

            <p style="font-size:15px;line-height:1.6;margin:0 0 16px;">
                You are now enrolled in <strong>{{ $enrollment->course->title }}</strong>.
            </p>

            <table style="width:100%;font-size:14px;border-collapse:collapse;margin:0 0 24px;">
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Course</td>
                    <td style="padding:8px 0;text-align:right;">{{ $enrollment->course->title }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Learning type</td>
                    <td style="padding:8px 0;text-align:right;text-transform:capitalize;">
                        {{ $enrollment->learning_type }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#6b7280;">Enrolled</td>
                    <td style="padding:8px 0;text-align:right;">
                        {{ optional($enrollment->enrolled_at)->format('M j, Y') ?? now()->format('M j, Y') }}
                    </td>
                </tr>
            </table>

            <a href="{{ route('learning.course', $enrollment->course->slug) }}"
               style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;font-size:15px;">
                Start learning
            </a>

            <p style="font-size:13px;line-height:1.6;color:#6b7280;margin:24px 0 0;">
                If you didn't make this enrollment, please contact support.
            </p>
        </div>

        <p style="text-align:center;font-size:12px;color:#9ca3af;margin:16px 0 0;">
            &copy; {{ date('Y') }} SACS Learning Platform
        </p>
    </div>
</body>
</html>
