<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil kategori pertama
        $category = Category::first(); 

        // Mengambil pengguna pertama
        $user = User::first();

        // Menambahkan file contoh untuk kategori dan pengguna tertentu
        File::create([
            'title' => 'Sample File 1',
            'file_path' => 'uploads/sample1.pdf',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'size' => 2.5 * 1024 * 1024, // 2.5 MB dalam byte
        ]);

        File::create([
            'title' => 'Sample File 2',
            'file_path' => 'uploads/sample2.pdf',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'size' => 3.0 * 1024 * 1024, // 3 MB dalam byte
        ]);
    }
}
