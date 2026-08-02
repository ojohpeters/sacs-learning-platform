<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification — SACS Computers</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            background: #f3f4f6;
            color: #1f2933;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            max-width: 520px;
            width: 100%;
            padding: 40px 36px;
            text-align: center;
        }
        .brand { font-weight: 800; letter-spacing: 0.5px; color: #111827; font-size: 20px; margin-bottom: 24px; }
        .brand span { color: #14b8a6; }
        .badge {
            width: 72px; height: 72px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; margin: 0 auto 16px;
        }
        .ok { background: #dcfce7; color: #16a34a; }
        .bad { background: #fee2e2; color: #dc2626; }
        h1 { font-size: 20px; margin-bottom: 6px; }
        .muted { color: #6b7280; font-size: 14px; margin-bottom: 24px; }
        .details { text-align: left; border-top: 1px solid #eee; padding-top: 16px; }
        .row { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; font-size: 14px; }
        .row .label { color: #6b7280; }
        .row .value { font-weight: 600; text-align: right; }
        .code { font-family: ui-monospace, monospace; font-size: 12px; color: #6b7280; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">SACS <span>COMPUTERS</span></div>

        @if ($enrollment)
            <div class="badge ok">✓</div>
            <h1>Certificate verified</h1>
            <p class="muted">SACS Computers has confirmed the identity of the individual
                and their participation in the course.</p>

            <div class="details">
                <div class="row"><span class="label">Name</span><span class="value">{{ $enrollment->user->name }}</span></div>
                <div class="row"><span class="label">Course</span><span class="value">{{ $enrollment->course->title }}</span></div>
                <div class="row">
                    <span class="label">Issued</span>
                    <span class="value">
                        {{ optional($enrollment->certificate_issued_at ?? $enrollment->completed_at)->format('F j, Y') ?? '—' }}
                    </span>
                </div>
            </div>
            <p class="code">Certificate ID: {{ $enrollment->certificate_code }}</p>
        @else
            <div class="badge bad">✕</div>
            <h1>Certificate not found</h1>
            <p class="muted">We couldn't find a certificate matching this code. Please
                check the link and try again.</p>
            <p class="code">Code checked: {{ $code }}</p>
        @endif
    </div>
</body>
</html>
