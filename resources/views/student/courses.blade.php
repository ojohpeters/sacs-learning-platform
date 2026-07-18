<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Courses
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($enrollments->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">You are not enrolled in any courses yet.</p>
                        <a href="{{ route('courses.catalog') }}" class="mt-4 inline-block text-accent hover:text-accent-dark font-medium">
                            Browse Courses →
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrollments as $enrollment)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $enrollment->course->title }}
                                </h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Learning Type: 
                                    <span class="font-medium">
                                        @if($enrollment->learning_type === 'inclass')
                                            In-Class
                                        @elseif($enrollment->learning_type === 'sync')
                                            Synchronous E-Learning
                                        @else
                                            Asynchronous E-Learning
                                        @endif
                                    </span>
                                </p>
                                <p class="mt-1 text-sm text-gray-600">
                                    Enrolled: {{ $enrollment->enrolled_at->format('M d, Y') }}
                                </p>
                                <a href="{{ route('learning.course', $enrollment->course->slug) }}" 
                                    class="mt-4 inline-block bg-primary text-white px-4 py-2 rounded text-sm hover:bg-primary-700 transition-colors">
                                    Start Learning →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>