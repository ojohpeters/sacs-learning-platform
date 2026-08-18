@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->title }}</h1>
            <p class="text-gray-500 text-sm">
                Course: {{ $quiz->section->course->title }} → 
                Section {{ $quiz->section->order }}: {{ $quiz->section->title }}
            </p>
        </div>
        <a href="{{ route('admin.quizzes.index', $quiz->section->course_id) }}" class="text-gray-600 hover:text-gray-800">
            ← Back to Quizzes
        </a>
    </div>

    <div class="grid grid-cols-3 gap-8">
        
        {{-- Left: Quiz Settings --}}
        <div class="col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Quiz Settings</h3>
                <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                            <input type="text" name="title" value="{{ $quiz->title }}" required
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                <option value="section_quiz" {{ $quiz->type === 'section_quiz' ? 'selected' : '' }}>Section Quiz</option>
                                <option value="final_exam" {{ $quiz->type === 'final_exam' ? 'selected' : '' }}>Final Exam</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Passing Score (%)</label>
                            <input type="number" name="passing_score" value="{{ $quiz->passing_score }}" required min="1" max="100"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Min Submit Time (min)</label>
                            <input type="number" name="min_submit_time" value="{{ $quiz->min_submit_time }}" required min="1"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Time Limit (min, optional)</label>
                            <input type="number" name="time_limit" value="{{ $quiz->time_limit }}"
                                   class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                        </div>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="show_correct_answers" value="1" {{ $quiz->show_correct_answers ? 'checked' : '' }} class="rounded">
                                <span class="ml-1 text-xs">Show answers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $quiz->is_active ? 'checked' : '' }} class="rounded">
                                <span class="ml-1 text-xs">Active</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>

        {{-- Right: Questions --}}
        <div class="col-span-2">
            
            {{-- Existing Questions --}}
            <div class="space-y-4 mb-8">
                @foreach($quiz->questions as $question)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-medium text-gray-900">
                                Q{{ $question->order }}: {{ $question->question_text }}
                            </h4>
                            <form action="{{ route('admin.quizzes.questions.destroy', $question) }}" method="POST"
                                  onsubmit="return confirm('Delete this question?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </div>
                        <ul class="space-y-1 ml-4">
                            @foreach($question->options as $option)
                                <li class="flex items-center text-sm {{ $option->is_correct ? 'text-green-600 font-semibold' : 'text-gray-600' }}">
                                    <span class="mr-2">{{ $option->is_correct ? '✓' : '○' }}</span>
                                    {{ chr(64 + $option->order) }}. {{ $option->option_text }}
                                </li>
                            @endforeach
                        </ul>
                        <button onclick="toggleEditQuestion({{ $question->id }})" 
                                class="mt-3 text-blue-600 text-sm hover:text-blue-800">Edit</button>

                        {{-- Edit Question Form (hidden) --}}
                        <div id="edit-question-{{ $question->id }}" class="hidden mt-4 pt-4 border-t">
                            <form action="{{ route('admin.quizzes.questions.update', $question) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Question Text</label>
                                    <input type="text" name="question_text" value="{{ $question->question_text }}" required
                                           class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm">
                                </div>
                                <div class="space-y-2 mb-3">
                                    @foreach($question->options as $i => $option)
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="correct_option" value="{{ $i }}" {{ $option->is_correct ? 'checked' : '' }}
                                                   class="text-accent">
                                            <input type="text" name="options[{{ $i }}][text]" value="{{ $option->option_text }}" required
                                                   class="flex-1 border border-gray-300 rounded px-3 py-1 text-sm"
                                                   placeholder="Option {{ chr(65 + $i) }}">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Save</button>
                                    <button type="button" onclick="toggleEditQuestion({{ $question->id }})" class="text-gray-600 text-sm hover:text-gray-800">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                @if($quiz->questions->isEmpty())
                    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                        <p>No questions yet. Add your first question below.</p>
                    </div>
                @endif
            </div>

            {{-- Add New Question --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Add New Question</h3>
                <form action="{{ route('admin.quizzes.questions.store', $quiz) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                        <input type="text" name="question_text" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2"
                               placeholder="Enter your question...">
                    </div>
                    
                    <div class="space-y-3 mb-4">
                        @for($i = 0; $i < 4; $i++)
                            <div class="flex items-center space-x-3">
                                <input type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}
                                       class="text-accent">
                                <input type="text" name="options[{{ $i }}][text]" required
                                       class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm"
                                       placeholder="Option {{ chr(65 + $i) }}">
                            </div>
                        @endfor
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Add Question
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function toggleEditQuestion(id) {
            document.getElementById('edit-question-' + id).classList.toggle('hidden');
        }
    </script>
@endsection