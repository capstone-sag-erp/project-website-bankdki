@extends('layouts.app')

@section('content')
@php
    // Kelas tombol primer (pink)
    $primaryBtn = 'bg-pink-500 hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow';
@endphp

<div class="flex">
    <!-- Sidebar -->
    <div class="w-1/5 min-h-screen bg-white border-r px-6 py-8">
        <h1 class="text-xl font-bold text-red-700 mb-8">FileB</h1>
        <ul class="space-y-4 text-sm font-semibold">
            <li><a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-700">Dashboard</a></li>
            <li><a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-red-700">All Files</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8 bg-gray-50 min-h-screen">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 drop-shadow-sm">
            {{ $category->name }} &nbsp;/&nbsp; {{ $folder->name }}
        </h2>

        <!-- Flash message -->
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex space-x-4 mb-6">
            <button onclick="toggleUploadModal(true)" class="{{ $primaryBtn }}">
                <i class="fas fa-upload mr-2"></i>New Document
            </button>
        </div>

        <!-- Files in this Folder -->
        <div>
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Files in “{{ $folder->name }}”</h3>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-sm table-auto">
                    <thead class="bg-gray-200 text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2 text-left">File Name</th>
                            <th class="px-4 py-2 text-left">Size</th>
                            <th class="px-4 py-2 text-left">Uploaded</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($files as $file)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-red-600">{{ $file->title }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        // Pastikan kita punya angka, cast ke integer
                                        $bytes = is_numeric($file->size) ? (int) $file->size : 0;

                                        if ($bytes > 0) {
                                            // Lebih dari 1 MB?
                                            if ($bytes >= 1048576) {
                                                echo number_format($bytes / 1048576, 2) . ' MB';
                                            } else {
                                                echo number_format($bytes / 1024, 2) . ' KB';
                                            }
                                        } else {
                                            // Kalau size tidak valid atau nol
                                            echo '-';
                                        }
                                    @endphp
                                </td>
                                <td class="px-4 py-2">{{ $file->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2">
                                    <div class="relative inline-block">
                                        <button type="button"
                                                class="p-2 rounded-full hover:bg-gray-100 focus:outline-none action-trigger"
                                                aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v text-gray-600"></i>
                                        </button>
                                        <div role="menu"
                                             class="action-menu hidden absolute right-0 mt-1 w-44 bg-white border rounded shadow-md z-30">
                                            <a href="{{ route('files.view', $file->id) }}"
                                               class="block px-4 py-2 text-sm hover:bg-gray-100">Lihat</a>
                                            <a href="{{ route('files.download', $file->id) }}"
                                               class="block px-4 py-2 text-sm hover:bg-gray-100">Download</a>
                                            <button onclick="openEditFileModal({{ $file->id }}, '{{ addslashes($file->title) }}', {{ $folder->id }})"
                                                    class="block w-full text-left px-4 py-2 text-sm hover:bg-yellow-50">Edit</button>
                                            <form action="{{ route('files.destroy', $file->id) }}" method="POST" class="m-0">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                                        onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">No files found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal (sama seperti di dashboard) -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
    <h3 class="text-lg font-semibold mb-4">Upload to “{{ $folder->name }}”</h3>
    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="category_id" value="{{ $category->id }}">
      <input type="hidden" name="folder_id"   value="{{ $folder->id }}">
      <div class="mb-4">
        <label class="block text-sm mb-1">File Title</label>
        <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-4">
        <label class="block text-sm mb-1">Pilih File</label>
        <input type="file" name="file" class="w-full" required>
      </div>
      <div class="flex justify-end">
        <button type="button" onclick="toggleUploadModal(false)"
                class="mr-2 px-4 py-2 border rounded">Cancel</button>
        <button type="submit"
                class="bg-gradient-to-r from-[#800000] to-[#b22222] text-white px-4 py-2 rounded hover:brightness-110 transition">
          Upload
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Drop-down & modal scripts (sama seperti sebelumnya) -->
@push('scripts')
<script>
  function toggleUploadModal(show){
    document.getElementById('uploadModal').classList.toggle('hidden', !show);
  }
  document.addEventListener('click', e => {
    const t = e.target.closest('.action-trigger');
    if (t){
      e.preventDefault(); e.stopPropagation();
      const m = t.nextElementSibling; 
      document.querySelectorAll('.action-menu').forEach(x=> x!==m && x.classList.add('hidden'));
      m.classList.toggle('hidden');
      t.setAttribute('aria-expanded', m.classList.contains('hidden') ? 'false' : 'true');
      return;
    }
    document.querySelectorAll('.action-menu').forEach(x=> x.classList.add('hidden'));
  });
</script>
@endpush
