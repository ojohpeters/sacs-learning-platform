@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
            <p class="text-gray-500 mt-1">Curriculum Builder</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-800">← Back to Courses</a>
    </div>

    <div class="space-y-6">
        @foreach($course->sections as $section)
            <div class="bg-white rounded-lg shadow">
                {{-- Section Header --}}
                <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Section {{ $section->order }}: {{ $section->title }}</h3>
                    <div class="flex space-x-2">
                        {{-- Edit Section Title --}}
                        <form action="{{ route('admin.sections.update', $section) }}" method="POST" class="flex space-x-2">
                            @csrf
                            @method('PUT')
                            <input type="text" name="title" value="{{ $section->title }}" 
                                   class="border border-gray-300 rounded px-3 py-1 text-sm">
                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">Save</button>
                        </form>
                        {{-- Delete Section --}}
                        <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Delete this section and all its lessons?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete Section</button>
                        </form>
                    </div>
                </div>

                {{-- Lessons List --}}
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500">
                                <th class="pb-2">#</th>
                                <th class="pb-2">Title</th>
                                <th class="pb-2">Type</th>
                                <th class="pb-2">Preview</th>
                                <th class="pb-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($section->lessons as $lesson)
                                <tr>
                                    <td class="py-2 text-sm">{{ $lesson->order }}</td>
                                    <td class="py-2 text-sm">{{ $lesson->title }}</td>
                                    <td class="py-2 text-sm">
                                        <span class="px-2 py-0.5 rounded text-xs bg-blue-100 text-blue-800">
                                            {{ ucfirst($lesson->content_type) }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-sm">
                                        @if($lesson->is_free_preview)
                                            <span class="text-green-600">✓ Free</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-sm space-x-2">
                                        <button onclick="toggleEditLesson({{ $lesson->id }})" class="text-gray-600 hover:text-gray-800">Edit</button>
                                        <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="inline" onsubmit="return confirm('Delete this lesson?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                {{-- Edit Lesson Form (hidden by default) --}}
                                <tr id="edit-lesson-{{ $lesson->id }}" class="hidden bg-gray-50">
                                    <td colspan="5" class="py-4 px-4">
                                        <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                                                    <input type="text" name="title" value="{{ $lesson->title }}" required
                                                           class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                                                    <select name="content_type" class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                                                        <option value="video" {{ $lesson->content_type === 'video' ? 'selected' : '' }}>Video</option>
                                                        <option value="text" {{ $lesson->content_type === 'text' ? 'selected' : '' }}>Text</option>
                                                        <option value="image" {{ $lesson->content_type === 'image' ? 'selected' : '' }}>Image</option>
                                                        <option value="pdf" {{ $lesson->content_type === 'pdf' ? 'selected' : '' }}>PDF</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Upload File</label>
                                                    @if($lesson->content_path)
                                                        <div class="mb-2 flex items-center">
                                                            <span class="text-xs text-gray-500">Current file: </span>
                                                            <a href="{{ asset('storage/' . $lesson->content_path) }}" target="_blank" class="text-xs text-accent ml-1 hover:underline">
                                                                {{ basename($lesson->content_path) }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="content_file"
                                                           class="w-full border border-gray-300 rounded px-3 py-1 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-accent file:text-white hover:file:bg-accent-dark">
                                                    <p class="text-xs text-gray-400 mt-0.5">Upload new to replace. Leave empty to keep current file.</p>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Duration (minutes)</label>
                                                    <input type="number" name="duration" value="{{ $lesson->duration }}" min="0"
                                                           class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                                                    <p class="text-xs text-gray-400 mt-0.5">Only needed for videos</p>
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Content Body (HTML)</label>
                                                    <textarea name="content_body" rows="3"
                                                              class="w-full border border-gray-300 rounded px-3 py-1 text-sm font-mono">{{ $lesson->content_body }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="flex items-center">
                                                        <input type="checkbox" name="is_free_preview" value="1" {{ $lesson->is_free_preview ? 'checked' : '' }} class="rounded border-gray-300">
                                                        <span class="ml-2 text-sm">Free Preview</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <button type="submit" class="mt-3 bg-blue-600 text-white px-4 py-1 rounded text-sm hover:bg-blue-700">Update Lesson</button>
                                            <button type="button" onclick="toggleEditLesson({{ $lesson->id }})" class="mt-3 ml-2 text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Add New Lesson --}}
                    <div class="mt-4 pt-4 border-t">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Add New Lesson</h4>
                        <form action="{{ route('admin.lessons.store', $section) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" name="title" required
                                           class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                                    <select name="content_type" class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                                        <option value="video">Video</option>
                                        <option value="text">Text</option>
                                        <option value="image">Image</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Upload File</label>
                                    <input type="file" name="content_file" id="new-content-file"
                                           class="w-full border border-gray-300 rounded px-3 py-1 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-accent file:text-white hover:file:bg-accent-dark">
                                    <p class="text-xs text-gray-400 mt-0.5" id="new-file-hint">Required for video/image/PDF. Not needed for text.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Duration (minutes)</label>
                                    <input type="number" name="duration" value="0" min="0"
                                           class="w-full border border-gray-300 rounded px-3 py-1 text-sm">
                                    <p class="text-xs text-gray-400 mt-0.5">Only needed for videos</p>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Content Body (HTML)</label>
                                    <textarea name="content_body" rows="2"
                                              class="w-full border border-gray-300 rounded px-3 py-1 text-sm font-mono"></textarea>
                                </div>
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_free_preview" value="1" class="rounded border-gray-300">
                                        <span class="ml-2 text-sm">Free Preview</span>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="mt-3 bg-green-600 text-white px-4 py-1 rounded text-sm hover:bg-green-700">Add Lesson</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Add New Section --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-900 mb-3">Add New Section</h3>
            <form action="{{ route('admin.sections.store', $course) }}" method="POST" class="flex space-x-4">
                @csrf
                <input type="text" name="title" placeholder="Section title..." required
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Add Section</button>
            </form>
        </div>
    </div>

    <script>
        function toggleEditLesson(id) {
            document.getElementById('edit-lesson-' + id).classList.toggle('hidden');
        }
    </script>
@endsection

<script>
    function toggleEditLesson(id) {
        var row = document.getElementById('edit-lesson-' + id);
        row.classList.toggle('hidden');
        
        // Initialize TinyMCE on newly visible textareas
        if (!row.classList.contains('hidden')) {
            row.querySelectorAll('textarea[name="content_body"]').forEach(function(textarea) {
                if (!textarea.hasAttribute('data-tinymce-initialized')) {
                    textarea.setAttribute('data-tinymce-initialized', 'true');
                    ClassicEditor
                        .create(textarea, {
                            toolbar: [
                                'undo', 'redo', '|',
                                'heading', '|',
                                'bold', 'italic', 'underline', '|',
                                'bulletedList', 'numberedList', '|',
                                'blockQuote', '|',
                                'code', 'codeBlock', '|',
                                'removeFormat'
                            ],
                            placeholder: 'Type your lesson content here...',
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        }
    }

    // File type and duration logic
    document.querySelectorAll('select[name="content_type"]').forEach(function(select) {
        updateFileHint(select);
        select.addEventListener('change', function() {
            updateFileHint(this);
        });
    });

    function updateFileHint(select) {
        var durationField = select.closest('form')?.querySelector('#duration-field') 
            || select.closest('.grid')?.querySelector('#duration-field');
        
        if (durationField) {
            durationField.style.display = select.value === 'video' ? 'block' : 'none';
        }
    }
</script>