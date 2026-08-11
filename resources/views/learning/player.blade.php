<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            <a href="{{ route('student.courses') }}" class="text-sm text-accent hover:text-accent-dark">
                ← My Courses
            </a>
        </div>
    </x-slot>

    <div class="flex h-[calc(100vh-8rem)]">

        {{-- Sidebar: Curriculum --}}
        <div class="w-80 bg-white border-r overflow-y-auto flex-shrink-0">
            {{-- Progress Header --}}
            <div class="p-4 border-b bg-gray-50">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Your Progress</span>
                    <span class="text-sm font-bold text-accent" id="progress-percent">{{ $progressPercent }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-accent h-2.5 rounded-full transition-all duration-300"
                        id="progress-bar"
                        style="width: {{ $progressPercent }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <span id="completed-count">{{ $completedCount }}</span>/<span id="total-count">{{ $course->lessons()->count() }}</span> lessons
                </p>
                <a href="{{ route('certificate.show', $course->slug) }}"
                    id="certificate-link"
                    class="mt-3 inline-flex items-center justify-center w-full bg-accent text-white text-sm font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition {{ $progressPercent >= 100 ? '' : 'hidden' }}">
                    🎓 Get your certificate
                </a>
                @if($enrollment->learning_type === 'sync')
                    <a href="{{ route('learning.sessions', $course->slug) }}"
                        class="mt-2 inline-flex items-center justify-center w-full border border-accent text-accent text-sm font-semibold px-4 py-2 rounded-lg hover:bg-accent hover:text-white transition">
                        📅 Live sessions
                    </a>
                @endif
            </div>

            {{-- Sections & Lessons --}}
            @foreach($course->sections as $section)
                @php
                    $isCurrentSection = isset($currentLesson) && $currentLesson->section_id === $section->id;
                @endphp
                <div class="border-b {{ $isCurrentSection ? 'bg-blue-50/50' : '' }}">
                    <button 
                        class="w-full text-left px-4 py-3 font-medium flex justify-between items-center transition-colors
                               {{ $isCurrentSection ? 'text-accent bg-blue-50' : 'text-gray-900 hover:bg-gray-50' }}"
                        onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-90')">
                        <span class="text-sm flex items-center">
                            @if($isCurrentSection)
                                <span class="w-2 h-2 bg-accent rounded-full mr-2 animate-pulse"></span>
                            @endif
                            {{ $section->title }}
                        </span>
                        <svg class="w-4 h-4 transition-transform {{ $isCurrentSection ? '' : 'rotate-90' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <div class="bg-gray-50 {{ $isCurrentSection ? '' : 'hidden' }}">
                        @foreach($section->lessons as $sectionLesson)
                            <a href="{{ route('learning.lesson', [$course->slug, $sectionLesson->id]) }}"
                                class="flex items-center px-6 py-3 text-sm hover:bg-gray-100 border-l-2 transition-colors
                                              {{ isset($currentLesson) && $currentLesson->id === $sectionLesson->id ? 'border-accent bg-blue-50 text-accent font-medium' : 'border-transparent text-gray-700' }}">

                                {{-- Completion Check --}}
                                <span class="mr-3 flex-shrink-0">
                                    @if(in_array($sectionLesson->id, $completedLessonIds))
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    @else
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-full"></div>
                                    @endif
                                </span>

                                {{-- Lesson Type Icon --}}
                                <span class="mr-2 flex-shrink-0">
                                    @if($sectionLesson->content_type === 'video')
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    </svg>
                                    @elseif($sectionLesson->content_type === 'text')
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    @endif
                                </span>

                                <span class="truncate">{{ $sectionLesson->title }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1 overflow-y-auto bg-gray-100">
            
            {{-- Tabs: Only show for sync students --}}
            @if($enrollment->learning_type === 'sync')
                <div class="bg-white border-b">
                    <div class="max-w-4xl mx-auto px-6">
                        <nav class="flex space-x-8">
                            <button onclick="switchTab('content')" id="tab-content-btn" 
                                    class="py-3 px-1 border-b-2 border-accent text-accent font-medium text-sm transition-colors">
                                📖 Course Content
                            </button>
                            <button onclick="switchTab('sessions')" id="tab-sessions-btn"
                                    class="py-3 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors">
                                🔴 Live Sessions
                            </button>
                        </nav>
                    </div>
                </div>
            @endif
            <div id="tab-content" class="{{ $enrollment->learning_type === 'sync' ? '' : '' }}">
                @if(isset($currentLesson))
                <div class="max-w-4xl mx-auto py-8 px-6">

                    {{-- Section Indicator + Lesson Title --}}
                    <div class="mb-6">
                        {{-- Section Breadcrumb --}}
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Section {{ $currentLesson->section->order }}: {{ $currentLesson->section->title }}</span>
                        </div>
                        
                        {{-- Lesson Title --}}
                        <h1 class="text-2xl font-bold text-gray-900">{{ $currentLesson->title }}</h1>
                    </div>

                    @php
                        $contentSrc = \Illuminate\Support\Str::startsWith($currentLesson->content_path, 'http')
                            ? $currentLesson->content_path
                            : route('lesson.content', [$course->slug, $currentLesson->id]);
                    @endphp

                    {{-- Video Content (download button hidden; right-click disabled) --}}
                    @if($currentLesson->content_type === 'video')
                    <div class="bg-black rounded-lg overflow-hidden mb-6">
                        <video controls controlsList="nodownload noplaybackrate" disablePictureInPicture
                               oncontextmenu="return false" class="w-full" style="max-height: 500px;">
                            <source src="{{ $contentSrc }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    @endif

                    {{-- Image Content --}}
                    @if($currentLesson->content_type === 'image')
                    <div class="bg-white rounded-lg overflow-hidden mb-6 shadow-sm">
                        <img src="{{ $contentSrc }}" alt="{{ $currentLesson->title }}" class="w-full"
                             oncontextmenu="return false">
                    </div>
                    @endif

                    {{-- PDF Content (rendered inline; viewer toolbar/download hidden) --}}
                    @if($currentLesson->content_type === 'pdf')
                    <div class="bg-gray-100 rounded-lg overflow-hidden mb-6 shadow-sm" style="height: 80vh;">
                        <iframe src="{{ $contentSrc }}#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                                class="w-full h-full" style="border: 0;" oncontextmenu="return false"
                                title="{{ $currentLesson->title }}"></iframe>
                    </div>
                    @endif

                    {{-- Text/Body Content --}}
                    @if($currentLesson->content_body)
                    <div class="bg-white rounded-lg p-6 shadow-sm prose max-w-none mb-6">
                        {!! $currentLesson->content_body !!}
                    </div>
                    @endif

                    {{-- Mark Complete Button --}}
                    @php
                        $isCurrentCompleted = in_array($currentLesson->id, $completedLessonIds);
                        $currentLocked = ! $isCurrentCompleted && ($secondsSpent < $requiredSeconds);
                    @endphp
                    <div class="flex flex-col items-center mb-8">
                        <button id="mark-complete-btn"
                            data-course-slug="{{ $course->slug }}"
                            data-lesson-id="{{ $currentLesson->id }}"
                            data-required-seconds="{{ $requiredSeconds }}"
                            data-spent-seconds="{{ $secondsSpent }}"
                            data-heartbeat-interval="{{ $heartbeatInterval }}"
                            data-completed="{{ $isCurrentCompleted ? '1' : '0' }}"
                            @if($currentLocked) disabled @endif
                            class="px-8 py-3 rounded-xl font-semibold text-lg transition-all duration-200
                                           {{ $isCurrentCompleted
                                              ? 'bg-success text-white hover:bg-success-dark'
                                              : ($currentLocked
                                                 ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                                 : 'bg-white border-2 border-accent text-accent hover:bg-accent hover:text-white') }}">
                            @if($isCurrentCompleted)
                            ✓ Completed — Click to Undo
                            @else
                            Mark as Complete
                            @endif
                        </button>
                        <p id="lesson-timer-hint" class="text-sm text-gray-500 mt-3 {{ $isCurrentCompleted ? 'hidden' : '' }}"></p>
                    </div>

                    {{-- Previous / Next Navigation --}}
                    <div class="flex justify-between items-center pb-12">
                        @if($prevLesson)
                        <a href="{{ route('learning.lesson', [$course->slug, $prevLesson->id]) }}"
                            class="flex items-center px-6 py-3 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous Lesson
                        </a>
                        @else
                        <div></div>
                        @endif

                        @if($nextLesson)
                        <a href="{{ route('learning.lesson', [$course->slug, $nextLesson->id]) }}"
                            class="flex items-center px-6 py-3 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors">
                            Next Lesson
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        @else
                        <div></div>
                        @endif
                    </div>
                </div>
                @else
                {{-- Welcome Screen --}}
                <div class="max-w-2xl mx-auto py-16 px-6 text-center">
                    <div class="bg-white rounded-lg p-12 shadow-sm">
                        <svg class="w-20 h-20 text-accent mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Welcome to {{ $course->title }}</h2>
                        <p class="text-gray-600 mb-8">Select a lesson from the sidebar to start learning.</p>

                        @php
                        $firstLesson = $course->sections->first()?->lessons->first();
                        @endphp
                        @if($firstLesson)
                        <a href="{{ route('learning.lesson', [$course->slug, $firstLesson->id]) }}"
                            class="inline-block bg-accent text-white px-8 py-3 rounded-lg font-semibold hover:bg-accent-dark transition-colors">
                            Start First Lesson →
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Sessions Tab Panel (only for sync students) --}}
            @if($enrollment->learning_type === 'sync')
                <div id="tab-sessions" class="hidden">
                    <div class="max-w-4xl mx-auto py-8 px-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-8">Live Sessions</h1>
                        
                        @php
                            $sessions = $course->sessions()->orderBy('session_date')->orderBy('start_time')->get();
                            $upcomingSessions = $sessions->where('status', 'upcoming')->where('session_date', '>=', now()->toDateString());
                            $pastSessions = $sessions->filter(function($session) {
                                return $session->status === 'completed' || $session->session_date < now()->toDateString();
                            });
                        @endphp

                        {{-- Upcoming Sessions --}}
                        <div class="mb-8">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Upcoming Sessions
                            </h2>
                            @if($upcomingSessions->isEmpty())
                                <div class="bg-white rounded-lg p-8 text-center text-gray-500 shadow-sm">
                                    <p>No upcoming sessions scheduled yet.</p>
                                    <p class="text-sm mt-1">Check back soon for live class dates.</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($upcomingSessions as $session)
                                        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $session->title }}</h3>
                                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            {{ $session->session_date->format('l, F j, Y') }}
                                                        </span>
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            {{ date('h:i A', strtotime($session->start_time)) }} - {{ date('h:i A', strtotime($session->end_time)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($session->meeting_link)
                                                    <a href="{{ $session->meeting_link }}" target="_blank" 
                                                       class="bg-accent text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-accent-dark transition-colors flex items-center">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Join Meeting
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Past Sessions --}}
                        @if($pastSessions->isNotEmpty())
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Past Sessions</h2>
                                <div class="space-y-3">
                                    @foreach($pastSessions as $session)
                                        <div class="bg-white rounded-lg shadow-sm p-5 opacity-75">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="font-medium text-gray-900">{{ $session->title }}</h3>
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        {{ $session->session_date->format('M d, Y') }} • {{ date('h:i A', strtotime($session->start_time)) }}
                                                    </p>
                                                </div>
                                                @if($session->recording_link)
                                                    <a href="{{ $session->recording_link }}" target="_blank"
                                                       class="text-accent hover:text-accent-dark text-sm font-medium flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Watch Recording
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Mark Complete AJAX Script --}}
   <script>
    function switchTab(tab) {
        const contentTab = document.getElementById('tab-content');
        const sessionsTab = document.getElementById('tab-sessions');
        const contentBtn = document.getElementById('tab-content-btn');
        const sessionsBtn = document.getElementById('tab-sessions-btn');
        
        if (tab === 'content') {
            contentTab.classList.remove('hidden');
            sessionsTab.classList.add('hidden');
            contentBtn.classList.add('border-accent', 'text-accent');
            contentBtn.classList.remove('border-transparent', 'text-gray-500');
            sessionsBtn.classList.add('border-transparent', 'text-gray-500');
            sessionsBtn.classList.remove('border-accent', 'text-accent');
        } else {
            contentTab.classList.add('hidden');
            sessionsTab.classList.remove('hidden');
            sessionsBtn.classList.add('border-accent', 'text-accent');
            sessionsBtn.classList.remove('border-transparent', 'text-gray-500');
            contentBtn.classList.add('border-transparent', 'text-gray-500');
            contentBtn.classList.remove('border-accent', 'text-accent');
        }
    }

    // Mark Complete Button + time-on-lesson gating
    document.addEventListener('DOMContentLoaded', function() {
        const markBtn = document.getElementById('mark-complete-btn');
        if (!markBtn) return;

        const hint = document.getElementById('lesson-timer-hint');
        const courseSlug = markBtn.dataset.courseSlug;
        const lessonId = markBtn.dataset.lessonId;
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        const required = parseInt(markBtn.dataset.requiredSeconds, 10) || 0;
        const interval = (parseInt(markBtn.dataset.heartbeatInterval, 10) || 15) * 1000;
        let spent = parseInt(markBtn.dataset.spentSeconds, 10) || 0; // server-confirmed
        let display = spent;                                         // on-screen, ticks each second
        let completed = markBtn.dataset.completed === '1';
        let confirming = false;

        const lockedClasses = 'px-8 py-3 rounded-xl font-semibold text-lg transition-all duration-200 bg-gray-200 text-gray-500 cursor-not-allowed';
        const readyClasses = 'px-8 py-3 rounded-xl font-semibold text-lg transition-all duration-200 bg-white border-2 border-accent text-accent hover:bg-accent hover:text-white';
        const doneClasses = 'px-8 py-3 rounded-xl font-semibold text-lg transition-all duration-200 bg-success text-white hover:bg-success-dark';

        const ready = () => spent >= required;

        function refreshGate() {
            if (completed) {
                markBtn.disabled = false;
                markBtn.className = doneClasses;
                markBtn.textContent = '✓ Completed — Click to Undo';
                if (hint) hint.classList.add('hidden');
                return;
            }
            if (hint) hint.classList.remove('hidden');
            if (ready()) {
                markBtn.disabled = false;
                markBtn.className = readyClasses;
                markBtn.textContent = 'Mark as Complete';
                if (hint) hint.textContent = 'You can now mark this lesson complete.';
            } else {
                markBtn.disabled = true;
                markBtn.className = lockedClasses;
                markBtn.textContent = 'Mark as Complete';
                const left = Math.max(required - display, 0);
                if (hint) hint.textContent = `Keep watching — ${left}s of active time left on this lesson.`;
            }
        }

        // Report active time to the server; it credits only real elapsed time.
        function heartbeat() {
            if (completed || ready() || document.visibilityState !== 'visible') { confirming = false; return; }
            fetch(`/learn/${courseSlug}/lesson/${lessonId}/heartbeat`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            })
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                confirming = false;
                if (!data) return;
                spent = data.secondsSpent;
                if (spent > display) display = spent;
                refreshGate();
            })
            .catch(() => { confirming = false; });
        }

        // Tick the visible countdown every second so it feels live.
        function tick() {
            if (completed || ready() || document.visibilityState !== 'visible') return;
            if (display < required) {
                display++;
                refreshGate();
            }
            // When the countdown reaches zero, confirm with the server right
            // away (don't wait for the next interval) so the button unlocks.
            if (display >= required && !confirming) {
                confirming = true;
                heartbeat();
            }
        }

        refreshGate();
        setInterval(heartbeat, interval);
        setInterval(tick, 1000);

        markBtn.addEventListener('click', function() {
            if (markBtn.disabled) return;
            const url = `/learn/${courseSlug}/lesson/${lessonId}/complete`;

            markBtn.disabled = true;
            markBtn.style.opacity = '0.7';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(async response => {
                if (response.status === 422) {
                    const err = await response.json();
                    spent = err.secondsSpent ?? spent;
                    markBtn.style.opacity = '1';
                    refreshGate();
                    if (hint) hint.textContent = err.error;
                    return null;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;

                completed = !!data.completed;
                markBtn.style.opacity = '1';
                refreshGate();

                const progressBar = document.getElementById('progress-bar');
                const progressPercent = document.getElementById('progress-percent');
                const completedCount = document.getElementById('completed-count');

                if (progressBar) progressBar.style.width = data.progressPercent + '%';
                if (progressPercent) progressPercent.textContent = data.progressPercent + '%';
                if (completedCount) completedCount.textContent = data.completedCount;

                const certificateLink = document.getElementById('certificate-link');
                if (certificateLink) certificateLink.classList.toggle('hidden', data.progressPercent < 100);

                const sidebarLink = document.querySelector(`a[href*="/lesson/${lessonId}"]`);
                if (sidebarLink) {
                    const checkmarkSpan = sidebarLink.querySelector('span.flex-shrink-0');
                    if (checkmarkSpan) {
                        if (data.completed) {
                            checkmarkSpan.innerHTML = `<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`;
                        } else {
                            checkmarkSpan.innerHTML = `<div class="w-5 h-5 border-2 border-gray-300 rounded-full"></div>`;
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                markBtn.style.opacity = '1';
                refreshGate();
            });
        });
    });
</script>
</x-app-layout>