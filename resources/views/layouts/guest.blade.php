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
        <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            {{-- Logo --}}
            <div class="mb-8 text-center">
                <a href="{{ url('/') }}" class="inline-block">
                    <img src="{{ asset('images/logo.png') }}" alt="SACS Computers" class="w-16 h-16 rounded-xl object-contain mx-auto mb-3">

                    <h2 class="text-xl font-bold text-primary">SACS Computers</h2>
                    <p class="text-sm text-gray-500">Learning Platform</p>
                </a>
            </div>

            {{-- Card --}}
            <div class="w-full max-w-md">
                <div class="bg-white rounded-xl shadow-card border border-gray-200 p-8">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer --}}
            <p class="mt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} SACS Computers. All rights reserved.
            </p>
        </div>
    </body>
</html>