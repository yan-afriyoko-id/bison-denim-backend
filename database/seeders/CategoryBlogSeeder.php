<?php

namespace Database\Seeders;

use App\Models\CategoryBlog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryBlog::create([
            'name' => 'Style Guide',
            'slug' => 'style-guide',
            'description' => 'Tips dan inspirasi mix & match outfit untuk berbagai kesempatan',
            'status' => true,
        ]);

        CategoryBlog::create([
            'name' => 'Trend Fashion',
            'slug' => 'trend-fashion',
            'description' => 'Update tren mode terkini dari runway hingga street style',
            'status' => true,
        ]);

        CategoryBlog::create([
            'name' => 'Tips Perawatan',
            'slug' => 'tips-perawatan',
            'description' => 'Cara merawat pakaian agar tetap awet dan tampak baru',
            'status' => true,
        ]);

        CategoryBlog::create([
            'name' => 'Koleksi Terbaru',
            'slug' => 'koleksi-terbaru',
            'description' => 'Informasi dan ulasan koleksi pakaian terbaru dari Bison Denim',
            'status' => true,
        ]);
    }
}
