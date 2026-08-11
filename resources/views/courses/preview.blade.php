<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }} — Free Preview
            </h2>
            <a href="{{ route('courses.show', $course->slug) }}" class="text-accent hover:text-accent-dark text-sm font-medium">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="flex h-[calc(100vh-8rem)]">
        
        {{-- Sidebar: Curriculum (Free Lessons Only) --}}
        <div class="w-80 bg-white border-r overflow-y-auto flex-shrink-0">
            <div class="p-4 border-b bg-yellow-50">
                <h3 class="font-semibold text-gray-900">Free Preview</h3>
                <p class="text-xs text-yellow-700 mt-1">
                    <span class="font-medium">{{ $course->sections->sum(fn($s) => $s->lessons->where('is_free_preview', true)->count()) }}</span> 
                    free lessons available. Enroll for full access.
                </p>
            </div>

            @foreach($course->sections as $section)
                @php $freeLessons = $section->lessons->where('is_free_preview', true); @endphp
                @if($freeLessons->isNotEmpty())
                    <div class="border-b">
                        <button 
                            class="w-full text-left px-4 py-3 font-medium text-gray-900 hover:bg-gray-50 flex justify-between items-center"
                            onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-90')">
                            <span class="text-sm">{{ $section->title }}</span>
                            <svg class="w-4 h-4 transition-transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div class="bg-gray-50">
                            @foreach($freeLessons as $freeLesson)
                                <a href="{{ route('courses.preview', [$course->slug, $freeLesson->id]) }}" 
                                   class="flex items-center px-6 py-3 text-sm hover:bg-gray-100 border-l-2 transition-colors
                                          {{ isset($lesson) && $lesson->id === $freeLesson->id ? 'border-yellow-500 bg-yellow-50 text-yellow-800 font-medium' : 'border-transparent text-gray-700' }}">
                                    
                                    {{-- Lesson Type Icon --}}
                                    <span class="mr-3 flex-shrink-0">
                                        @if($freeLesson->content_type === 'video')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            </svg>
                                        @elseif($freeLesson->content_type === 'text')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        @endif
                                    </span>

                                    <span class="truncate">{{ $freeLesson->title }}</span>
                                    <span class="ml-auto text-xs text-green-600">Free</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1 overflow-y-auto bg-gray-100">
            @if(isset($lesson))
                <div class="max-w-4xl mx-auto py-8 px-6">
                    
                    {{-- Free Preview Banner --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-800 font-medium">Free Preview Lesson</p>
                                <p class="text-yellow-600 text-sm">You're viewing a free lesson. Enroll to unlock all {{ $course->lessons->count() }} lessons.</p>
                            </div>
                            @auth
                                <a href="{{ route('checkout.show', ['token' => $tokens['inclass']]) }}" 
                                   class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors whitespace-nowrap">
                                    Enroll Now
                                </a>
                            @else
                                <a href="{{ route('checkout.show', ['token' => $tokens['inclass']]) }}" 
                                   class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors whitespace-nowrap">
                                    Register to Enroll
                                </a>
                            @endauth
                        </div>
                    </div>

                    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $lesson->title }}</h1>

                    @php
                        $contentSrc = \Illuminate\Support\Str::startsWith($lesson->content_path, 'http')
                            ? $lesson->content_path
                            : route('lesson.content', [$course->slug, $lesson->id]);
                    @endphp

                    {{-- Video Content --}}
                    @if($lesson->content_type === 'video')
                        <div class="bg-black rounded-lg overflow-hidden mb-6">
                            <video controls controlsList="nodownload noplaybackrate" disablePictureInPicture
                                   oncontextmenu="return false" class="w-full" style="max-height: 500px;">
                                <source src="{{ $contentSrc }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @endif

                    {{-- Image Content --}}
                    @if($lesson->content_type === 'image')
                        <div class="bg-white rounded-lg overflow-hidden mb-6">
                            <img src="{{ $contentSrc }}" alt="{{ $lesson->title }}" class="w-full" oncontextmenu="return false">
                        </div>
                    @endif

                    {{-- PDF Content (inline viewer) --}}
                    @if($lesson->content_type === 'pdf')
                        <div class="bg-gray-100 rounded-lg overflow-hidden mb-6 shadow-sm" style="height: 80vh;">
                            <iframe src="{{ $contentSrc }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                                    class="w-full h-full" style="border: 0;" oncontextmenu="return false"
                                    title="{{ $lesson->title }}"></iframe>
                        </div>
                    @endif

                    {{-- Text Content --}}
                    @if($lesson->content_body)
                        <div class="bg-white rounded-lg p-6 shadow-sm prose max-w-none">
                            {!! $lesson->content_body !!}
                        </div>
                    @endif

                </div>
            @else
                {{-- No lesson selected --}}
                <div class="max-w-2xl mx-auto py-16 px-6 text-center">
                    <div class="bg-white rounded-lg p-12 shadow-sm">
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Free Course Preview</h2>
                        <p class="text-gray-600 mb-8">Select a free lesson from the sidebar to preview this course before enrolling.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>