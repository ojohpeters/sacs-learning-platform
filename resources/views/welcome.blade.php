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
                        primary: { DEFAULT: '#1E3A5F', 700: '#172D4A', 800: '#102035' },
                        accent: { DEFAULT: '#3B82F6', dark: '#2563EB', light: '#93C5FD' },
                        secondary: { DEFAULT: '#14B8A6', light: '#5EEAD4', dark: '#0D9488' },
                        success: { DEFAULT: '#059669', light: '#D1FAE5' },
                    },
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        .text-brand-gradient { background: linear-gradient(90deg, #5EEAD4, #93C5FD); -webkit-background-clip: text; background-clip: text; color: transparent; }
    </style>
</head>

<body class="font-sans antialiased bg-white text-gray-900">

    {{-- ============================================ NAVBAR ============================================ --}}
    <nav class="bg-primary/95 backdrop-blur sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent to-secondary flex items-center justify-center shadow-lg shadow-accent/30 group-hover:scale-105 transition-transform">
                        <span class="text-white font-black text-lg">S</span>
                    </div>
                    <div class="leading-tight">
                        <span class="text-white font-extrabold text-lg">SACS</span>
                        <span class="text-secondary-light text-xs block -mt-1 tracking-wide">Computers</span>
                    </div>
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('courses.catalog') }}" class="hidden sm:inline-flex text-gray-300 hover:text-white text-sm font-medium px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">Courses</a>
                    @auth
                    <a href="{{ route('student.courses') }}" class="text-secondary-light hover:text-white text-sm font-medium px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">My Courses</a>
                    @else
                    <a href="{{ route('login') }}" class="text-gray-100 hover:text-white text-sm font-medium px-3 py-2 rounded-lg hover:bg-white/10 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-accent to-secondary text-white px-5 py-2 rounded-xl text-sm font-semibold shadow-lg shadow-accent/30 hover:-translate-y-0.5 transition-all">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ============================================ HERO ============================================ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-primary via-accent-dark to-secondary-dark">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-accent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-6xl mx-auto px-4 py-24 md:py-28 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 px-4 py-1.5 text-sm text-white mb-6 backdrop-blur">
                    🚀 In-class · Live online · Self-paced
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                    Learn Tech Skills That <span class="text-brand-gradient">Get You Hired</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-200 mb-10 max-w-2xl mx-auto">
                    Master Web Development, Cybersecurity, Data Science, and more — from industry experts,
                    with hands-on projects and a certificate to prove it.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#courses" class="bg-white text-primary px-8 py-4 rounded-xl font-bold text-lg hover:-translate-y-0.5 transition-all shadow-xl">
                        Explore Courses
                    </a>
                    <a href="#how-it-works" class="border border-white/40 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition-colors backdrop-blur">
                        How It Works
                    </a>
                </div>
                <div class="mt-10 flex flex-wrap justify-center gap-x-8 gap-y-2 text-gray-200 text-sm">
                    <span>🎓 Certificate Awarded</span>
                    <span>💻 Hands-on Projects</span>
                    <span>👨‍🏫 Expert Instructors</span>
                </div>
            </div>
        </div>
        <div class="h-6 bg-gray-50 rounded-t-[2.5rem] relative z-10 -mb-1"></div>
    </section>

    {{-- ============================================ FEATURED COURSES ============================================ --}}
    <section id="courses" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold uppercase tracking-wider text-secondary-dark mb-2">Our Catalog</p>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Courses We Offer</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Industry-relevant courses designed to take you from beginner to job-ready professional.</p>
            </div>

            @if($featuredCourses->isEmpty())
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
                </div>
                <p class="text-gray-500 text-lg">Courses coming soon. Check back shortly!</p>
            </div>
            @else
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($featuredCourses as $course)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group border border-gray-100 flex flex-col">
                    {{-- Course Image --}}
                    <div class="h-44 relative overflow-hidden bg-gradient-to-br from-primary via-accent-dark to-secondary-dark">
                        @if($course->thumbnail_path)
                        <img src="{{ Str::startsWith($course->thumbnail_path, 'http') ? $course->thumbnail_path : asset('storage/' . $course->thumbnail_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                        <div class="flex items-center justify-center h-full">
                            <span class="text-white/80 font-black text-2xl">{{ Str::of($course->title)->explode(' ')->take(2)->map(fn($w) => Str::substr($w,0,1))->implode('') }}</span>
                        </div>
                        @endif
                        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur text-primary text-xs font-bold px-3 py-1 rounded-full">Certificate</span>
                    </div>

                    {{-- Course Details --}}
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $course->short_description }}</p>

                        <div class="space-y-2 mb-6 mt-auto">
                            <div class="flex justify-between text-sm"><span class="text-gray-500">In-Class</span><span class="font-semibold text-gray-900">₦{{ number_format($course->price + 4000) }}</span></div>
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Live Online</span><span class="font-semibold text-gray-900">₦{{ number_format($course->price - 10000 + 4000) }}</span></div>
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Self-Paced</span><span class="font-bold text-secondary-dark">₦{{ number_format($course->price - 25000 + 4000) }}</span></div>
                        </div>

                        <a href="{{ route('courses.show', $course->slug) }}" class="block w-full text-center bg-gradient-to-r from-accent to-secondary text-white py-3 rounded-xl font-semibold hover:opacity-95 hover:-translate-y-0.5 transition-all shadow-lg shadow-accent/20">
                            View Course →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($allCourses->count() > 3)
            <div class="text-center mt-10">
                <a href="{{ route('courses.catalog') }}" class="inline-flex items-center gap-2 text-accent-dark hover:text-secondary-dark font-semibold">
                    View All {{ $allCourses->count() }} Courses →
                </a>
            </div>
            @endif
        </div>
    </section>

    {{-- ============================================ HOW IT WORKS ============================================ --}}
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <p class="text-sm font-semibold uppercase tracking-wider text-secondary-dark mb-2">Simple</p>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">How It Works</h2>
                <p class="text-lg text-gray-600">Three simple steps to start your learning journey.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['1', 'Choose Your Course', 'Browse our catalog and pick the course that matches your career goals. Compare learning modes and prices.'],
                    ['2', 'Enroll & Pay Securely', 'Create your account and pay securely online. Your enrollment is instant — start learning immediately.'],
                    ['3', 'Learn & Get Certified', 'Access video lessons, hands-on projects, and earn your verifiable certificate at your own pace.'],
                ] as $step)
                <div class="text-center group">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-accent to-secondary flex items-center justify-center mx-auto mb-6 shadow-lg shadow-accent/30 group-hover:scale-105 transition-transform">
                        <span class="text-3xl font-black text-white">{{ $step[0] }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $step[1] }}</h3>
                    <p class="text-gray-600">{{ $step[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================ LEARNING MODES ============================================ --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Choose Your Learning Style</h2>
                <p class="text-lg text-gray-600">Flexibility to learn the way that works best for you.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all text-center border border-gray-100">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">In-Class Learning</h3>
                    <p class="text-gray-600 text-sm mb-3">Attend physical classes at our training center. Direct interaction with instructors and peers.</p>
                    <span class="text-sm font-semibold text-primary">Includes all course materials</span>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-xl transition-all text-center border-2 border-accent relative md:-translate-y-2">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-accent to-secondary text-white text-xs font-bold px-4 py-1 rounded-full shadow">MOST POPULAR</span>
                    <div class="w-16 h-16 bg-gradient-to-br from-accent to-secondary rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Live Online</h3>
                    <p class="text-gray-600 text-sm mb-3">Join live classes from anywhere. Real-time interaction with instructors via video conferencing.</p>
                    <span class="text-sm font-semibold text-accent-dark">Learn from anywhere</span>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition-all text-center border border-gray-100">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Self-Paced</h3>
                    <p class="text-gray-600 text-sm mb-3">Learn at your own speed. Access all course materials anytime, anywhere. Best value.</p>
                    <span class="text-sm font-semibold text-success">Best Value</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ STATS ============================================ --}}
    <section class="py-16 bg-gradient-to-br from-primary via-accent-dark to-secondary-dark">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl md:text-5xl font-black text-white mb-2">{{ $allCourses->count() }}+</div>
                    <div class="text-secondary-light text-sm">Courses Available</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-black text-white mb-2">3</div>
                    <div class="text-secondary-light text-sm">Learning Modes</div>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2">24/7</div>
                    <div class="text-secondary-light text-sm">Access to Materials</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ FINAL CTA ============================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-3xl mx-auto text-center px-4">
            <h2 class="text-3xl md:text-4xl font-black text-gray-900 mb-4">Ready to Transform Your Career?</h2>
            <p class="text-lg text-gray-600 mb-8">Join SACS Computers today and gain the skills employers are looking for.</p>
            @auth
            <a href="{{ route('courses.catalog') }}" class="inline-block bg-gradient-to-r from-accent to-secondary text-white px-10 py-4 rounded-xl font-bold text-lg hover:-translate-y-0.5 transition-all shadow-xl shadow-accent/30">Browse Courses</a>
            @else
            <a href="{{ route('register') }}" class="inline-block bg-gradient-to-r from-accent to-secondary text-white px-10 py-4 rounded-xl font-bold text-lg hover:-translate-y-0.5 transition-all shadow-xl shadow-accent/30">Get Started Free — It Takes 30 Seconds</a>
            @endauth
        </div>
    </section>

    {{-- ============================================ FOOTER ============================================ --}}
    <footer class="bg-primary py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center gap-3 mb-6 md:mb-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent to-secondary flex items-center justify-center">
                        <span class="text-white font-black">S</span>
                    </div>
                    <div>
                        <span class="text-white font-bold">SACS Computers</span>
                        <span class="text-gray-400 text-sm block">Learning Platform</span>
                    </div>
                </div>
                <div class="flex gap-6 text-gray-400 text-sm">
                    <a href="{{ route('courses.catalog') }}" class="hover:text-secondary-light transition-colors">Courses</a>
                    <a href="#how-it-works" class="hover:text-secondary-light transition-colors">How It Works</a>
                    <a href="#" class="hover:text-secondary-light transition-colors">Contact</a>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-white/10 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} SACS Computers. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>
