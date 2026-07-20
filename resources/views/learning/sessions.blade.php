<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Live Sessions — {{ $course->title }}
            </h2>
            <a href="{{ route('learning.course', $course->slug) }}"
                class="text-sm text-accent hover:underline">← Back to course</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-10">

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Upcoming --}}
            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Upcoming sessions</h3>

                @forelse($upcoming as $session)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-gray-900">{{ $session->title }}</h4>
                                    @if($session->status === 'live')
                                        <span class="inline-flex items-center text-xs font-semibold text-red-600">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-1 animate-pulse"></span>LIVE
                                        </span>
                                    @endif
                                </div>
                                @if($session->description)
                                    <p class="text-sm text-gray-600 mb-2">{{ $session->description }}</p>
                                @endif
                                <p class="text-sm text-gray-500">
                                    📅 {{ $session->session_date->format('l, M j, Y') }}
                                    &nbsp;·&nbsp;
                                    🕒 {{ \Illuminate\Support\Str::of($session->start_time)->before('.') }} –
                                    {{ \Illuminate\Support\Str::of($session->end_time)->before('.') }}
                                </p>
                            </div>
                            @if($session->meeting_link)
                                <a href="{{ $session->meeting_link }}" target="_blank" rel="noopener"
                                    class="shrink-0 bg-accent text-white text-sm font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition">
                                    Join
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No upcoming sessions scheduled yet. Check back soon.</p>
                @endforelse
            </section>

            {{-- Past --}}
            <section>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Past sessions</h3>

                @forelse($past as $session)
                    <div class="bg-gray-50 rounded-xl border border-gray-100 p-5 mb-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $session->title }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    📅 {{ $session->session_date->format('l, M j, Y') }}
                                </p>
                            </div>
                            @if($session->recording_link)
                                <a href="{{ $session->recording_link }}" target="_blank" rel="noopener"
                                    class="shrink-0 border border-accent text-accent text-sm font-semibold px-4 py-2 rounded-lg hover:bg-accent hover:text-white transition">
                                    ▶ Watch recording
                                </a>
                            @else
                                <span class="shrink-0 text-xs text-gray-400 self-center">No recording</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No past sessions.</p>
                @endforelse
            </section>

        </div>
    </div>
</x-app-layout>
