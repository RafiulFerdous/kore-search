<?php

namespace Database\Seeders;

use App\Models\HeroSection;
use Illuminate\Database\Seeder;

class HeroSectionSeeder extends Seeder
{
    public function run(): void
    {
        HeroSection::create([
            'title' => 'Unlock Your Potential with <span class="highlight">KoreSearch</span>',
            'subtitle' => 'Explore expert-led courses in development, design, and technology. Build the skills employers are looking for — at your own pace.',
            'hero_image' => 'https://placehold.co/560x400/1F3864/ffffff?text=Learn+Online',
            'stats' => [
                ['count' => '540+', 'label' => 'Students'],
                ['count' => '20+', 'label' => 'Courses'],
                ['count' => '10+', 'label' => 'Instructors'],
            ],
            'is_active' => true,
        ]);
    }
}
