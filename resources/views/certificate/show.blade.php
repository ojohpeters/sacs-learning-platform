<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate — {{ $course->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #e9edf2;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* The stage matches the template's 1131x1600 aspect ratio. Text is
           positioned as a percentage and sized in cqw so it scales with the
           certificate on any screen and when printed. */
        .certificate {
            position: relative;
            width: min(820px, 96vw);
            aspect-ratio: 1131 / 1600;
            background: #fbf9ec;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
            container-type: inline-size;
        }
        .certificate img.template {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            user-select: none;
            pointer-events: none;
        }

        .field {
            position: absolute;
            left: 0;
            right: 0;
            text-align: center;
            color: #1f2933;
        }
        .field-name {
            top: 56.2%;
            font-size: 3.4cqw;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #2b2b2b;
        }
        .field-course {
            top: 70.3%;
            font-size: 3.6cqw;
            font-weight: 700;
            color: #141414;
        }
        .field-date {
            top: 82.4%;
            left: 9.5%;
            right: auto;
            width: 34%;
            text-align: center;
            font-size: 1.5cqw;
            color: #333;
        }
        .field-verify {
            top: 89.4%;
            left: 30.8%;
            right: auto;
            width: 47%;
            text-align: left;
            font-size: 1.18cqw;
            line-height: 1.45;
            color: #333;
        }
        .field-verify a { color: #1d4ed8; text-decoration: none; word-break: break-all; }
        .field-verify a:hover { text-decoration: underline; }

        .actions { margin-top: 20px; }
        .btn {
            display: inline-block;
            background: #1d4ed8;
            color: #fff;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .actions { display: none; }
            .certificate { box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <img class="template" src="{{ asset('images/certificate-template.jpeg') }}" alt="SACS Computers Certificate of Training">

        {{-- Recipient --}}
        <div class="field field-name">{{ $user->name }}</div>

        {{-- Course --}}
        <div class="field field-course">{{ $course->title }}</div>

        {{-- Date (sits on the line above the "Date" label) --}}
        <div class="field field-date">{{ $completedAt->format('F j, Y') }}</div>

        {{-- Verification (clickable) --}}
        <div class="field field-verify">
            Verify at:<br>
            <a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a><br>
            SACS Computers has confirmed the identity of the individual
            and their participation in the course.
        </div>
    </div>

    <div class="actions">
        <a href="javascript:window.print()" class="btn">Print / Save as PDF</a>
    </div>
</body>
</html>
