<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Quiz Results
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Result Banner --}}
            <div class="rounded-xl p-8 text-center mb-8 shadow-lg {{ $attempt->passed ? 'bg-success text-white' : 'bg-red-500 text-white' }}">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    @if($attempt->passed)
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @endif
                </div>
                <h1 class="text-2xl font-bold mb-2">
                    {{ $attempt->passed ? 'Congratulations! You Passed!' : 'Not Quite — Try Again' }}
                </h1>
                <p class="text-lg opacity-90">
                    Your Score: <span class="font-bold">{{ $attempt->score }}%</span>
                    ({{ $attempt->correct_answers }}/{{ $attempt->total_questions }} correct)
                </p>
                <p class="text-sm opacity-80 mt-1">Passing Score: {{ $quiz->passing_score }}%</p>
            </div>

            {{-- Show Answers (only if allowed) --}}
            @if($showAnswers)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4">Review Your Answers</h2>
                @foreach($attempt->answers as $answer)
                @if($answer->question)
                <div class="mb-4 pb-4 border-b last:border-0">
                    <p class="font-medium text-gray-900">
                        {{ $answer->question->order }}. {{ $answer->question->question_text }}
                    </p>
                    <div class="ml-4 mt-2 space-y-1">
                        @foreach($answer->question->options as $option)
                        <p class="text-sm flex items-center
                                        {{ $option->is_correct ? 'text-green-600 font-semibold' : '' }}
                                        {{ $option->id === $answer->selected_option_id && !$option->is_correct ? 'text-red-600 line-through' : '' }}">
                            @if($option->id === $answer->selected_option_id)
                            <span class="mr-2">→</span>
                            @else
                            <span class="mr-2"> </span>
                            @endif
                            {{ $option->option_text }}
                            @if($option->is_correct)
                            <span class="ml-2">✓</span>
                            @endif
                        </p>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex justify-center space-x-4">
                @if(!$attempt->passed)
                <a href="{{ route('quiz.retake', [$course->slug, $quiz->id]) }}"
                    class="bg-accent text-white px-8 py-3 rounded-lg font-semibold hover:bg-accent-dark transition-colors">
                    Retake Quiz
                </a>
                @else
                @if(isset($nextLesson))
                <a href="{{ route('learning.lesson', [$course->slug, $nextLesson->id]) }}"
                    class="bg-success text-white px-8 py-3 rounded-lg font-semibold hover:bg-success-dark transition-colors">
                    Continue to Next Section →
                </a>
                @else
                <a href="{{ route('learning.course', $course->slug) }}"
                    class="bg-success text-white px-8 py-3 rounded-lg font-semibold hover:bg-success-dark transition-colors">
                    Continue Learning
                </a>
                @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>