<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-lg shadow-sm p-12">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Quiz Already Passed!</h1>
                <p class="text-gray-600 mb-8">You've already passed this quiz. No need to retake it.</p>
                <a href="{{ route('learning.course', $course->slug) }}" 
                   class="inline-block bg-accent text-white px-8 py-3 rounded-lg font-semibold hover:bg-accent-dark">
                    Back to Course
                </a>
            </div>
        </div>
    </div>
</x-app-layout>