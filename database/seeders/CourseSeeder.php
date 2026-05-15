<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $instructorIds = User::whereHas('roles', fn($q) => $q->where('name', 'instructor'))->pluck('id');

        if ($instructorIds->isEmpty()) {
            $instructor = User::factory()->create(['role' => 'instructor']);
            $instructor->assignRole('instructor');
            $instructorIds = collect([$instructor->id]);
        }

        Course::withoutEvents(function () use ($instructorIds) {
            $existingSlugs = Course::pluck('slug')->toArray();
            $slug = '';
            $factory = Course::factory();

            for ($i = 0; $i < 200; $i++) {
                do {
                    $course = $factory->make();
                    $slug = $course->slug;
                } while (in_array($slug, $existingSlugs, true));

                $existingSlugs[] = $slug;
                $course->instructor_id = $instructorIds->random();
                $course->save();
            }
        });
    }
}
