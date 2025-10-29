@extends('layouts.app')

@section('content')
<div class="flex">
    <!-- Sidebar -->
    <div class="w-1/5 min-h-screen bg-white border-r px-6 py-8">
        <h1 class="text-xl font-bold text-red-700 mb-8">FileB</h1>
        <ul class="space-y-4 text-sm font-semibold">
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-700">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('files.myfiles') }}" class="text-gray-700 hover:text-red-700">My Files</a>
            </li>
            <li>
                <a href="{{ route('files.favorites') }}" class="text-red-700">Favorites</a>
            </li>
        </ul>
    </div>

    <!-- Main -->
    <div class="flex-1 p-8 bg-gray-50 min-h-screen">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">My Favorites</h2>
                <p class="text-gray-500 text-sm">Files yang kamu tandai ⭐</p>
            </div>
        </div>

        @if($files->count() === 0)
            <!-- Empty state -->
            <div class="bg-white border rounded-2xl p-10 text-center text-gray-500">
                <i class="fa-regular fa-star text-5xl mb-3"></i>
                <p class="font-medium mb-1">Belum ada favorit</p>
                <p class="text-sm">Tandai file dengan klik ikon ⭐ di My Files.</p>
            </div>
        @else
            <!-- Table daftar favorit -->
            <div class="bg-white rounded-xl shadow overflow-x-auto">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-5 py-3 text-left">Name</th>
                            <th class="px-5 py-3 text-left">Category</th>
                            <th class="px-5 py-3 text-left">Folder</th>
                            <th class="px-5 py-3 text-left">Uploaded</th>
                            <th class="px-5 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($files as $file)
                            <tr class="border-t hover:bg-gray-50">
                                <!-- Name -->
                                <td class="px-5 py-3">
                                    <a href="{{ route('files.view', $file->id) }}"
                                       target="_blank"
                                       class="text-red-600 hover:underline">
                                        {{ $file->title }}
                                    </a>
                                </td>

                                <!-- Category -->
                                <td class="px-5 py-3">{{ $file->category->name ?? '-' }}</td>

                                <!-- Folder -->
                                <td class="px-5 py-3">
                                    @if($file->folder)
                                        <span class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full">
                                            {{ $file->folder->name }}
                                        </span>
                                    @else
                                        &mdash;
                                    @endif
                                </td>

                                <!-- Uploaded -->
                                <td class="px-5 py-3">{{ $file->created_at->format('d M Y') }}</td>

                                <!-- Actions -->
                                <td class="px-5 py-3">
                                    <div class="inline-flex gap-3">
                                        <a href="{{ route('files.download', $file->id) }}"
                                           class="text-sm text-gray-700 hover:underline">
                                           Download
                                        </a>

                                        <form action="{{ route('files.destroy', $file->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this file?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-sm text-red-600 hover:underline">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $files->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
