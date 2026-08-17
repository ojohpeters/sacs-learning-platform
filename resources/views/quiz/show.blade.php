<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $quiz->title }}
            </h2>
            <a href="{{ route('learning.course', $course->slug) }}" class="text-sm text-accent hover:text-accent-dark">
                ← Back to Course
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Quiz Info Card --}}
            <div class="bg-primary rounded-xl shadow-lg p-6 mb-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-accent-light text-sm font-medium uppercase tracking-wide mb-1">
                            {{ $quiz->type === 'final_exam' ? 'Final Exam' : 'Section Quiz' }}
                        </p>
                        <h1 class="text-2xl font-bold mb-2">{{ $quiz->title }}</h1>
                        @if($quiz->description)
                        <p class="text-gray-300 text-sm">{{ $quiz->description }}</p>
                        @endif
                    </div>
                    <div class="text-right space-y-1 text-sm">
                        <div class="bg-accent/20 rounded-lg px-4 py-2">
                            <p class="text-accent-light">Passing Score</p>
                            <p class="font-bold text-xl">{{ $quiz->passing_score }}%</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-6 pt-4 border-t border-primary-700">
                    <div class="text-center">
                        <p class="text-accent-light text-sm">Questions</p>
                        <p class="font-bold text-xl">{{ $questions->count() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-accent-light text-sm">Min Submit Time</p>
                        <p class="font-bold text-xl">{{ $quiz->min_submit_time }} min</p>
                    </div>
                    <div class="text-center">
                        <p class="text-accent-light text-sm">Time Limit</p>
                        <p class="font-bold text-xl">{{ $quiz->time_limit ? $quiz->time_limit . ' min' : 'None' }}</p>
                    </div>
                </div>
            </div>

            {{-- Timer Bar --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6 sticky top-4 z-10 border-l-4 border-orange-400">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Submit button unlocks in</p>
                        <p class="text-2xl font-bold text-orange-600" id="submit-timer">--:--</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Answered</p>
                        <p class="text-2xl font-bold text-accent" id="answered-count-display">0/{{ $questions->count() }}</p>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                    <div id="answered-progress" class="bg-accent h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            {{-- Questions --}}
            <form id="quiz-form" action="{{ route('quiz.submit', [$course->slug, $quiz->id, $attempt->id]) }}" method="POST"
                data-total-questions="{{ $questions->count() }}"
                data-min-submit-seconds="{{ (int)($quiz->min_submit_time * 60) }}"
                data-save-url="{{ route('quiz.save-answer', [$course->slug, $quiz->id, $attempt->id]) }}">

                @csrf

                @foreach($questions as $index => $question)
                <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
                    <div class="flex items-start mb-4">
                        <div class="w-8 h-8 bg-accent text-white rounded-lg flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <h3 class="font-semibold text-gray-900 text-lg leading-snug">
                            {{ $question->question_text }}
                        </h3>
                    </div>
                    <div class="space-y-2 ml-11">
                        @foreach($question->options as $option)
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-accent/5 hover:border-accent transition-colors
                                    {{ isset($existingAnswers[$question->id]) && $existingAnswers[$question->id] == $option->id ? 'border-accent bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio"
                                name="answers[{{ $question->id }}]"
                                value="{{ $option->id }}"
                                data-question-id="{{ $question->id }}"
                                class="quiz-option text-accent focus:ring-accent w-5 h-5"
                                {{ isset($existingAnswers[$question->id]) && $existingAnswers[$question->id] == $option->id ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                {{-- Submit Button --}}
                <div class="flex justify-end items-center space-x-4 mt-6 mb-12">
                    <p id="answered-count-text" class="text-sm text-gray-500"></p>
                    <button type="submit" id="submit-btn" disabled
                        class="bg-gray-200 text-gray-500 px-8 py-3 rounded-lg font-semibold cursor-not-allowed transition-colors">
                        Submit Quiz
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        // Track answered count
        function updateAnsweredCount() {
            const total = parseInt(document.getElementById('quiz-form').dataset.totalQuestions);
            const answered = document.querySelectorAll('.quiz-option:checked').length;
            const percent = total > 0 ? Math.round((answered / total) * 100) : 0;

            const countDisplay = document.getElementById('answered-count-display');
            const countText = document.getElementById('answered-count-text');
            const progressBar = document.getElementById('answered-progress');

            if (countDisplay) countDisplay.textContent = answered + '/' + total;
            if (countText) countText.textContent = answered + ' of ' + total + ' answered';
            if (progressBar) progressBar.style.width = percent + '%';
        }

        // Auto-save on answer
        document.querySelectorAll('.quiz-option').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.dataset.questionId;
                const optionId = this.value;
                const saveUrl = this.closest('form').dataset.saveUrl;

                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        option_id: optionId
                    })
                }).catch(() => {});

                updateAnsweredCount();
            });
        });

        // Submit timer
        (function() {
            const submitBtn = document.getElementById('submit-btn');
            const timerDisplay = document.getElementById('submit-timer');
            const form = document.getElementById('quiz-form');

            if (!submitBtn || !timerDisplay || !form) return;

            let remainingSeconds = parseInt(form.dataset.minSubmitSeconds);

            function formatTime(totalSeconds) {
                const mins = Math.floor(totalSeconds / 60);
                const secs = totalSeconds % 60;
                return mins + ':' + String(secs).padStart(2, '0');
            }

            function tick() {
                if (remainingSeconds <= 0) {
                    timerDisplay.textContent = 'Ready!';
                    submitBtn.disabled = false;
                    submitBtn.className = 'bg-accent text-white px-8 py-3 rounded-lg font-semibold hover:bg-accent-dark transition-colors';
                    return;
                }

                timerDisplay.textContent = formatTime(remainingSeconds);
                remainingSeconds--;
                setTimeout(tick, 1000);
            }

            tick();
        })();

        // Init
        updateAnsweredCount();

        // Submit confirm
        document.getElementById('quiz-form').addEventListener('submit', function(e) {
            const total = parseInt(this.dataset.totalQuestions);
            const answered = document.querySelectorAll('.quiz-option:checked').length;

            if (answered < total) {
                e.preventDefault();
                alert('Please answer all questions before submitting. (' + answered + '/' + total + ' answered)');
            }
        });
    </script>
</x-app-layout>