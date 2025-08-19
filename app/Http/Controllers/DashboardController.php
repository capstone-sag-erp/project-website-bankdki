<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama dengan kategori,
     * recent files, dan storage usage.
     * Mendukung filter: search (title), category, date_from, date_to.
     */
    public function index(Request $request)
    {
        // 1. Ambil 7 kategori dengan hitungan file
        $categories = Category::withCount('files')->take(7)->get();

        // 2. Siapkan query untuk 5 file terbaru milik user
        $recentFilesQuery = File::with(['category', 'user', 'folder'])
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->filled('search')) {
            $recentFilesQuery->where('title', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('category_id')) {
            $recentFilesQuery->where('category_id', $request->category_id);
        }
        if ($request->filled('date_from')) {
            $recentFilesQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $recentFilesQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $recentFiles = $recentFilesQuery->take(5)->get();

        // 3. Hitung storage usage (bytes)
        $usedBytes  = File::where('user_id', Auth::id())->sum('size');
        $quotaBytes = 5 * 1024 * 1024 * 1024; // 5 GB

        // 4. Kirim ke view
        return view('dashboard.index', compact(
            'categories',
            'recentFiles',
            'usedBytes',
            'quotaBytes'
        ));
    }

    /**
     * Tampilkan halaman kategori: semua file & folder dalam kategori.
     */
    public function showCategory($id)
    {
        $category = Category::findOrFail($id);

        $files = File::where('category_id', $id)
                     ->orderBy('created_at', 'desc')
                     ->get();

        $folders = Folder::where('category_id', $id)->get();

        return view('category.show', compact('category', 'files', 'folders'));
    }

    /**
     * Upload file baru ke kategori dan (opsional) ke folder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,csv,txt,jpg,png,ppt,pptx,zip|max:5120',
            'folder_id'   => 'nullable|exists:folders,id',
        ]);

        // Simpan fisik ke storage
        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('uploads', 'public');

        // Simpan metadata ke DB
        File::create([
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
            'folder_id'   => $request->folder_id,
            'title'       => $request->title,
            'file_path'   => $path,
            'size'        => $uploadedFile->getSize(), // bytes
        ]);

        return redirect()->back()->with('success', 'File berhasil diunggah.');
    }

    /**
     * Update nama kategori.
     */
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->save();

        return redirect()
            ->route('category.show', $id)
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Tampilkan isi file dari folder tertentu.
     *
     * @param  int  $categoryId  ID kategori induk (digunakan validasi)
     * @param  int  $folderId    ID folder yang dibuka
     */
    public function showFolder(Category $category, Folder $folder)
    {
        // Validasi: folder milik kategori ini
        abort_if($folder->category_id !== $category->id, 404);

        // Untuk dropdown modal edit file
        $folders = $category->folders()
                            ->where('user_id', Auth::id())
                            ->get();

        // File hanya di folder ini
        $files   = $folder->files()
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at','desc')
                        ->get();

        // **GANTI** nama view di bawah menjadi 'folders.show'
        return view('folders.show', compact('category','folder','folders','files'));
    }
}
