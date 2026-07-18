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
                <a href="{{ url('/') }}">
                    <div class="w-16 h-16 bg-[#1E3A5F] rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <span class="text-[#3B82F6] font-bold text-2xl">S</span>
                    </div>
                    <h2 class="text-xl font-bold text-[#1E3A5F]">SACS Computers</h2>
                    <p class="text-sm text-gray-500">Learning Platform</p>
                </a>
            </div>

            {{-- Card --}}
            <div class="w-full max-w-md">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
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