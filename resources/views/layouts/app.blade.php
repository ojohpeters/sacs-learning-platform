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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            {{-- Navigation --}}
            <nav class="bg-primary border-b border-primary-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        {{-- Logo & Brand --}}
                        <div class="flex items-center">
                            <a href="{{ url('/') }}" class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">S</span>
                                </div>
                                <div>
                                    <span class="text-white font-bold text-xl">SACS</span>
                                    <span class="text-accent-light text-sm block -mt-1">Computers</span>
                                </div>
                            </a>
                        </div>

                        {{-- Navigation Links --}}
                        <div class="flex items-center space-x-4">
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="text-accent-light hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                                        Admin Panel
                                    </a>
                                @endif
                                <a href="{{ route('student.courses') }}" class="text-accent-light hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                                    My Courses
                                </a>
                                
                                {{-- User Dropdown --}}
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center text-white hover:text-accent-light transition-colors px-3 py-2">
                                        <span class="text-sm mr-2">{{ auth()->user()->name }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-accent-light hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="bg-accent text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-accent-dark transition-colors">
                                    Get Started
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            {{-- Page Heading --}}
            @if (isset($header))
                <header class="bg-white shadow-sm border-b">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            {{-- Flash Messages --}}
            @if(session('success') || session('error') || session('info'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    @if(session('success'))
                        <div class="bg-success-light border border-success text-success-dark px-4 py-3 rounded-lg mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-danger-light border border-danger text-danger-dark px-4 py-3 rounded-lg mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="bg-accent-light border border-accent text-accent-dark px-4 py-3 rounded-lg mb-4">
                            {{ session('info') }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- Page Content --}}
            <main>
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-primary text-white mt-16">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex items-center space-x-2 mb-4 md:mb-0">
                            <div class="w-8 h-8 bg-accent rounded flex items-center justify-center">
                                <span class="text-white font-bold text-sm">S</span>
                            </div>
                            <span class="text-accent-light">SACS Computers Learning Platform</span>
                        </div>
                        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} SACS Computers. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>