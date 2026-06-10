<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GeneralSetting::create([
            'phone' => '+62-812-3456-7890',
            'email' => 'info@bisondenim.com',
            'instagram' => 'https://instagram.com/bisondenim',
            'tiktok' => 'https://tiktok.com/@bisondenim',
            'facebook' => 'https://facebook.com/bisondenim',
            'youtube' => 'https://youtube.com/@bisondenim',
            'pinterest' => 'https://pinterest.com/bisondenim',
            'location' => 'Jl. Merdeka No. 123, Jakarta, Indonesia',
        ]);

        $this->command->info('General Settings seeded successfully!');
    }
}

