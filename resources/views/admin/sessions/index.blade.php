@extends('admin.layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
            <p class="text-gray-500 mt-1">Manage Live Sessions</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="text-gray-600 hover:text-gray-800">← Back to Courses</a>
    </div>

    {{-- Add Session Form --}}
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="font-semibold text-gray-900 mb-4">Schedule New Session</h3>
        <form action="{{ route('admin.sessions.store', $course) }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Session Title</label>
                    <input type="text" name="title" required placeholder="e.g., Introduction to Machine Learning"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="session_date" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="sync">Synchronous (Live Online)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="time" name="start_time" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-0.5">24-hour format (e.g., 14:00 = 2:00 PM)</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                    <input type="time" name="end_time" required
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-0.5">24-hour format (e.g., 16:00 = 4:00 PM)</p>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Meeting Link (Zoom / Google Meet)</label>
                    <input type="url" name="meeting_link" placeholder="https://zoom.us/j/..."
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-accent text-white px-6 py-2 rounded-lg hover:bg-accent-dark transition-colors">
                Schedule Session
            </button>
        </form>
    </div>

    {{-- Sessions List --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-900">All Sessions</h3>
        </div>
        
        @if($sessions->isEmpty())
            <div class="p-6 text-center text-gray-500">No sessions scheduled yet.</div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Time</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($sessions as $session)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium">{{ $session->title }}</td>
                            <td class="px-6 py-4 text-sm">{{ $session->session_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm">{{ date('h:i A', strtotime($session->start_time)) }} - {{ date('h:i A', strtotime($session->end_time)) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span @class([
                                    'px-2 py-1 rounded text-xs font-medium',
                                    'bg-blue-100 text-blue-800' => $session->status === 'upcoming',
                                    'bg-green-100 text-green-800' => $session->status === 'live',
                                    'bg-gray-100 text-gray-800' => $session->status === 'completed',
                                    'bg-red-100 text-red-800' => !in_array($session->status, ['upcoming', 'live', 'completed']),
                                ])>
                                    {{ ucfirst($session->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <button onclick="toggleEditSession({{ $session->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Delete this session?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                        {{-- Edit Row --}}
                        <tr id="edit-session-{{ $session->id }}" class="hidden bg-gray-50">
                            <td colspan="5" class="px-6 py-4">
                                <form action="{{ route('admin.sessions.update', $session) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                                            <input type="text" name="title" value="{{ $session->title }}" required
                                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                                            <input type="date" name="session_date" value="{{ $session->session_date->format('Y-m-d') }}" required
                                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                            <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                                <option value="upcoming" {{ $session->status === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                                <option value="live" {{ $session->status === 'live' ? 'selected' : '' }}>Live</option>
                                                <option value="completed" {{ $session->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $session->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                                            <input type="time" name="start_time" value="{{ date('H:i', strtotime($session->start_time)) }}" required
                                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                            <p class="text-xs text-gray-400 mt-0.5">24-hour format</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                                            <input type="time" name="end_time" value="{{ date('H:i', strtotime($session->end_time)) }}" required
                                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                            <p class="text-xs text-gray-400 mt-0.5">24-hour format</p>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Meeting Link</label>
                                            <input type="url" name="meeting_link" value="{{ $session->meeting_link }}"
                                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Recording Link</label>
                                            <input type="url" name="recording_link" value="{{ $session->recording_link }}"
                                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                        </div>
                                    </div>
                                    <div class="mt-3 space-x-2">
                                        <button type="submit" class="bg-accent text-white px-4 py-1.5 rounded text-sm hover:bg-accent-dark">Update</button>
                                        <button type="button" onclick="toggleEditSession({{ $session->id }})" class="text-sm text-gray-600 hover:text-gray-800">Cancel</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        function toggleEditSession(id) {
            document.getElementById('edit-session-' + id).classList.toggle('hidden');
        }
    </script>
@endsection