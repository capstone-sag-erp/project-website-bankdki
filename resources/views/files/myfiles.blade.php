@extends('layouts.app')

@section('content')
@php
    // helper tampilan
    function humanBytesLocal($bytes) {
        if (!is_numeric($bytes)) return $bytes ?: '-';
        if ($bytes >= 1073741824) return number_format($bytes/1073741824,2).' GB';
        if ($bytes >= 1048576)    return number_format($bytes/1048576,2).' MB';
        if ($bytes >= 1024)       return number_format($bytes/1024,2).' KB';
        return $bytes.' B';
    }
    function fileExtIcon($file) {
        $name = $file->title ?? '';
        $ext  = pathinfo($file->file_path ?? $name, PATHINFO_EXTENSION);
        $ext  = strtolower($ext);
        return match ($ext) {
            'pdf' => 'fa-file-pdf',
            'doc','docx' => 'fa-file-word',
            'xls','xlsx' => 'fa-file-excel',
            'ppt','pptx' => 'fa-file-powerpoint',
            'jpg','jpeg','png','gif','webp' => 'fa-file-image',
            'zip','rar','7z' => 'fa-file-zipper',
            'txt','md' => 'fa-file-lines',
            default => 'fa-file',
        };
    }
    $isGrid = request('view','list') === 'grid';
@endphp

<div class="flex">
    <!-- Sidebar (ikuti pola halaman lain agar konsisten) -->
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
                <a href="{{ route('files.favorites') }}" class="text-gray-700 hover:text-red-700">Favorites</a>
            </li>
        </ul>
    </div>

    <!-- Main -->
    <div class="flex-1 p-8 bg-gray-50 min-h-screen">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">My Files</h2>
                <p class="text-gray-500 text-sm">You can securely upload and manage your folders and assets here</p>
            </div>

            <!-- Actions kanan: Upload + Toggle list/grid -->
            <div class="flex items-center gap-2">
                <a href="{{ route('files.myfiles', array_merge(request()->all(), ['view'=>'list'])) }}"
                   class="px-3 py-2 rounded-lg border text-sm {{ !$isGrid ? 'bg-white shadow' : 'bg-gray-100' }}"
                   title="List view">📋</a>
                <a href="{{ route('files.myfiles', array_merge(request()->all(), ['view'=>'grid'])) }}"
                   class="px-3 py-2 rounded-lg border text-sm {{ $isGrid ? 'bg-white shadow' : 'bg-gray-100' }}"
                   title="Grid view">🔲</a>

                <button onclick="toggleUploadModal(true)"
                    class="ml-2 bg-gradient-to-r from-[#800000] to-[#b22222] text-white px-4 py-2 rounded-lg shadow hover:brightness-110 text-sm">
                    + Upload
                </button>
            </div>
        </div>

        <!-- Filter bar -->
        <form method="GET" class="flex flex-wrap gap-2 items-center mb-4">
            <input type="hidden" name="view" value="{{ $isGrid ? 'grid' : 'list' }}"/>
            <div class="flex-1 min-w-[220px]">
                <div class="relative">
                    <input name="search" value="{{ request('search') }}" placeholder="Search title..."
                           class="w-full border rounded-lg pl-3 pr-9 py-2 text-sm bg-white focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
                </div>
            </div>

            <select name="category_id" class="border rounded-lg px-3 py-2 text-sm bg-white">
                <option value="">All Categories</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded-lg px-3 py-2 text-sm bg-white">
            <span class="text-gray-400">—</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded-lg px-3 py-2 text-sm bg-white">

            <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
            @if(request()->anyFilled(['search','category_id','date_from','date_to']))
                <a href="{{ route('files.myfiles', ['view' => $isGrid ? 'grid' : 'list']) }}" class="text-sm text-gray-600 underline">Clear</a>
            @endif

            <!-- Sort -->
            <div class="ml-auto">
                <select name="sort" onchange="this.form.submit()" class="border rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="created_at" {{ $sort==='created_at' ? 'selected' : '' }}>Last Modified</option>
                    <option value="title"      {{ $sort==='title' ? 'selected' : '' }}>Title</option>
                    <option value="size"       {{ $sort==='size' ? 'selected' : '' }}>Size</option>
                </select>
                <select name="dir" onchange="this.form.submit()" class="border rounded-lg px-2 py-2 text-sm bg-white">
                    <option value="desc" {{ $dir==='desc' ? 'selected' : '' }}>↓</option>
                    <option value="asc"  {{ $dir==='asc'  ? 'selected' : '' }}>↑</option>
                </select>
            </div>
        </form>

        @if($files->count() === 0)
            <!-- Empty state -->
            <div class="bg-white border rounded-2xl p-10 text-center text-gray-500">
                <i class="fa-regular fa-folder-open text-5xl mb-3"></i>
                <p class="font-medium mb-1">No files yet</p>
                <p class="text-sm">Click “Upload” to add your first file.</p>
            </div>
        @else
            @if(!$isGrid)
                <!-- LIST VIEW -->
                <div class="bg-white rounded-xl shadow overflow-x-auto">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-5 py-3 text-left w-12">Fav</th>
                                <th class="px-5 py-3 text-left">Name</th>
                                <th class="px-5 py-3 text-left">Category</th>
                                <th class="px-5 py-3 text-left">Folder</th>
                                <th class="px-5 py-3 text-left">Uploaded</th>
                                <th class="px-5 py-3 text-left">Size</th>
                                <th class="px-5 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files as $file)
                                <tr class="border-t hover:bg-gray-50">
                                    {{-- Fav (solid star untuk dua state, beda warna) --}}
                                    <td class="px-5 py-3">
                                        <button
                                            class="fav-toggle w-7 h-7 grid place-items-center rounded hover:bg-yellow-50"
                                            data-id="{{ $file->id }}"
                                            title="Toggle favorite"
                                            aria-label="Toggle favorite">
                                            <i class="fa-solid fa-star fa-fw text-xl {{ isset($favoritedMap[$file->id]) ? 'text-yellow-500' : 'text-gray-400' }}"></i>
                                        </button>
                                    </td>

                                    {{-- Name --}}
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid {{ fileExtIcon($file) }} text-gray-600"></i>
                                            <a href="{{ route('files.view', $file->id) }}" target="_blank" class="text-red-600 hover:underline">{{ $file->title }}</a>
                                        </div>
                                    </td>

                                    {{-- Category --}}
                                    <td class="px-5 py-3">{{ $file->category->name ?? '-' }}</td>

                                    {{-- Folder --}}
                                    <td class="px-5 py-3">
                                        @if($file->folder)
                                            <span class="inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full">
                                                {{ $file->folder->name }}
                                            </span>
                                        @else
                                            &mdash;
                                        @endif
                                    </td>

                                    {{-- Uploaded --}}
                                    <td class="px-5 py-3">{{ $file->created_at->format('d M Y') }}</td>

                                    {{-- Size --}}
                                    <td class="px-5 py-3">{{ humanBytesLocal($file->size) }}</td>

                                    {{-- Actions --}}
                                    <td class="px-5 py-3">
                                        <div class="relative inline-block">
                                            <button class="p-2 rounded-full hover:bg-gray-100 action-trigger" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical text-gray-600"></i>
                                            </button>
                                            <div class="action-menu hidden absolute right-0 mt-1 w-44 bg-white border rounded shadow-md z-30">
                                                <ul class="py-1">
                                                    <li><a href="{{ route('files.view', $file->id) }}" target="_blank" class="block px-4 py-2 hover:bg-gray-100">View</a></li>
                                                    <li><a href="{{ route('files.download', $file->id) }}" class="block px-4 py-2 hover:bg-gray-100">Download</a></li>
                                                    <li><button type="button" onclick="openEditFileModal({{ $file->id }}, '{{ addslashes($file->title) }}', {{ $file->folder_id ?? 'null' }})" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Rename/Move</button></li>
                                                    <li>
                                                        <form action="{{ route('files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Delete this file?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- GRID VIEW -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($files as $file)
                        <div class="group bg-white rounded-xl p-4 shadow hover:shadow-lg transition relative">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-2">
                                    {{-- Fav button (solid star dua state, beda warna) --}}
                                    <button
                                        class="fav-toggle w-7 h-7 grid place-items-center rounded hover:bg-yellow-50"
                                        data-id="{{ $file->id }}"
                                        title="Toggle favorite"
                                        aria-label="Toggle favorite">
                                        <i class="fa-solid fa-star fa-fw text-xl {{ isset($favoritedMap[$file->id]) ? 'text-yellow-500' : 'text-gray-400' }}"></i>
                                    </button>

                                    <div>
                                        <a href="{{ route('files.view', $file->id) }}" target="_blank" class="font-medium text-gray-800 hover:underline line-clamp-1">{{ $file->title }}</a>
                                        <div class="text-xs text-gray-500">
                                            {{ $file->category->name ?? '-' }} · {{ humanBytesLocal($file->size) }}
                                        </div>
                                    </div>
                                </div>

                                <button class="p-2 rounded-full hover:bg-gray-100 action-trigger" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical text-gray-600"></i>
                                </button>

                                <div class="action-menu hidden absolute right-2 top-10 w-44 bg-white border rounded shadow-md z-30">
                                    <ul class="py-1">
                                        <li><a href="{{ route('files.view', $file->id) }}" target="_blank" class="block px-4 py-2 hover:bg-gray-100">View</a></li>
                                        <li><a href="{{ route('files.download', $file->id) }}" class="block px-4 py-2 hover:bg-gray-100">Download</a></li>
                                        <li><button type="button" onclick="openEditFileModal({{ $file->id }}, '{{ addslashes($file->title) }}', {{ $file->folder_id ?? 'null' }})" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Rename/Move</button></li>
                                        <li>
                                            <form action="{{ route('files.destroy', $file->id) }}" method="POST" onsubmit="return confirm('Delete this file?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            @if($file->folder)
                                <span class="mt-3 inline-block bg-gray-200 text-gray-800 text-xs px-2 py-1 rounded-full">{{ $file->folder->name }}</span>
                            @endif
                            <div class="text-xs text-gray-500 mt-2">{{ $file->created_at->format('d M Y') }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-4">
                {{ $files->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Upload (pakai pola dashboard supaya konsisten) -->
<div id="uploadModal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold mb-4">Upload File</h3>
        <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm mb-1">Title</label>
                <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1">Category</label>
                <select name="category_id" class="w-full border rounded px-3 py-2" required>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm mb-1">File</label>
                <input type="file" name="file" class="w-full" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleUploadModal(false)" class="px-4 py-2 rounded border">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit (reuse update route kamu) -->
<div id="editFileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h2 class="text-xl font-semibold text-gray-800 mb-5">Rename / Move File</h2>
        <form id="editFileForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="editFileTitle" required
                       class="mt-1 w-full px-3 py-2 border rounded-lg focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Folder</label>
                <select name="folder_id" id="editFileFolder"
                        class="mt-1 w-full px-3 py-2 border rounded-lg focus:outline-none">
                    <option value="">— No Folder —</option>
                    {{-- Optional: isi folder sesuai kebutuhan --}}
                </select>
                <p class="text-xs text-gray-500 mt-1">*Pindah folder hanya bekerja untuk folder yang tersedia di kategori terkait (opsional).</p>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="toggleEditFileModal(false)" class="px-4 py-2 rounded bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white">Save</button>
            </div>
        </form>
    </div>
</div>

<style>
    .action-menu{border-radius:.75rem}
    .action-menu::before{content:"";position:absolute;top:-6px;right:12px;width:12px;height:12px;background:#fff;transform:rotate(45deg);box-shadow:-1px -1px 2px rgba(0,0,0,.06);z-index:-1}

    /* Biar ikon bintang selalu terlihat dan sedikit “nendang” */
    .fav-toggle i {
        line-height: 1;
        filter: drop-shadow(0 0 1px rgba(0,0,0,.06));
    }
</style>

<script>
    function toggleUploadModal(show){
        document.getElementById('uploadModal').classList.toggle('hidden', !show)
    }
    function openEditFileModal(id, title, folderId){
        document.getElementById('editFileTitle').value = title;
        document.getElementById('editFileFolder').value = folderId ?? '';
        document.getElementById('editFileForm').action = `/files/${id}`;
        toggleEditFileModal(true);
    }
    function toggleEditFileModal(show){
        document.getElementById('editFileModal').classList.toggle('hidden', !show)
    }

    // dropdown 3-titik
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.action-trigger');
        if (trigger) {
            e.preventDefault(); e.stopPropagation();
            const menu = trigger.parentElement.querySelector('.action-menu');
            document.querySelectorAll('.action-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.action-trigger').forEach(b => b.setAttribute('aria-expanded','false'));
            trigger.setAttribute('aria-expanded', 'true');
            menu.classList.toggle('hidden');
            return;
        }
        document.querySelectorAll('.action-menu').forEach(m => m.classList.add('hidden'));
        document.querySelectorAll('.action-trigger').forEach(b => b.setAttribute('aria-expanded','false'));
    });

    // Toggle Favorite (AJAX) — cukup toggle warna (ikon selalu fa-solid)
    document.addEventListener('click', async function(e){
        const btn = e.target.closest('.fav-toggle');
        if (!btn) return;

        const fileId = btn.dataset.id;
        try {
            const res = await fetch(`/files/${fileId}/favorite`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error('Request failed');
            const data = await res.json();

            const icon = btn.querySelector('i');
            if (!icon) return;

            if (data.status === 'added') {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-yellow-500');
            } else {
                icon.classList.remove('text-yellow-500');
                icon.classList.add('text-gray-400');
            }
        } catch(err){
            alert('Gagal mengubah favorit.');
            console.error(err);
        }
    });
</script>
@endsection
