<div x-data="{ open: false }">
    <button @click="open = true" class="bg-red-600 text-white px-4 py-2 rounded">
        Upload File
    </button>

    <!-- Modal -->
    <div x-show="open" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-1/3">
            <h2 class="text-lg font-semibold mb-4">Upload File</h2>
            <form action="{{ route('dashboard.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Nama File</label>
                    <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Pilih File</label>
                    <input type="file" name="file" class="w-full" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Pilih Kategori</label>
                    <select name="category_id" class="w-full border rounded px-3 py-2" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Pilih Folder (Opsional)</label>
                    <select name="folder_id" class="w-full border rounded px-3 py-2">
                        <option value="">Tidak ada</option>
                        @foreach ($folders as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="button" @click="open = false" class="mr-2 px-4 py-2 border rounded">Batal</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
