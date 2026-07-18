@extends('admin.layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Dashboard Overview</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Courses</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $total_courses }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Students</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $total_students }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Enrollments</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $total_enrollments }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Revenue</div>
            <div class="text-3xl font-bold text-green-600 mt-2">₦{{ number_format($total_revenue) }}</div>
        </div>
    </div>

    {{-- Recent Enrollments --}}
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Recent Enrollments</h2>
        </div>
        <div class="p-6">
            @if($recent_enrollments->isEmpty())
                <p class="text-gray-500">No enrollments yet.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500">
                            <th class="pb-3">Student</th>
                            <th class="pb-3">Course</th>
                            <th class="pb-3">Type</th>
                            <th class="pb-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recent_enrollments as $enrollment)
                            <tr>
                                <td class="py-3 text-sm">{{ $enrollment->user->name }}</td>
                                <td class="py-3 text-sm">{{ $enrollment->course->title }}</td>
                                <td class="py-3 text-sm">{{ ucfirst($enrollment->learning_type) }}</td>
                                <td class="py-3 text-sm text-gray-500">{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Recent Payments</h2>
        </div>
        <div class="p-6">
            @if($recent_payments->isEmpty())
                <p class="text-gray-500">No payments yet.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500">
                            <th class="pb-3">Reference</th>
                            <th class="pb-3">User</th>
                            <th class="pb-3">Amount</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recent_payments as $payment)
                            <tr>
                                <td class="py-3 text-sm font-mono">{{ $payment->transaction_reference }}</td>
                                <td class="py-3 text-sm">{{ $payment->user->name }}</td>
                                <td class="py-3 text-sm">₦{{ number_format($payment->amount) }}</td>
                                <td class="py-3 text-sm">
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        @if($payment->status === 'successful') bg-green-100 text-green-800
                                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="py-3 text-sm text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection