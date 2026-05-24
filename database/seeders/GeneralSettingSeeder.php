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
            'email' => 'info@karsindo.com',
            'instagram' => 'https://instagram.com/karsindo_official',
            'tiktok' => 'https://tiktok.com/@karsindo_official',
            'facebook' => 'https://facebook.com/karsindo.official',
            'youtube' => 'https://youtube.com/@karsindo_official',
            'pinterest' => 'https://pinterest.com/karsindo_official',
            'location' => 'Jl. Merdeka No. 123, Jakarta, Indonesia',
        ]);

        $this->command->info('General Settings seeded successfully!');
    }
}

