<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SACS Computers') }} — {{ $title ?? 'Login' }}</title>
        <link rel="icon" type="image/png" href="https://placehold.co/32x32/1E3A5F/FFFFFF?text=S">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative min-h-screen flex flex-col items-center justify-center bg-brand-gradient py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
            {{-- Ambient glow --}}
            <div class="pointer-events-none absolute inset-0 opacity-30">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-secondary rounded-full blur-3xl"></div>
            </div>

            {{-- Logo --}}
            <div class="relative mb-8 text-center">
                <a href="{{ url('/') }}" class="inline-block">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center mx-auto mb-3 shadow-glow">
                        <span class="text-white font-black text-2xl">S</span>
                    </div>
                    <h2 class="text-xl font-extrabold text-white">SACS Computers</h2>
                    <p class="text-sm text-secondary-light">Learning Platform</p>
                </a>
            </div>

            {{-- Card --}}
            <div class="relative w-full max-w-md animate-fade-up">
                <div class="bg-white rounded-2xl shadow-card-hover border border-white/20 p-8">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer --}}
            <p class="relative mt-8 text-center text-sm text-white/70">
                &copy; {{ date('Y') }} SACS Computers. All rights reserved.
            </p>
        </div>
    </body>
</html>