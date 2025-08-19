<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    /**
     * Menyimpan folder baru di dalam kategori tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category      $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        Folder::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('category.show', $request->category_id)
                        ->with('success', 'Folder created successfully.');
    }

    /**
     * Mengubah nama folder.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder = Folder::findOrFail($id);
        $folder->name = $request->name;
        $folder->save();

        return redirect()->back()->with('success', 'Folder renamed successfully.');
    }

    /**
     * Menghapus folder berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();

        return back()->with('success', 'Folder deleted successfully.');
    }
}
