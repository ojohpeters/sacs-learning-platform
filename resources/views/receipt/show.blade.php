<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Confirmation
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success Banner --}}
            <div class="bg-success text-white rounded-xl p-8 text-center mb-8 shadow-lg">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold mb-2">Payment Successful!</h1>
                <p class="text-green-100 text-lg">Your enrollment has been confirmed.</p>
            </div>

            {{-- Payment Details Card --}}
            <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Details</h2>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Course</span>
                        <span class="font-semibold text-gray-900">{{ $payment->course->title }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Learning Type</span>
                        <span class="font-semibold text-gray-900">In-Class Learning</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Amount Paid</span>
                        <span class="font-semibold text-success text-lg">₦{{ number_format($payment->amount) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Transaction Reference</span>
                        <span class="font-semibold text-gray-900 font-mono text-sm">{{ $payment->transaction_reference }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Date</span>
                        <span class="font-semibold text-gray-900">{{ $payment->paid_at->format('F j, Y \a\t h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Next Steps Card --}}
            <div class="bg-white rounded-xl shadow-sm p-8 mb-6 border-l-4 border-accent">
                <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    What To Do Next
                </h2>
                
                <div class="space-y-4 ml-8">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">1</div>
                        <div>
                            <p class="font-medium text-gray-900">Print or save this receipt</p>
                            <p class="text-sm text-gray-600">Keep a copy of this payment confirmation — either printed or on your phone.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">2</div>
                        <div>
                            <p class="font-medium text-gray-900">Visit our office</p>
                            <p class="text-sm text-gray-600">Come to our training center during office hours to complete your registration in person.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-accent text-white rounded-full flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">3</div>
                        <div>
                            <p class="font-medium text-gray-900">Bring the following items</p>
                            <ul class="text-sm text-gray-600 list-disc list-inside mt-1">
                                <li>A printed copy of this receipt (or show it on your phone)</li>
                                <li>A passport photograph</li>
                                <li>A laptop (if you have one)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Address Card --}}
            <div class="bg-primary rounded-xl shadow-sm p-8 text-white mb-6">
                <h2 class="text-lg font-bold mb-4 flex items-center">
                    <svg class="w-6 h-6 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Our Office Address
                </h2>
                
                <div class="ml-8 space-y-3">
                    <p class="text-lg font-semibold text-accent-light">SACS Computers</p>
                    <p class="text-gray-300">Inikpi Street, High Level</p>
                    <p class="text-gray-300">Makurdi, Benue State</p>
                    <p class="text-gray-300">Nigeria</p>
                    
                    <div class="mt-4 pt-4 border-t border-primary-700 space-y-2">
                        <p class="flex items-center text-sm text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Office Hours: Monday – Friday, 9:00 AM – 5:00 PM
                        </p>
                        <p class="flex items-center text-sm text-gray-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Please call ahead for large groups
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('receipt.download', $payment) }}" 
                   class="flex-1 bg-white border-2 border-accent text-accent text-center py-3 rounded-xl font-semibold hover:bg-accent hover:text-white transition-colors">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Receipt
                    </span>
                </a>
                <a href="{{ route('student.courses') }}" 
                   class="flex-1 bg-accent text-white text-center py-3 rounded-xl font-semibold hover:bg-accent-dark transition-colors">
                    Go to My Courses →
                </a>
            </div>

        </div>
    </div>
</x-app-layout>