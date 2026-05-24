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
            'name' => 'Teknologi',
            'slug' => 'teknologi',
            'description' => 'Artikel tentang perkembangan teknologi terkini',
            'status' => true,
        ]);

        CategoryBlog::create([
            'name' => 'Bisnis',
            'slug' => 'bisnis',
            'description' => 'Tips dan trik bisnis untuk pengusaha',
            'status' => true,
        ]);

        CategoryBlog::create([
            'name' => 'Lifestyle',
            'slug' => 'lifestyle',
            'description' => 'Gaya hidup dan kesehatan',
            'status' => true,
        ]);

        CategoryBlog::create([
            'name' => 'Pendidikan',
            'slug' => 'pendidikan',
            'description' => 'Artikel tentang dunia pendidikan',
            'status' => true,
        ]);
    }
}
