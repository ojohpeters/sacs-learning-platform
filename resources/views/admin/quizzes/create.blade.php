@extends('admin.layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Quiz</h1>
        <p class="text-gray-500 mb-8">Section: {{ $section->title }}</p>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.quizzes.store', $section) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="section_quiz">Section Quiz</option>
                        <option value="final_exam">Final Exam</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Final exams don't show correct answers after failing.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passing Score (%)</label>
                        <input type="number" name="passing_score" value="{{ old('passing_score', 70) }}" required min="1" max="100"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Submit Time (min)</label>
                        <input type="number" name="min_submit_time" value="{{ old('min_submit_time', 5) }}" required min="1"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <p class="text-xs text-gray-400">Student must wait this long before submitting.</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit (minutes, optional)</label>
                    <input type="number" name="time_limit" value="{{ old('time_limit') }}" min="1"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <p class="text-xs text-gray-400">Leave empty for no time limit.</p>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="show_correct_answers" value="1" checked class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Show correct answers after failed attempt</span>
                    </label>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Create Quiz & Add Questions
                </button>
            </form>
        </div>
    </div>
@endsection