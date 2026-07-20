<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate — {{ $course->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #eef2f7;
            color: #1f2937;
            padding: 32px 16px;
        }
        .certificate {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #d4af37;
            border-radius: 12px;
            padding: 56px 64px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            position: relative;
        }
        .certificate::before {
            content: "";
            position: absolute;
            inset: 12px;
            border: 1px solid #e5c76b;
            border-radius: 8px;
            pointer-events: none;
        }
        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 4px;
            font-size: 13px;
            color: #b8892b;
            margin-bottom: 24px;
        }
        h1 { font-size: 34px; margin-bottom: 8px; color: #111827; }
        .subtitle { font-size: 15px; color: #6b7280; margin-bottom: 32px; }
        .recipient {
            font-size: 30px;
            color: #1d4ed8;
            border-bottom: 2px solid #e5e7eb;
            display: inline-block;
            padding: 0 24px 8px;
            margin-bottom: 24px;
        }
        .course { font-size: 22px; font-weight: bold; margin: 8px 0 32px; }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-top: 48px;
            font-size: 13px;
            color: #6b7280;
        }
        .meta .value { font-size: 15px; color: #111827; }
        .actions { text-align: center; margin: 24px auto 0; max-width: 900px; }
        .btn {
            display: inline-block;
            background: #1d4ed8;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .actions { display: none; }
            .certificate { box-shadow: none; border-color: #d4af37; }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="eyebrow">Certificate of Completion</div>
        <h1>SACS Learning Platform</h1>
        <p class="subtitle">This is proudly presented to</p>

        <div class="recipient">{{ $user->name }}</div>

        <p class="subtitle">for successfully completing the course</p>
        <div class="course">{{ $course->title }}</div>

        <div class="meta">
            <div>
                <div>Date</div>
                <div class="value">{{ $completedAt->format('F j, Y') }}</div>
            </div>
            <div>
                <div>Certificate ID</div>
                <div class="value">SACS-{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    <div class="actions">
        <a href="javascript:window.print()" class="btn">Print / Save as PDF</a>
    </div>
</body>
</html>
