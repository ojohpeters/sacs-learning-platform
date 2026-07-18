@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Courses</h1>
        <a href="{{ route('admin.courses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Add New Course
        </a>
    </div>

    <div class="bg-white rounded-lg shadow">
        <table class="w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Thumbnail</th>
                    <th class="px-6 py-3">Price</th>
                    <th class="px-6 py-3">Sections</th>
                    <th class="px-6 py-3">Enrollments</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($courses as $course)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $course->title }}</td>
                        <td class="px-6 py-4">
                            @if($course->thumbnail_path)
                                <img src="{{ asset('storage/' . $course->thumbnail_path) }}" alt="" class="w-16 h-10 object-cover rounded">
                            @else
                                <div class="w-16 h-10 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-xs text-gray-400">—</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">₦{{ number_format($course->price) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $course->sections_count }}</td>
                        <td class="px-6 py-4 text-sm">{{ $course->enrollments_count }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.courses.curriculum', $course) }}" class="text-blue-600 hover:text-blue-800">Curriculum</a>
                            <a href="{{ route('admin.sessions.index', $course) }}" class="text-green-600 hover:text-green-800">Sessions</a>
                            <a href="{{ route('admin.courses.edit', $course) }}" class="text-gray-600 hover:text-gray-800">Edit</a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Delete this course?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $courses->links() }}
    </div>
@endsection