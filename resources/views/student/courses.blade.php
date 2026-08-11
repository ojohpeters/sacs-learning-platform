<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-900 leading-tight">My Courses</h2>
                <p class="text-sm text-gray-500 mt-0.5">Pick up where you left off.</p>
            </div>
            <a href="{{ route('courses.catalog') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-accent hover:text-accent-dark">
                Browse catalog
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($enrollments->isEmpty())
                {{-- Empty state --}}
                <div class="max-w-md mx-auto text-center py-16">
                    <div class="w-16 h-16 rounded-2xl bg-accent/10 text-accent-dark flex items-center justify-center mx-auto mb-5">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No courses yet</h3>
                    <p class="text-gray-500 mb-6">You're not enrolled in any courses yet. Explore the catalog to get started.</p>
                    <a href="{{ route('courses.catalog') }}" class="inline-flex items-center gap-2 bg-accent text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-accent-dark transition-colors">
                        Browse Courses
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrollments as $enrollment)
                        @php
                            $course = $enrollment->course;
                            $mode = match($enrollment->learning_type) {
                                'inclass' => 'In-Class',
                                'sync'    => 'Live Online',
                                default   => 'Self-Paced',
                            };
                            $thumb = $course->thumbnail_path
                                ? (\Illuminate\Support\Str::startsWith($course->thumbnail_path, 'http') ? $course->thumbnail_path : asset('storage/' . $course->thumbnail_path))
                                : null;
                            $initials = \Illuminate\Support\Str::of($course->title)->explode(' ')->take(2)->map(fn($w) => \Illuminate\Support\Str::substr($w, 0, 1))->implode('');
                            $pct = $enrollment->progress_percent ?? 0;
                        @endphp

                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">
                            {{-- Thumbnail with fallback --}}
                            <div class="relative h-40 bg-primary">
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $course->title }}" class="w-full h-full object-cover"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="absolute inset-0 items-center justify-center bg-primary" style="display:none;">
                                        <span class="text-white/70 font-bold text-3xl">{{ $initials }}</span>
                                    </div>
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-white/70 font-bold text-3xl">{{ $initials }}</span>
                                    </div>
                                @endif

                                <span class="absolute top-3 left-3 bg-white/90 backdrop-blur text-primary text-xs font-semibold px-2.5 py-1 rounded-full">{{ $mode }}</span>

                                @if($pct >= 100)
                                    <span class="absolute top-3 right-3 bg-success text-white text-xs font-semibold px-2.5 py-1 rounded-full">Completed</span>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="text-base font-semibold text-gray-900 mb-1 line-clamp-1">{{ $course->title }}</h3>
                                <p class="text-xs text-gray-500 mb-4">Enrolled {{ $enrollment->enrolled_at->format('M j, Y') }}</p>

                                {{-- Progress --}}
                                <div class="mb-5 mt-auto">
                                    <div class="flex items-center justify-between text-xs mb-1.5">
                                        <span class="text-gray-500">{{ $enrollment->progress_completed ?? 0 }} of {{ $enrollment->progress_total ?? 0 }} lessons</span>
                                        <span class="font-semibold text-primary">{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-success' : 'bg-accent' }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>

                                <a href="{{ route('learning.course', $course->slug) }}"
                                   class="block w-full text-center bg-accent text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-accent-dark transition-colors">
                                    {{ $pct > 0 ? 'Continue learning' : 'Start learning' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
