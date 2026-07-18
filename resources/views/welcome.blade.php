<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SACS Computers — Master Tech Skills That Pay</title>
    <link rel="icon" type="image/png" href="https://placehold.co/32x32/1E3A5F/FFFFFF?text=S">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#1E3A5F',
                            700: '#172D4A',
                            800: '#102035'
                        },
                        accent: {
                            DEFAULT: '#3B82F6',
                            dark: '#2563EB',
                            light: '#93C5FD'
                        },
                        success: {
                            DEFAULT: '#059669',
                            light: '#D1FAE5'
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    },
                }
            }
        }
    </script>
</head>

<body class="font-sans antialiased bg-white">

    {{-- ============================================ --}}
    {{-- NAVBAR --}}
    {{-- ============================================ --}}
    <nav class="bg-primary sticky top-0 z-50 shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <div>
                        <span class="text-white font-bold text-lg">SACS</span>
                        <span class="text-accent-light text-xs block -mt-1">Computers</span>
                    </div>
                </a>
                <div class="flex items-center space-x-4">
                    @auth
                    <a href="{{ route('student.courses') }}" class="text-accent-light hover:text-white text-sm font-medium">My Courses</a>
                    @else
                    <a href="{{ route('login') }}" class="text-accent-light hover:text-white text-sm font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-accent text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-accent-dark transition-colors">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ============================================ --}}
    {{-- HERO SECTION --}}
    {{-- ============================================ --}}
    <section class="bg-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-accent rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-6xl mx-auto px-4 py-24 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    Learn Tech Skills That <span class="text-accent">Get You Hired</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-10 max-w-2xl mx-auto">
                    Master Web Development, Cybersecurity, Data Science, and more.
                    Learn from industry experts in-class, live online, or at your own pace.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#courses" class="bg-accent text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-accent-dark transition-colors shadow-lg shadow-accent/30">
                        Explore Courses
                    </a>
                    <a href="#how-it-works" class="border-2 border-white/30 text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white/10 transition-colors">
                        How It Works
                    </a>
                </div>
                <div class="mt-8 flex justify-center space-x-8 text-gray-400 text-sm">
                    <span>🎓 Certificate Awarded</span>
                    <span>💻 Hands-on Projects</span>
                    <span>👨‍🏫 Expert Instructors</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FEATURED COURSES --}}
    {{-- ============================================ --}}
    <section id="courses" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                    Courses We Offer
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Industry-relevant courses designed to take you from beginner to job-ready professional.
                </p>
            </div>

            @if($featuredCourses->isEmpty())
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
                    </svg>
                </div>
                <p class="text-gray-500 text-lg">Courses coming soon. Check back shortly!</p>
            </div>
            @else
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($featuredCourses as $course)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
                    {{-- Course Image --}}
                    <div class="h-48 bg-primary relative overflow-hidden">
                        @if($course->thumbnail_path)
                        <img src="{{ Str::startsWith($course->thumbnail_path, 'http') ? $course->thumbnail_path : asset('storage/' . $course->thumbnail_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <svg class="w-16 h-16 text-accent/30 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
                                </svg>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Course Details --}}
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $course->short_description }}</p>

                        {{-- Learning Modes & Prices --}}
                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">In-Class</span>
                                <span class="font-semibold text-gray-900">₦{{ number_format($course->price + 4000) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Live Online</span>
                                <span class="font-semibold text-gray-900">₦{{ number_format($course->price - 10000 + 4000) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Self-Paced</span>
                                <span class="font-semibold text-green-600">₦{{ number_format($course->price - 25000 + 4000) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('courses.show', $course->slug) }}"
                            class="block w-full text-center bg-primary text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors">
                            View Course Details →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($allCourses->count() > 3)
            <div class="text-center mt-8">
                <a href="{{ route('courses.catalog') }}" class="text-accent hover:text-accent-dark font-semibold">
                    View All {{ $allCourses->count() }} Courses →
                </a>
            </div>
            @endif
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- HOW IT WORKS --}}
    {{-- ============================================ --}}
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">How It Works</h2>
                <p class="text-lg text-gray-600">Three simple steps to start your learning journey.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Step 1 --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary/20">
                        <span class="text-3xl font-extrabold text-accent">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Choose Your Course</h3>
                    <p class="text-gray-600">Browse our catalog and pick the course that matches your career goals. Compare learning modes and prices.</p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary/20">
                        <span class="text-3xl font-extrabold text-accent">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Enroll & Pay Securely</h3>
                    <p class="text-gray-600">Create your account and pay securely online. Your enrollment is instant — start learning immediately.</p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-primary rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary/20">
                        <span class="text-3xl font-extrabold text-accent">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Learn & Get Certified</h3>
                    <p class="text-gray-600">Access video lessons, hands-on projects, and earn your certificate. Learn at your own pace or join live classes.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- LEARNING MODES --}}
    {{-- ============================================ --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Choose Your Learning Style</h2>
                <p class="text-lg text-gray-600">Flexibility to learn the way that works best for you.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- In-Class --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-shadow text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">In-Class Learning</h3>
                    <p class="text-gray-600 text-sm mb-3">Attend physical classes at our training center. Direct interaction with instructors and fellow students.</p>
                    <span class="text-sm font-semibold text-primary">Includes all course materials</span>
                </div>

                {{-- Live Online --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-shadow text-center border-2 border-accent/20">
                    <div class="w-16 h-16 bg-accent/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Live Online</h3>
                    <p class="text-gray-600 text-sm mb-3">Join live classes from anywhere. Real-time interaction with instructors via video conferencing.</p>
                    <span class="text-sm font-semibold text-accent">Most Popular</span>
                </div>

                {{-- Self-Paced --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-shadow text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Self-Paced</h3>
                    <p class="text-gray-600 text-sm mb-3">Learn at your own speed. Access all course materials anytime, anywhere. Best value.</p>
                    <span class="text-sm font-semibold text-success">Best Value</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- STATS / SOCIAL PROOF --}}
    {{-- ============================================ --}}
    <section class="py-20 bg-primary">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl font-extrabold text-accent mb-2">{{ $allCourses->count() }}+</div>
                    <div class="text-accent-light text-sm">Courses Available</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-accent mb-2">3</div>
                    <div class="text-accent-light text-sm">Learning Modes</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-accent mb-2">24/7</div>
                    <div class="text-accent-light text-sm">Access to Materials</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FINAL CTA --}}
    {{-- ============================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-3xl mx-auto text-center px-4">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                Ready to Transform Your Career?
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Join SACS Computers today and gain the skills employers are looking for.
            </p>
            @auth
            <a href="{{ route('courses.catalog') }}" class="inline-block bg-accent text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-accent-dark transition-colors shadow-lg shadow-accent/30">
                Browse Courses
            </a>
            @else
            <a href="{{ route('register') }}" class="inline-block bg-accent text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-accent-dark transition-colors shadow-lg shadow-accent/30">
                Get Started Free — It Takes 30 Seconds
            </a>
            @endauth
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================ --}}
    <footer class="bg-primary py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-6 md:mb-0">
                    <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">S</span>
                    </div>
                    <div>
                        <span class="text-white font-bold">SACS Computers</span>
                        <span class="text-gray-400 text-sm block">Learning Platform</span>
                    </div>
                </div>
                <div class="flex space-x-6 text-gray-400 text-sm">
                    <a href="{{ route('courses.catalog') }}" class="hover:text-white transition-colors">Courses</a>
                    <a href="#how-it-works" class="hover:text-white transition-colors">How It Works</a>
                    <a href="#" class="hover:text-white transition-colors">Contact</a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-primary-700 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} SACS Computers. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>