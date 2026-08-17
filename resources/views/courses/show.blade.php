<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            <a href="{{ route('courses.catalog') }}" class="text-accent hover:text-accent-dark text-sm font-medium">
                ← All Courses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Course Header --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>
                    <p class="text-lg text-gray-600 mb-6">{{ $course->short_description }}</p>

                    <div class="flex items-center justify-between">
                        @auth
                        @php
                        $enrolled = auth()->user()->enrollments()
                        ->where('course_id', $course->id)
                        ->where('status', 'active')
                        ->exists();
                        @endphp
                        @if($enrolled)
                        <div>
                            <span class="text-success-dark font-medium text-lg">✓ You are enrolled in this course</span>
                        </div>
                        <a href="{{ route('learning.course', $course->slug) }}"
                            class="bg-success text-white px-8 py-3 rounded-lg font-semibold hover:bg-success-dark transition-colors">
                            Continue Learning →
                        </a>
                        @else
                    </div>{{-- Close the flex container --}}

                    {{-- Payment Cards --}}
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Choose Your Learning Mode</h2>
                        <div class="grid md:grid-cols-3 gap-6">

                            {{-- In-Class Learning --}}
                            <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-primary hover:shadow-lg transition-all text-center">
                                <div class="text-sm font-semibold text-primary uppercase tracking-wide mb-3">In Class Learning</div>
                                <div class="text-3xl font-extrabold text-gray-900 mb-4">
                                    ₦{{ number_format($course->price + 4000) }}
                                </div>
                                <ul class="text-sm text-gray-600 space-y-2 mb-6 text-left">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Attend Physical classes
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Get a chance to meet instructors
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Enjoy taking classes with others
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Full access to course materials
                                    </li>
                                </ul>
                                <a href="{{ route('checkout.show', ['token' => $tokens['inclass']]) }}"
                                    class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">
                                    Enroll Now
                                </a>
                            </div>

                            {{-- E-Learning (Asynchronous) --}}
                            <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-green-500 hover:shadow-lg transition-all text-center">
                                <div class="text-sm font-semibold text-success uppercase tracking-wide mb-3">E-learning (Asynchronous)</div>
                                <div class="text-3xl font-extrabold text-gray-900 mb-4">
                                    ₦{{ number_format($course->async_price) }}
                                </div>
                                <ul class="text-sm text-gray-600 space-y-2 mb-6 text-left">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Have Access to all Learning Materials
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Reach Instructors through email
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Enjoy taking courses at your own comfort
                                    </li>
                                </ul>
                                <a href="{{ route('checkout.show', ['token' => $tokens['async']]) }}"
                                    class="block w-full bg-success text-white py-3 rounded-lg font-semibold hover:bg-success-dark transition-colors">
                                    Enroll Now
                                </a>
                            </div>

                            {{-- E-Learning (Synchronous) --}}
                            <div class="border-2 border-accent rounded-xl p-6 bg-accent/5 hover:shadow-lg transition-all text-center">
                                <div class="text-sm font-semibold text-accent uppercase tracking-wide mb-3">E-learning (Synchronous)</div>
                                <div class="text-3xl font-extrabold text-gray-900 mb-4">
                                    ₦{{ number_format($course->price - 10000 + 4000) }}
                                </div>
                                <ul class="text-sm text-gray-600 space-y-2 mb-6 text-left">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Attend classes from the convenience of your home
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Get a chance to meet instructors
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Enjoy taking classes with others
                                    </li>
                                </ul>
                                <a href="{{ route('checkout.show', ['token' => $tokens['sync']]) }}"
                                    class="block w-full bg-accent text-white py-3 rounded-lg font-semibold hover:bg-accent-dark transition-colors">
                                    Enroll Now
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Reopen flex container for enrolled state --}}
                    <div>
                        @endif
                        @else
                    </div>{{-- Close the flex container --}}

                    {{-- Payment Cards for Guests --}}
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Choose Your Learning Mode</h2>
                        <div class="grid md:grid-cols-3 gap-6">

                            {{-- In-Class --}}
                            <div class="border-2 border-gray-200 rounded-xl p-6 text-center">
                                <div class="text-sm font-semibold text-primary uppercase tracking-wide mb-3">In Class Learning</div>
                                <div class="text-3xl font-extrabold text-gray-900 mb-4">₦{{ number_format($course->price + 4000) }}</div>
                                <ul class="text-sm text-gray-600 space-y-2 mb-6 text-left">
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Attend Physical classes</li>
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Get a chance to meet instructors</li>
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Enjoy taking classes with others</li>
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Full access to course materials</li>
                                </ul>
                                <a href="{{ route('checkout.show', ['token' => $tokens['inclass']]) }}" class="block w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-700 transition-colors">Register to Enroll</a>
                            </div>

                            {{-- Asynchronous --}}
                            <div class="border-2 border-gray-200 rounded-xl p-6 text-center">
                                <div class="text-sm font-semibold text-success uppercase tracking-wide mb-3">E-learning (Asynchronous)</div>
                                <div class="text-3xl font-extrabold text-gray-900 mb-4">₦{{ number_format($course->async_price) }}</div>
                                <ul class="text-sm text-gray-600 space-y-2 mb-6 text-left">
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Have Access to all Learning Materials</li>
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Reach Instructors through email</li>
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Enjoy taking courses at your own comfort</li>
                                </ul>
                                <a href="{{ route('checkout.show', ['token' => $tokens['async']]) }}" class="block w-full bg-success text-white py-3 rounded-lg font-semibold hover:bg-success-dark transition-colors">Register to Enroll</a>
                            </div>

                            {{-- Synchronous --}}
                            <div class="border-2 border-accent rounded-xl p-6 bg-accent/5 text-center">
                                <div class="text-sm font-semibold text-accent uppercase tracking-wide mb-3">E-learning (Synchronous)</div>
                                <div class="text-3xl font-extrabold text-gray-900 mb-4">₦{{ number_format($course->price - 10000 + 4000) }}</div>
                                <ul class="text-sm text-gray-600 space-y-2 mb-6 text-left">
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Attend classes from the convenience of your home</li>
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Get a chance to meet instructors</li>
                                    <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>Enjoy taking classes with others</li>
                                </ul>
                                <a href="{{ route('checkout.show', ['token' => $tokens['sync']]) }}" class="block w-full bg-accent text-white py-3 rounded-lg font-semibold hover:bg-accent-dark transition-colors">Register to Enroll</a>
                            </div>

                        </div>
                    </div>
                    <div>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Course Description --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">About This Course</h2>
                    @php
                    $description = preg_replace(
                    '/\b(Overview|Course Outline|What you will learn|Requirements)\b/i',
                    '<span class="course-description-heading">$0</span>',
                    $course->full_description
                    );
                    @endphp
                    <div class="prose max-w-none text-gray-600">
                        {!! $description !!}
                    </div>
                </div>
            </div>

            <style>
                .course-description-heading {
                    display: inline-block;
                    font-size: 1.125rem;
                    font-weight: 700;
                    color: #111827;
                    margin-top: 1rem;
                    margin-bottom: 0.35rem;
                }
            </style>

            {{-- Free Preview Lessons --}}
            @if($course->sections->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Course Preview</h2>
                    <p class="text-gray-500 mb-6">Free lessons available before enrollment.</p>

                    @foreach($course->sections as $section)
                    @if($section->lessons->isNotEmpty())
                    <div class="mb-4">
                        <h3 class="font-medium text-gray-900 mb-2">{{ $section->title }}</h3>
                        <ul class="space-y-2">
                            @foreach($section->lessons as $lesson)
                            <li>
                                <a href="{{ route('courses.preview', [$course->slug, $lesson->id]) }}"
                                    class="flex items-center text-gray-600 hover:text-accent transition-colors py-1">
                                    <svg class="w-4 h-4 mr-2 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $lesson->title }}
                                    <span class="ml-auto text-xs text-green-600 bg-green-100 px-2 py-0.5 rounded">Free</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>