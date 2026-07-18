<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Browse Courses
            </h2>
            @auth
            <a href="{{ route('student.courses') }}" class="text-accent hover:text-accent-dark text-sm font-medium">
                ← My Courses
            </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($courses->isEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No Courses Available</h3>
                    <p class="mt-2 text-gray-500">Check back soon for new courses.</p>
                </div>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    {{-- Thumbnail --}}
                    <div class="h-48 bg-primary flex items-center justify-center">
                        @if($course->thumbnail_path)
                        <img src="{{ Str::startsWith($course->thumbnail_path, 'http') ? $course->thumbnail_path : asset('storage/' . $course->thumbnail_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                        <div class="text-center">
                            <div class="w-16 h-16 bg-accent rounded-xl flex items-center justify-center mx-auto mb-2">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $course->short_description }}</p>

                        <div class="space-y-1.5 mb-4">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">In-Class</span>
                                <span class="font-semibold text-gray-900">₦{{ number_format($course->price + 4000) }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Live Online</span>
                                <span class="font-semibold text-gray-900">₦{{ number_format($course->price - 10000 + 4000) }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Self-Paced</span>
                                <span class="font-semibold text-green-600">₦{{ number_format($course->price - 25000 + 4000) }}</span>
                            </div>
                        </div>

                        <div class="text-xs text-gray-400 mb-4 text-center">{{ $course->enrollments_count }} students enrolled</div>

                        <a href="{{ route('courses.show', $course->slug) }}"
                            class="block w-full text-center bg-primary text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-primary-700 transition-colors">
                            View Details & Enroll →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $courses->links() }}
            </div>
            @endif

        </div>
    </div>
</x-app-layout>