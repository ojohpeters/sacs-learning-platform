<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SACS Computers — Master Tech Skills That Pay</title>
    <link rel="icon" type="image/png" href="https://placehold.co/32x32/1E3A5F/FFFFFF?text=S">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        body { font-family: 'Inter', system-ui, sans-serif; }
        /* Subtle, tasteful scroll reveal */
        [data-reveal] { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; }
        [data-reveal].in { opacity: 1; transform: none; }
    </style>
</head>

<body class="antialiased bg-white text-gray-900">

    {{-- ============================================ NAVBAR ============================================ --}}
    <nav id="nav" class="bg-white/90 backdrop-blur sticky top-0 z-50 border-b border-gray-200 transition-shadow">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="SACS Computers" class="w-9 h-9 rounded-lg object-contain">

                    <div class="leading-none">
                        <span class="text-primary font-bold text-lg">SACS</span>
                        <span class="text-gray-500 text-[11px] block mt-0.5 tracking-wide uppercase">Computers</span>
                    </div>
                </a>
                <div class="flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('courses.catalog') }}" class="hidden sm:inline-flex text-sm font-medium text-gray-600 hover:text-primary px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">Courses</a>
                    <a href="#how-it-works" class="hidden sm:inline-flex text-sm font-medium text-gray-600 hover:text-primary px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">How it works</a>
                    @auth
                    <a href="{{ route('student.courses') }}" class="bg-accent text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-accent-dark transition-colors">My Courses</a>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-primary px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">Log in</a>
                    <a href="{{ route('register') }}" class="bg-accent text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-accent-dark transition-colors">Get Started</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ============================================ HERO ============================================ --}}
    <section class="bg-primary text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-20 md:py-28 text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-accent-light mb-5" data-reveal>SACS Computers · Learning Platform</p>
            <h1 class="text-4xl md:text-5xl font-bold leading-[1.1] tracking-tight mb-6" data-reveal>
                Master tech skills that <span class="text-accent-light">get you hired</span>
            </h1>
            <p class="text-lg text-gray-300 leading-relaxed max-w-2xl mx-auto mb-10" data-reveal>
                Learn Web Development, Cybersecurity, Data Science and more — in-class, live online, or self-paced —
                with hands-on projects and a verifiable certificate.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-3" data-reveal>
                <a href="#courses" class="bg-accent text-white px-7 py-3.5 rounded-lg font-semibold hover:bg-accent-dark transition-colors">Explore Courses</a>
                <a href="#how-it-works" class="border border-white/25 text-white px-7 py-3.5 rounded-lg font-semibold hover:bg-white/10 transition-colors">How It Works</a>
            </div>
            <div class="mt-12 flex flex-wrap justify-center gap-x-8 gap-y-2 text-sm text-gray-400" data-reveal>
                <span>Certificate awarded</span>
                <span class="text-gray-600">·</span>
                <span>Hands-on projects</span>
                <span class="text-gray-600">·</span>
                <span>Expert instructors</span>
            </div>
        </div>
    </section>

    {{-- ============================================ FEATURED COURSES ============================================ --}}
    <section id="courses" class="py-20 bg-gray-50 border-t border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="max-w-2xl mb-12" data-reveal>
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-dark mb-2">Our catalog</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Courses we offer</h2>
                <p class="text-gray-600">Industry-relevant courses designed to take you from beginner to job-ready professional.</p>
            </div>

            @if($featuredCourses->isEmpty())
            <div class="text-center py-12 text-gray-500">Courses coming soon. Check back shortly!</div>
            @else
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($featuredCourses as $course)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col" data-reveal>
                    <div class="h-40 relative overflow-hidden bg-primary">
                        @if($course->thumbnail_path)
                        <img src="{{ Str::startsWith($course->thumbnail_path, 'http') ? $course->thumbnail_path : asset('storage/' . $course->thumbnail_path) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                        <div class="flex items-center justify-center h-full">
                            <span class="text-white/70 font-bold text-2xl">{{ Str::of($course->title)->explode(' ')->take(2)->map(fn($w) => Str::substr($w,0,1))->implode('') }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1.5">{{ $course->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $course->short_description }}</p>
                        <div class="space-y-1.5 mb-5 mt-auto text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">In-Class</span><span class="font-medium text-gray-900">₦{{ number_format($course->price + 4000) }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Live Online</span><span class="font-medium text-gray-900">₦{{ number_format($course->price - 10000 + 4000) }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Self-Paced</span><span class="font-semibold text-secondary-dark">₦{{ number_format($course->price - 25000 + 4000) }}</span></div>
                        </div>
                        <a href="{{ route('courses.show', $course->slug) }}" class="block w-full text-center bg-primary text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-700 transition-colors">View Course</a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($allCourses->count() > 3)
            <div class="mt-10" data-reveal>
                <a href="{{ route('courses.catalog') }}" class="inline-flex items-center gap-1.5 text-accent-dark hover:text-primary font-semibold text-sm">
                    View all {{ $allCourses->count() }} courses
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @endif
        </div>
    </section>

    {{-- ============================================ HOW IT WORKS ============================================ --}}
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="max-w-2xl mb-12" data-reveal>
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-dark mb-2">Simple</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">How it works</h2>
                <p class="text-gray-600">Three steps to start your learning journey.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['01', 'Choose your course', 'Browse the catalog and pick the course that matches your goals. Compare learning modes and prices.'],
                    ['02', 'Enroll & pay securely', 'Create your account and pay securely online. Enrollment is instant — start learning right away.'],
                    ['03', 'Learn & get certified', 'Work through video lessons and projects, then earn your verifiable certificate.'],
                ] as $step)
                <div data-reveal>
                    <div class="w-11 h-11 rounded-lg bg-accent/10 text-accent-dark flex items-center justify-center font-bold mb-4">{{ $step[0] }}</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1.5">{{ $step[1] }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $step[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================ LEARNING MODES ============================================ --}}
    <section class="py-20 bg-gray-50 border-t border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="max-w-2xl mb-12" data-reveal>
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-dark mb-2">Flexible</p>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Choose your learning style</h2>
                <p class="text-gray-600">Learn the way that works best for you.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['In-Class Learning', 'Attend physical classes at our training center with direct instructor and peer interaction.', 'Includes all materials', false],
                    ['Live Online', 'Join live classes from anywhere with real-time instructor interaction via video.', 'Most popular', true],
                    ['Self-Paced', 'Learn at your own speed with anytime access to all course materials. Best value.', 'Best value', false],
                ] as $mode)
                <div class="bg-white rounded-xl border {{ $mode[3] ? 'border-accent ring-1 ring-accent/20' : 'border-gray-200' }} p-6 shadow-sm" data-reveal>
                    <div class="flex items-center gap-2 mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $mode[0] }}</h3>
                        @if($mode[3])<span class="text-[11px] font-semibold uppercase tracking-wide text-accent-dark bg-accent/10 px-2 py-0.5 rounded-full">Popular</span>@endif
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ $mode[1] }}</p>
                    <span class="text-sm font-medium {{ $mode[3] ? 'text-accent-dark' : 'text-gray-500' }}">{{ $mode[2] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================ STATS ============================================ --}}
    <section class="py-16 bg-primary text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 grid grid-cols-2 md:grid-cols-3 gap-8 text-center">
            <div data-reveal><div class="text-4xl font-bold mb-1">{{ $allCourses->count() }}+</div><div class="text-gray-400 text-sm">Courses available</div></div>
            <div data-reveal><div class="text-4xl font-bold mb-1">3</div><div class="text-gray-400 text-sm">Learning modes</div></div>
            <div class="col-span-2 md:col-span-1" data-reveal><div class="text-4xl font-bold mb-1">24/7</div><div class="text-gray-400 text-sm">Access to materials</div></div>
        </div>
    </section>

    {{-- ============================================ FINAL CTA ============================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-2xl mx-auto text-center px-4 sm:px-6" data-reveal>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">Ready to transform your career?</h2>
            <p class="text-gray-600 mb-8">Join SACS Computers today and gain the skills employers are looking for.</p>
            @auth
            <a href="{{ route('courses.catalog') }}" class="inline-block bg-accent text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-accent-dark transition-colors">Browse Courses</a>
            @else
            <a href="{{ route('register') }}" class="inline-block bg-accent text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-accent-dark transition-colors">Get Started — it takes 30 seconds</a>
            @endauth
        </div>
    </section>

    {{-- ============================================ FOOTER ============================================ --}}
    <footer class="bg-primary text-gray-400 border-t border-white/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="SACS Computers" class="w-9 h-9 rounded-lg object-contain bg-white">

                    <div class="leading-none">
                        <span class="text-white font-bold">SACS Computers</span>
                        <span class="text-gray-500 text-xs block mt-0.5">Learning Platform</span>
                    </div>
                </div>
                <div class="flex gap-6 text-sm">
                    <a href="{{ route('courses.catalog') }}" class="hover:text-white transition-colors">Courses</a>
                    <a href="#how-it-works" class="hover:text-white transition-colors">How it works</a>
                    <a href="#" class="hover:text-white transition-colors">Contact</a>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-white/10 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} SACS Computers. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Nav shadow on scroll
        const nav = document.getElementById('nav');
        addEventListener('scroll', () => nav.classList.toggle('shadow-sm', scrollY > 8));
        // Tasteful scroll reveal
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
        }, { threshold: 0.12 });
        document.querySelectorAll('[data-reveal]').forEach((el, i) => {
            el.style.transitionDelay = Math.min(i % 3, 2) * 80 + 'ms';
            io.observe(el);
        });
    </script>

</body>

</html>
