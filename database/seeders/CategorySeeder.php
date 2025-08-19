<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'CMS', 'max_size' => 5 * 1024],
            ['name' => 'QRIS', 'max_size' => 5 * 1024],
            ['name' => 'JOBS', 'max_size' => 5 * 1024],
            ['name' => 'EDC', 'max_size' => 5 * 1024],
            ['name' => 'TMR', 'max_size' => 5 * 1024],
            ['name' => 'MONAS', 'max_size' => 5 * 1024],
            ['name' => 'TMII', 'max_size' => 5 * 1024],
        ];

        // Menambahkan kategori baru hanya jika tidak ada kategori dengan nama yang sama
        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
