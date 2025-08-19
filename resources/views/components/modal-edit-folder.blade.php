<!-- resources/views/components/modal-edit-folder.blade.php -->
<div id="newFolderModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Rename Folder</h2>
        <form method="POST" action="" id="editFolderForm">
            @csrf
            @method('PUT')
            <input type="text" name="name" id="editFolderName" class="w-full border border-gray-300 rounded px-3 py-2 mb-4" placeholder="Folder Name" required>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleNewFolderModal(false)" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Save</button>
            </div>
        </form>
    </div>
</div>
