<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SACS Computers') }} — Learning Platform</title>
        <link rel="icon" type="image/png" href="https://placehold.co/32x32/1E3A5F/FFFFFF?text=S">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen flex flex-col">
            {{-- Navigation --}}
            <nav class="sticky top-0 z-50 bg-primary/95 backdrop-blur border-b border-white/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        {{-- Logo & Brand --}}
                        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center group-hover:scale-105 transition-transform">
                                <span class="text-white font-bold text-lg">S</span>
                            </div>
                            <div class="leading-tight">
                                <span class="text-white font-bold text-xl">SACS</span>
                                <span class="text-secondary-light text-xs block -mt-1 tracking-wide">Computers</span>
                            </div>
                        </a>

                        {{-- Navigation Links --}}
                        <div class="flex items-center gap-1 sm:gap-2">
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="text-secondary-light hover:text-white px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-colors">
                                        Admin Panel
                                    </a>
                                @endif
                                <a href="{{ route('courses.catalog') }}" class="hidden sm:inline-flex text-gray-300 hover:text-white px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-colors">
                                    Browse
                                </a>
                                <a href="{{ route('student.courses') }}" class="text-gray-100 hover:text-white px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-colors">
                                    My Courses
                                </a>

                                {{-- User Dropdown --}}
                                <div class="relative ml-1" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center gap-2 text-white rounded-full pl-1 pr-2 py-1 hover:bg-white/10 transition-colors">
                                        <span class="w-8 h-8 rounded-full bg-accent flex items-center justify-center text-sm font-bold">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                        <span class="text-sm hidden sm:block">{{ auth()->user()->name }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-card-hover py-1 z-50 border border-gray-100">
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-danger hover:bg-danger-light">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('courses.catalog') }}" class="hidden sm:inline-flex text-gray-300 hover:text-white px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-colors">
                                    Courses
                                </a>
                                <a href="{{ route('login') }}" class="text-gray-100 hover:text-white px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/10 transition-colors">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="bg-accent text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-accent-dark transition-colors">
                                    Get Started
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            {{-- Page Heading --}}
            @if (isset($header))
                <header class="bg-white shadow-sm border-b border-gray-100">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- Flash Messages --}}
            @if(session('success') || session('error') || session('info'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
                     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
                    @if(session('success'))
                        <div class="bg-white border-l-4 border-success shadow-card text-gray-700 px-4 py-3 rounded-xl mb-2 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-success-light text-success-dark flex items-center justify-center mr-3">✓</span>
                            {{ session('success') }}
                            <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-white border-l-4 border-danger shadow-card text-gray-700 px-4 py-3 rounded-xl mb-2 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-danger-light text-danger flex items-center justify-center mr-3">!</span>
                            {{ session('error') }}
                            <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="bg-white border-l-4 border-accent shadow-card text-gray-700 px-4 py-3 rounded-xl mb-2 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-accent/10 text-accent flex items-center justify-center mr-3">i</span>
                            {{ session('info') }}
                            <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-primary text-white mt-16">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-8 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center">
                                    <span class="text-white font-bold">S</span>
                                </div>
                                <div class="leading-tight">
                                    <span class="text-white font-bold text-lg">SACS Computers</span>
                                    <span class="text-secondary-light text-xs block -mt-0.5">Learning Platform</span>
                                </div>
                            </div>
                            <p class="text-gray-400 text-sm max-w-sm">Master in-demand tech skills — in-class, live online, or self-paced — and earn a verifiable certificate.</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-white mb-3">Explore</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li><a href="{{ route('courses.catalog') }}" class="hover:text-secondary-light transition-colors">All Courses</a></li>
                                <li><a href="{{ url('/') }}" class="hover:text-secondary-light transition-colors">Home</a></li>
                                @auth<li><a href="{{ route('student.courses') }}" class="hover:text-secondary-light transition-colors">My Courses</a></li>@endauth
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-white mb-3">Learning modes</h4>
                            <ul class="space-y-2 text-sm text-gray-400">
                                <li>In-Class</li>
                                <li>Live Online</li>
                                <li>Self-Paced</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-10 pt-6 border-t border-white/10 text-center text-gray-500 text-sm">
                        &copy; {{ date('Y') }} SACS Computers. All rights reserved.
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
