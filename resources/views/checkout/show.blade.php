<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Complete Your Enrollment
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <!-- Course Info -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h3>
                        <p class="mt-2 text-gray-600">{{ $course->short_description }}</p>
                    </div>

                    <!-- Order Summary -->
                    <div class="border rounded-lg p-6 mb-8 bg-gray-50">
                        <h4 class="text-lg font-semibold mb-4">Order Summary</h4>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Learning Type</span>
                            <span class="font-medium">
                                @if($learningType === 'inclass')
                                    In-Class Learning
                                @elseif($learningType === 'sync')
                                    E-Learning (Synchronous)
                                @else
                                    E-Learning (Asynchronous)
                                @endif
                            </span>
                        </div>

                        <div class="flex justify-between py-2 border-t">
                            <span class="text-gray-600">Registration Fee</span>
                            <span class="font-medium">₦4,000</span>
                        </div>

                        <div class="flex justify-between py-2 border-t">
                            <span class="text-gray-600">Course Fee</span>
                            <span class="font-medium">₦{{ number_format($price - 4000) }}</span>
                        </div>

                        <div class="flex justify-between py-2 border-t text-lg font-bold">
                            <span>Total</span>
                            <span>₦{{ number_format($price) }}</span>
                        </div>
                    </div>

                    <!-- What You'll Get -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold mb-3">What You'll Get</h4>
                        <ul class="list-disc list-inside space-y-2 text-gray-600">
                            @if($learningType === 'inclass')
                                <li>Physical classroom attendance</li>
                                <li>Direct interaction with instructors</li>
                                <li>Full access to course materials</li>
                            @elseif($learningType === 'sync')
                                <li>Live online classes from home</li>
                                <li>Real-time interaction with instructors</li>
                                <li>Access to course materials</li>
                            @else
                                <li>Complete self-paced learning</li>
                                <li>Access to all course materials</li>
                                <li>Learn at your own comfort</li>
                            @endif
                        </ul>
                    </div>

                    <!-- Pay Button -->
                    <form action="{{ route('checkout.pay') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-primary text-white py-3 px-6 rounded-lg text-lg font-semibold hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-colors">
                            Proceed to Payment — ₦{{ number_format($price) }}
                        </button>
                    </form>

                    <!-- Cancel Link -->
                    <div class="mt-4 text-center">
                        <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            Cancel and return to courses
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>