@extends('admin.layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Payments</h1>

    {{-- Payment Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Revenue</div>
            <div class="text-3xl font-bold text-green-600 mt-2">₦{{ number_format($totalRevenue) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Pending Payments</div>
            <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $totalPending }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Failed Payments</div>
            <div class="text-3xl font-bold text-red-600 mt-2">{{ $totalFailed }}</div>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white rounded-lg shadow">
        <table class="w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="px-6 py-3">Reference</th>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Course</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Amount</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $payment->transaction_reference }}</td>
                        <td class="px-6 py-4 text-sm">{{ $payment->user->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $payment->course->title }}</td>
                        <td class="px-6 py-4 text-sm">{{ ucfirst($payment->learning_type) }}</td>
                        <td class="px-6 py-4 text-sm">₦{{ number_format($payment->amount) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($payment->status === 'successful') bg-green-100 text-green-800
                                @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>
@endsection