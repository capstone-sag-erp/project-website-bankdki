@extends('layouts.app')

@section('content')
@php
    // Konsistenkan kelas tombol primer (pink)
    $primaryBtn = 'bg-pink-500 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow';
@endphp

<div class="flex">

    <!-- Sidebar -->
    <div class="w-1/5 min-h-screen bg-white border-r px-6 py-8">
        <h1 class="text-xl font-bold text-red-700 mb-8">FileB</h1>
        <ul class="space-y-4 text-sm font-semibold">
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-700">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-700">All Files</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8 bg-gray-50 min-h-screen">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 drop-shadow-sm">{{ $category->name }}</h2>

        <!-- Flash message -->
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex space-x-4 mb-6">
            <button onclick="toggleNewFolderModal(true)" class="{{ $primaryBtn }}">
                <i class="fas fa-folder-plus mr-2" aria-hidden="true"></i>New Folder
            </button>
            <button onclick="toggleUploadModal(true)" class="{{ $primaryBtn }}">
                <i class="fas fa-upload mr-2" aria-hidden="true"></i>New Document
            </button>
        </div>

        <!-- All Folders -->
        <div class="mb-10">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">All Folders</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse ($folders as $folder)
                    <div class="relative group">
                        <a href="{{ route('category.folder', [
                                    'category' => $category->id,
                                    'folder'   => $folder->id
                                ]) }}"
                            class="block bg-white p-4 rounded shadow hover:shadow-lg transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-folder text-yellow-500" aria-hidden="true"></i>
                                    <span class="font-medium">{{ $folder->name }}</span>
                                </div>
                                <button class="text-gray-600 hover:text-gray-800 focus:outline-none" aria-label="More actions">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </a>
                        <!-- Dropdown Actions -->
                        <div class="absolute right-2 top-2 hidden group-hover:block z-10 bg-white border rounded shadow-md">
                            <button onclick="openRenameModal({{ $folder->id }}, '{{ addslashes($folder->name) }}')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">Rename</button>
                            <form action="{{ route('folders.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 w-full text-left">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No folders found.</p>
                @endforelse
            </div>
        </div>

        <!-- All Files -->
        <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-700">All Files</h3>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-sm table-auto">
                    <thead class="bg-gray-200 text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2 text-left">File Name</th>
                            <th class="px-4 py-2 text-left">Folder</th>
                            <th class="px-4 py-2 text-left">Size</th>
                            <th class="px-4 py-2 text-left">Uploaded</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($files as $file)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-red-600">{{ $file->title }}</td>
                                <td class="px-4 py-2">{{ $file->folder->name ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $sizeDisplay = '-';
                                        if (!empty($file->size)) {
                                            // jika size disimpan dalam bytes, konversi ke KB/MB
                                            if (is_numeric($file->size)) {
                                                $bytes = $file->size;
                                                $sizeDisplay = $bytes >= 1048576
                                                    ? number_format($bytes / 1048576, 2) . ' MB'
                                                    : number_format($bytes / 1024, 2) . ' KB';
                                            } else {
                                                $sizeDisplay = $file->size;
                                            }
                                        } elseif ($file->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)) {
                                            $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($file->file_path);
                                            $sizeDisplay = $bytes >= 1048576
                                                ? number_format($bytes / 1048576, 2) . ' MB'
                                                : number_format($bytes / 1024, 2) . ' KB';
                                        }
                                    @endphp
                                    {{ $sizeDisplay }}
                                </td>
                                <td class="px-4 py-2">{{ $file->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2">
                                    <div class="relative inline-block">
                                        <button aria-label="Actions" aria-haspopup="true" aria-expanded="false" type="button"
                                                class="p-2 rounded-full hover:bg-gray-100 focus:outline-none action-trigger">
                                            <!-- vertical ellipsis SVG (3 titik) -->
                                            <svg aria-hidden="true" class="w-4 h-4 text-gray-600" viewBox="0 0 192 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M96 208c26.5 0 48-21.5 48-48S122.5 112 96 112 48 133.5 48 160s21.5 48 48 48zm0 96c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm0-256c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z"/>
                                            </svg>
                                        </button>

                                        <div role="menu" aria-label="File actions"
                                             class="action-menu hidden absolute right-0 mt-1 w-44 bg-white border rounded shadow-md z-30">
                                            <ul class="py-1" role="none">
                                                <li role="none">
                                                    <a role="menuitem" href="{{ route('files.view', $file->id) }}" target="_blank"
                                                       class="block px-4 py-2 text-sm hover:bg-gray-100">Lihat</a>
                                                </li>
                                                <li role="none">
                                                    <a role="menuitem" href="{{ route('files.download', $file->id) }}"
                                                       class="block px-4 py-2 text-sm hover:bg-gray-100">Download</a>
                                                </li>
                                                <li role="none">
                                                    <button type="button"
                                                            onclick="openEditFileModal({{ $file->id }}, '{{ addslashes($file->title) }}', {{ $file->folder_id ?? 'null' }})"
                                                            class="block w-full text-left px-4 py-2 text-sm hover:bg-yellow-50">Edit</button>
                                                </li>
                                                <li role="none">
                                                    <form action="{{ route('files.destroy', $file->id) }}" method="POST"
                                                          onsubmit="return confirm('Yakin ingin menghapus file ini?')" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Hapus</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">No files found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reusable Edit File Modal -->
        <div id="editFileModalReusable" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
                <h2 class="text-xl font-semibold text-gray-800 mb-5">Edit File</h2>
                <form id="editFileForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul File</label>
                        <input type="text" name="title" id="editFileTitle" required
                               class="mt-1 w-full px-3 py-2 border rounded-lg focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Folder</label>
                        <select name="folder_id" id="editFileFolder"
                                class="mt-1 w-full px-3 py-2 border rounded-lg focus:outline-none">
                            <option value="">-- Tanpa Folder --</option>
                            @foreach ($folders as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="toggleEditFileModal(false)"
                                class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-red-500 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Create New Folder -->
<div id="newFolderModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Create New Folder</h2>
        <form action="{{ route('folders.store', ['category' => $category->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <input type="text" name="name" placeholder="Folder name" required class="w-full px-3 py-2 border rounded mb-4">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="toggleNewFolderModal(false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Upload File -->
<div id="uploadModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Upload File</h2>
        <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="title" placeholder="File Title" required class="w-full px-3 py-2 border rounded mb-4">
            <select name="folder_id" class="w-full px-3 py-2 border rounded mb-4">
                <option value="">No Folder</option>
                @foreach($folders as $folder)
                    <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                @endforeach
            </select>
            <input type="file" name="file" required class="w-full mb-4">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="toggleUploadModal(false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <input type="hidden" name="category_id" value="{{ $category->id }}">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Rename Folder -->
<div id="renameModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Rename Folder</h2>
        <form id="renameForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="name" id="renameInput" required class="w-full px-3 py-2 border rounded mb-4">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="toggleRenameModal(false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Rename File -->
<div id="renameFileModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-lg font-bold mb-4">Rename File</h2>
        <form id="renameFileForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="title" id="renameFileInput" required class="w-full px-3 py-2 border rounded mb-4">
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="toggleRenameFileModal(false)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Styles for improved dropdown -->
<style>
    .action-menu {
        min-width: 160px;
        border-radius: 0.5rem;
    }
    .action-menu::before {
        content: "";
        position: absolute;
        top: -6px;
        right: 12px;
        width: 12px;
        height: 12px;
        background: white;
        transform: rotate(45deg);
        box-shadow: -1px -1px 2px rgba(0,0,0,0.08);
        z-index: -1;
    }
</style>

<!-- Script -->
<script>
    function toggleNewFolderModal(show) {
        document.getElementById('newFolderModal').classList.toggle('hidden', !show);
    }

    function toggleUploadModal(show) {
        document.getElementById('uploadModal').classList.toggle('hidden', !show);
    }

    function openRenameModal(id, currentName) {
        document.getElementById('renameInput').value = currentName;
        document.getElementById('renameForm').action = `/folders/${id}`;
        document.getElementById('renameModal').classList.remove('hidden');
    }

    function toggleRenameModal(show) {
        document.getElementById('renameModal').classList.toggle('hidden', !show);
    }

    function openRenameFileModal(id, currentTitle) {
        document.getElementById('renameFileInput').value = currentTitle;
        document.getElementById('renameFileForm').action = `/files/${id}`;
        document.getElementById('renameFileModal').classList.remove('hidden');
    }

    function toggleRenameFileModal(show) {
        document.getElementById('renameFileModal').classList.toggle('hidden', !show);
    }

    function openEditFileModal(id, title, folderId) {
        document.getElementById('editFileTitle').value = title;
        document.getElementById('editFileFolder').value = folderId ?? '';
        document.getElementById('editFileForm').action = `/files/${id}`;
        document.getElementById('editFileModalReusable').classList.remove('hidden');
    }

    function toggleEditFileModal(show) {
        document.getElementById('editFileModalReusable').classList.toggle('hidden', !show);
    }

    // improved dropdown behavior for file actions
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.action-trigger');
        if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            const menu = trigger.nextElementSibling;
            if (!menu) return;

            // close others
            document.querySelectorAll('.action-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });

            // toggle this
            const expanded = trigger.getAttribute('aria-expanded') === 'true';
            trigger.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            menu.classList.toggle('hidden');

            // reposition if overflow right
            const rect = menu.getBoundingClientRect();
            if (rect.right > window.innerWidth) {
                menu.style.right = 'auto';
                menu.style.left = '0';
            } else {
                menu.style.left = '';
                menu.style.right = '';
            }
            return;
        }

        // click outside: close all dropdowns
        document.querySelectorAll('.action-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.action-trigger').forEach(btn => {
            btn.setAttribute('aria-expanded', 'false');
        });
    });

    // prevent internal clicks inside menu from closing it
    document.querySelectorAll('.action-menu').forEach(menu => {
        menu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });
</script>
@endsection
