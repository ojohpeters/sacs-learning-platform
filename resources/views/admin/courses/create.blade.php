@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create New Course</h1>
        <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-800">← Back to Courses</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Course Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                <textarea name="short_description" rows="2" required
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('short_description') }}</textarea>
                @error('short_description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Description (HTML allowed)</label>
                <textarea name="full_description" rows="8" required
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">{{ old('full_description') }}</textarea>
                @error('full_description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Base Price (₦) — In-Class</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" required step="0.01"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">This is the In-Class price. Other prices are auto-calculated.</p>
                @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                
                {{-- Calculated Prices Preview --}}
                <div class="mt-3 bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Prices students will see:</p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">In-Class (Base + Registration)</span>
                            <span class="font-semibold" id="preview-inclass">₦0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Live Online (Base - ₦10,000 + Registration)</span>
                            <span class="font-semibold" id="preview-sync">₦0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Self-Paced (Base - ₦25,000 + Registration)</span>
                            <span class="font-semibold text-green-600" id="preview-async">₦0</span>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.querySelector('input[name="price"]').addEventListener('input', function() {
                    var base = parseInt(this.value) || 0;
                    document.getElementById('preview-inclass').textContent = '₦' + (base + 4000).toLocaleString();
                    document.getElementById('preview-sync').textContent = '₦' + Math.max(base - 10000 + 4000, 0).toLocaleString();
                    document.getElementById('preview-async').textContent = '₦' + Math.max(base - 25000 + 4000, 0).toLocaleString();
                });
            </script>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Course Thumbnail</label>
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-accent file:text-white hover:file:bg-accent-dark">
                <p class="text-xs text-gray-500 mt-1">Recommended: 600×400 pixels. JPG, PNG, or WebP. Max 2MB.</p>
                @error('thumbnail') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Publish immediately</span>
                </label>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Create Course
            </button>
        </form>
    </div>
@endsection