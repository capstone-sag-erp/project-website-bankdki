<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Folder;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('file.index', compact('categories'));
    }

    public function create()
    {
        $folders = Folder::all();
        $categories = Category::all();
        return view('file.create', compact('folders', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png',
            'category_id' => 'required|exists:categories,id',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $filePath = null;
        $sizeInBytes = null;

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $filePath = $uploaded->store('uploads', 'public');
            $sizeInBytes = $uploaded->getSize();
        }

        File::create([
            'title' => $request->title,
            'file_path' => $filePath,
            'category_id' => $request->category_id,
            'folder_id' => $request->folder_id,
            'user_id' => Auth::id(),
            'size' => $sizeInBytes,
        ]);

        return redirect()->back()->with('success', 'File berhasil diunggah.');
    }

    /**
     * Menampilkan halaman kategori beserta file dan folder.
     * (Method ini dinamai show tapi menerima category id)
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);
        $folders = Folder::where('category_id', $id)->get();
        $files = File::where('category_id', $id)->get();
        return view('category.show', compact('category', 'folders', 'files'));
    }

    public function edit(File $file)
    {
        $folders = Folder::where('category_id', $file->category_id)->get();
        return view('file.edit', compact('file', 'folders'));
    }

    public function update(Request $request, File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $file->title = $request->title;
        $file->folder_id = $request->folder_id;
        $file->save();

        return redirect()->back()->with('success', 'File berhasil diperbarui.');
    }

    public function destroy(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->back()->with('success', 'File berhasil dihapus.');
    }

    /**
     * View / open file di tab baru (serve langsung dari Laravel)
     */
    public function view(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$file->file_path || !Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fullPath = Storage::disk('public')->path($file->file_path);

        return response()->file($fullPath);
    }

    /**
     * Download file
     */
    public function download(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$file->file_path || !Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Ambil ekstensi dari file path yang disimpan
        $extension = pathinfo($file->file_path, PATHINFO_EXTENSION);

        // Siapkan nama download: judul + ekstensi (jika belum ada)
        $downloadName = $file->title;
        $lowerTitle = strtolower($downloadName);
        $expectedSuffix = '.' . strtolower($extension);
        if (!str_ends_with($lowerTitle, $expectedSuffix)) {
            $downloadName .= $expectedSuffix;
        }

        return Storage::disk('public')->download($file->file_path, $downloadName);
    }
}