@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
            <p class="text-gray-500 mt-1">Quizzes & Exams Management</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-800">← Back to Courses</a>
    </div>

    <div class="space-y-6">
        @foreach($course->sections as $section)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-900">
                        Section {{ $section->order }}: {{ $section->title }}
                    </h3>
                    @if($section->quiz)
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                {{ $section->quiz->type === 'final_exam' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $section->quiz->type === 'final_exam' ? 'Final Exam' : 'Section Quiz' }}
                            </span>
                            <span class="text-sm {{ $section->quiz->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $section->quiz->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    @if($section->quiz)
                        <div class="grid grid-cols-4 gap-4 mb-4">
                            <div>
                                <span class="text-xs text-gray-500">Questions</span>
                                <p class="font-semibold">{{ $section->quiz->questions->count() }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Passing Score</span>
                                <p class="font-semibold">{{ $section->quiz->passing_score }}%</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Attempts</span>
                                <p class="font-semibold">{{ $section->quiz->attempts->count() }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Min Submit Time</span>
                                <p class="font-semibold">{{ $section->quiz->min_submit_time }} min</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.quizzes.edit', $section->quiz) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Edit Quiz & Questions
                            </a>
                            <form action="{{ route('admin.quizzes.destroy', $section->quiz) }}" method="POST" 
                                  onsubmit="return confirm('Delete this quiz and all its questions?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm px-4 py-2">Delete Quiz</button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm mb-3">No quiz assigned to this section.</p>
                            <a href="{{ route('admin.quizzes.create', $section) }}" 
                               class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                + Add Quiz
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection