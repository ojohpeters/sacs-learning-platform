<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/44.3.0/classic/ckeditor.js"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">

        {{-- Sidebar --}}
        <div class="w-64 bg-gray-900 text-white flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">S</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white">SACS Computers</h1>
                        <p class="text-xs text-gray-400">Admin Panel</p>
                    </div>
                </div>
                <p class="text-sm text-gray-400 mt-2">{{ auth()->user()->name }}</p>
            </div>

            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : '' }}">
                    <span>📊</span>
                    <span class="ml-3">Dashboard</span>
                </a>

                <a href="{{ route('admin.courses.index') }}"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.courses.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : '' }}">
                    <span>📚</span>
                    <span class="ml-3">Courses</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : '' }}">
                    <span>👥</span>
                    <span class="ml-3">Users</span>
                </a>

                <a href="{{ route('admin.payments.index') }}"
                    class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-800 hover:text-white {{ request()->routeIs('admin.payments.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : '' }}">
                    <span>💳</span>
                    <span class="ml-3">Payments</span>
                </a>
            </nav>

            <div class="absolute bottom-0 w-64 p-6 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-gray-400 hover:text-white">
                        🚪 Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 overflow-y-auto">
            <div class="p-8">
                <div class="mb-6">
                    <a href="/" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">
                        ← Back to Welcome Page
                    </a>
                </div>

                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('textarea').forEach(function(textarea) {
                if (textarea.name === 'content_body' || textarea.name === 'full_description') {
                    ClassicEditor
                        .create(textarea, {
                            toolbar: [
                                'undo', 'redo', '|',
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', '|',
                                'bulletedList', 'numberedList', '|',
                                'outdent', 'indent', '|',
                                'blockQuote', 'insertTable', '|',
                                'code', 'codeBlock', '|',
                                'removeFormat'
                            ],
                            heading: {
                                options: [
                                    { model: 'paragraph', title: 'Paragraph' },
                                    { model: 'heading2', view: 'h2', title: 'Heading 2' },
                                    { model: 'heading3', view: 'h3', title: 'Heading 3' },
                                    { model: 'heading4', view: 'h4', title: 'Heading 4' },
                                ]
                            },
                            codeBlock: {
                                languages: [
                                    { language: 'php', label: 'PHP' },
                                    { language: 'javascript', label: 'JavaScript' },
                                    { language: 'html', label: 'HTML' },
                                    { language: 'css', label: 'CSS' },
                                    { language: 'python', label: 'Python' },
                                    { language: 'sql', label: 'SQL' },
                                    { language: 'plaintext', label: 'Plain text' },
                                ]
                            },
                            placeholder: 'Type your content here...',
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        });
    </script>

</body>

</html>