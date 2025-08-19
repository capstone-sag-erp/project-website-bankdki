<!-- Modal Edit File -->
<div id="editFileModal-{{ $file->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
        <h2 class="text-xl font-semibold text-gray-800 mb-5">Edit File</h2>
        <form action="{{ route('files.update', $file->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Judul File -->
            <div>
                <label for="title-{{ $file->id }}" class="block text-sm font-medium text-gray-700">Judul File</label>
                <input type="text" id="title-{{ $file->id }}" name="title" value="{{ $file->title }}"
                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
            </div>

            <!-- Folder Selection -->
            <div>
                <label for="folder-{{ $file->id }}" class="block text-sm font-medium text-gray-700">Folder</label>
                <select id="folder-{{ $file->id }}" name="folder_id"
                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">-- Tanpa Folder --</option>
                    @foreach ($folders as $folder)
                        <option value="{{ $folder->id }}" @if ($folder->id == $file->folder_id) selected @endif>
                            {{ $folder->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button"
                    onclick="document.getElementById('editFileModal-{{ $file->id }}').classList.add('hidden')"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
