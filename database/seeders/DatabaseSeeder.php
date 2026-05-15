<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $admin = User::create([
            'name'      => 'Admin User',
            'email'     => 'admin@koresearch.com',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'headline'  => 'Platform Administrator',
            'bio'       => 'Managing the KoreSearch platform.',
            'location'  => 'Dhaka, Bangladesh',
        ]);
        $admin->assignRole('admin');

        $instructor = User::create([
            'name'      => 'Ataur Rahman Sakib',
            'email'     => 'instructor@koresearch.com',
            'password'  => Hash::make('password'),
            'role'      => 'instructor',
            'headline'  => 'Senior Web Developer & Instructor',
            'bio'       => 'Full-stack developer with 8+ years of experience. Teaching Laravel, React and Vue on KoreSearch.',
            'location'  => 'Dhaka, Bangladesh',
        ]);
        $instructor->assignRole('instructor');

        $student = User::create([
            'name'      => 'Student User',
            'email'     => 'student@koresearch.com',
            'password'  => Hash::make('password'),
            'role'      => 'student',
            'headline'  => 'Aspiring Developer',
            'bio'       => 'Learning web development on KoreSearch.',
            'location'  => 'Chittagong, Bangladesh',
        ]);
        $student->assignRole('student');

        $courses = [
            [
                'instructor_id' => $instructor->id,
                'title'         => 'Complete Laravel 10 for Beginners',
                'slug'          => 'complete-laravel-10-beginners',
                'description'   => 'Learn Laravel 10 from scratch. Build real-world applications with PHP and the most popular MVC framework.',
                'thumbnail'     => 'https://placehold.co/800x450',
                'category'      => 'Backend',
                'level'         => 'beginner',
                'price'         => 0,
                'is_published'  => true,
                'enrolled_count'=> 142,
                'rating'        => 4.7,
                'duration'      => '12 hours',
                'topics'        => [
                    'Introduction to Laravel and MVC',
                    'Routing and Controllers',
                    'Blade Templating Engine',
                    'Eloquent ORM and Migrations',
                    'Authentication and Authorization',
                    'Building REST APIs with Laravel',
                ],
                'created_at'    => now()->subMonths(3),
                'updated_at'    => now()->subMonths(3),
            ],
            [
                'instructor_id' => $instructor->id,
                'title'         => 'Vue.js 3 Complete Guide',
                'slug'          => 'vuejs-3-complete-guide',
                'description'   => 'Master Vue.js 3 with Composition API, Pinia, and Vue Router. Build reactive, component-based applications.',
                'thumbnail'     => 'https://placehold.co/800x450',
                'category'      => 'Frontend',
                'level'         => 'intermediate',
                'price'         => 1500,
                'is_published'  => true,
                'enrolled_count'=> 89,
                'rating'        => 4.5,
                'duration'      => '10 hours',
                'topics'        => [
                    'Vue.js Fundamentals and Setup',
                    'Component Architecture',
                    'Composition API Deep Dive',
                    'State Management with Pinia',
                    'Vue Router for SPAs',
                ],
                'created_at'    => now()->subMonths(2),
                'updated_at'    => now()->subMonths(2),
            ],
            [
                'instructor_id' => $instructor->id,
                'title'         => 'MySQL Database Design Mastery',
                'slug'          => 'mysql-database-design-mastery',
                'description'   => 'Learn database design principles, normalization, indexing, and query optimization with MySQL 8.',
                'thumbnail'     => 'https://placehold.co/800x450',
                'category'      => 'Database',
                'level'         => 'intermediate',
                'price'         => 1200,
                'is_published'  => true,
                'enrolled_count'=> 67,
                'rating'        => 4.3,
                'duration'      => '8 hours',
                'topics'        => [
                    'Relational Database Concepts',
                    'Schema Design and Normalization',
                    'Indexes and Query Optimization',
                    'Stored Procedures and Triggers',
                    'Backup and Recovery Strategies',
                ],
                'created_at'    => now()->subMonths(2),
                'updated_at'    => now()->subMonths(2),
            ],
            [
                'instructor_id' => $instructor->id,
                'title'         => 'React.js for Modern Web Development',
                'slug'          => 'reactjs-modern-web-development',
                'description'   => 'Build powerful SPAs with React 18, hooks, context API, and modern tooling.',
                'thumbnail'     => 'https://placehold.co/800x450',
                'category'      => 'Frontend',
                'level'         => 'beginner',
                'price'         => 0,
                'is_published'  => true,
                'enrolled_count'=> 210,
                'rating'        => 4.8,
                'duration'      => '14 hours',
                'topics'        => [
                    'JSX and Component Basics',
                    'Props, State and Lifecycle',
                    'React Hooks In Depth',
                    'Context API and Global State',
                    'Fetching Data and Side Effects',
                ],
                'created_at'    => now()->subMonths(4),
                'updated_at'    => now()->subMonths(4),
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        $firstCourse = Course::first();

        Order::create([
            'user_id'            => $student->id,
            'course_id'          => $firstCourse->id,
            'transaction_number' => '8NK2031ABC',
            'amount'             => $firstCourse->price,
            'status'             => 'completed',
            'created_at'         => now()->subWeeks(2),
            'updated_at'         => now()->subWeeks(2),
        ]);
    }
}
