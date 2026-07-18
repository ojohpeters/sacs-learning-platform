<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt — {{ $payment->transaction_reference }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto p-8 bg-white my-8 shadow-lg rounded-lg print:shadow-none print:my-0">
        
        {{-- Header --}}
        <div class="text-center border-b pb-6 mb-6">
            <div class="w-16 h-16 bg-gray-900 rounded-xl flex items-center justify-center mx-auto mb-3">
                <span class="text-white font-bold text-2xl">S</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">SACS Computers</h1>
            <p class="text-gray-600">Learning Platform</p>
            <p class="text-sm text-gray-500 mt-1">Payment Receipt</p>
        </div>

        {{-- Receipt Info --}}
        <div class="mb-6">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Receipt #:</span>
                <span class="font-mono font-semibold">{{ $payment->transaction_reference }}</span>
            </div>
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Date:</span>
                <span>{{ $payment->paid_at->format('F j, Y \a\t h:i A') }}</span>
            </div>
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Student:</span>
                <span class="font-semibold">{{ $payment->user->name }}</span>
            </div>
        </div>

        {{-- Payment Details --}}
        <table class="w-full mb-6 border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-900">
                    <th class="text-left py-2 text-sm">Description</th>
                    <th class="text-right py-2 text-sm">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="py-3">
                        <p class="font-semibold">{{ $payment->course->title }}</p>
                        <p class="text-sm text-gray-600">In-Class Learning — Registration & Course Fee</p>
                    </td>
                    <td class="text-right py-3 font-semibold">₦{{ number_format($payment->amount) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-900">
                    <td class="py-3 font-bold text-lg">Total Paid</td>
                    <td class="text-right py-3 font-bold text-lg">₦{{ number_format($payment->amount) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Instructions --}}
        <div class="border-t pt-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-2">Next Steps</h3>
            <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2">
                <li>Print this receipt or save it on your phone</li>
                <li>Visit <strong>SACS Computers, Inikpi Street, High Level, Makurdi, Benue State</strong></li>
                <li>Bring a passport photograph</li>
                <li>Bring your laptop if you have one</li>
                <li>Office Hours: Monday – Friday, 9:00 AM – 5:00 PM</li>
            </ol>
        </div>

        {{-- Footer --}}
        <div class="text-center text-xs text-gray-500 border-t pt-4">
            <p>SACS Computers • Inikpi Street, High Level, Makurdi, Benue State</p>
            <p>This is a computer-generated receipt and does not require a signature.</p>
        </div>

        {{-- Print Button --}}
        <div class="text-center mt-6 no-print">
            <button onclick="window.print()" 
                    class="bg-gray-900 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800">
                🖨️ Print Receipt
            </button>
        </div>
    </div>

    {{-- Auto-trigger print --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>